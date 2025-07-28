<?php
namespace WPBackendDash\Helpers;

use WPBackendDash\Helpers\ActionTodoBuilder;

/**
 * usage
 * 1. get
 * $name = WBERequest::get('name'); 
 * 2.  query vars
 * $post_id = WBERequest::queryVar('p');
 * 3. response JSON
 * return WBERequest::Response()->json(['ok' => true]);
 * 4. Response wp_send_json_success
 * return WBERequest::Response()->wpjson(['message' => 'Todo bien'], true);
 * 5. redirection
 * return WBERequest::Response()->redirect(admin_url('admin.php?page=mi_pagina'));
 */
class WBERequest
{
    protected static $detectedType = null;
    protected static $parsedJson = null;


    /**
     * Detecta el tipo de request: json, post, get, ajax
     */
    public static function detectType()
    {
        if (self::$detectedType !== null) return self::$detectedType;

        if (
            isset($_SERVER['CONTENT_TYPE']) &&
            str_contains($_SERVER['CONTENT_TYPE'], 'application/json')
        ) {
            self::$detectedType = 'json';
        } elseif (self::isAjax()) {
            self::$detectedType = 'ajax';
        } elseif (self::isPost()) {
            self::$detectedType = 'post';
        } else {
            self::$detectedType = 'get';
        }

        return self::$detectedType;
    }

    /**
     * Get All request data
     */
    public static function all()
    {
        $type = self::detectType();

        switch ($type) {
            case 'json':
                return self::json();
            case 'post':
                return $_POST;
            case 'get':
            case 'ajax':
            default:
                return $_REQUEST;
        }
    }

    /**
     * Obtener dato del request según el tipo detectado
     */
    public static function get($key = null, $default = null)
    {
        $type = self::detectType();

        switch ($type) {
            case 'json':
                $body = self::json();
                return $key ? ($body[$key] ?? $default) : $body;
            case 'post':
                return $key ? ($_POST[$key] ?? $default) : $_POST;
            case 'get':
            case 'ajax':
            default:
                return $key ? ($_REQUEST[$key] ?? $default) : $_REQUEST;
        }
    }

    /**
     * Obtener body JSON ya parseado (con cache interno)
     */
    public static function json()
    {
        if (self::$parsedJson !== null) return self::$parsedJson;

        $raw = file_get_contents('php://input');
        self::$parsedJson = json_decode($raw, true) ?? [];

        return self::$parsedJson;
    }

    /**
     * Saber si es AJAX
     */
    public static function isAjax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public static function headers()
    {
        return function_exists('getallheaders') ? getallheaders() : [];
    }

    public static function fullUrl()
    {
        $protocol = is_ssl() ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public static function queryVar($key, $default = null)
    {
        global $wp_query;
        return $wp_query->query_vars[$key] ?? $default;
    }

    public static function allQueryVars()
    {
        global $wp_query;
        return $wp_query->query_vars ?? [];
    }

    /**
     * Subclase para manejar las respuestas
     */
    public static function Response()
    {
        return new class {

            private ActionTodoBuilder $actionTodoBuilder;

            private function __construct()
            {
                $this->actionTodoBuilder = new ActionTodoBuilder();
            }

            public function addAction(string $methodRef, array|string|int|float|null $params = [])
            {
                $this->actionTodoBuilder->add($methodRef, $params);
                return $this;
            }

            public function actions(array $actions)
            {
                foreach ($actions as $action) {
                    if (is_array($action) && isset($action[0])) {
                        $this->addAction($action[0], $action[1] ?? []);
                    } elseif (is_string($action)) {
                        $this->addAction($action);
                    }
                }
                return $this;
            }

            protected function prepareResponse($data = [])
            {
                $response = $this->actionTodoBuilder->response($data);

                return $response;
            }

            public function json($data = [], $code = 200)
            {
                wp_send_json($this->prepareResponse($data), $code);
            }

            public function send($data = [], $code = 200)
            {
                http_response_code($code);
                header('Content-Type: application/json');
                echo json_encode($this->prepareResponse($data));
                exit;
            }

            public function wpjson($data = [], $success = true)
            {
                if ($success) {
                    wp_send_json_success($this->prepareResponse($data));
                } else {
                    wp_send_json_error($this->prepareResponse($data));
                }
            }

            public function redirect($url, $status = 302)
            {
                wp_redirect($url, $status);
                exit;
            }

            public function text($string, $status = 200)
            {
                http_response_code($status);
                header('Content-Type: text/plain');
                echo $string;
                exit;
            }
        };
    }

    


}
