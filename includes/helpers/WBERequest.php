<?php
namespace WPBackendDash\Helpers;

use WPBackendDash\Helpers\WBEResponseBuilder;

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

    public static function file($filename)
    {
        if (isset($_FILES[$filename])) {
            return $_FILES[$filename];
        }
        return null;
    }

    public static function has($key)
    {
        $type = self::detectType();

        switch ($type) {
            case 'json':
                $body = self::json();
                return isset($body[$key]);
            case 'post':
                return isset($_POST[$key]);
            case 'get':
            case 'ajax':
            default:
                return isset($_REQUEST[$key]);
        }
    }

    public function hasFile($filename)
    {
        return isset($_FILES[$filename]) && !empty($_FILES[$filename]['name']);
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
        return new WBEResponseBuilder();
    }

    public static function request()
    {
        return new self();
    }

    public static function requiredFields($modelOrArray)
    {
        $requiredFields = [];
        if (is_array($modelOrArray)) {
            $requiredFields = $modelOrArray;
        } elseif (method_exists($modelOrArray, 'getRequired')) {
            $requiredFields = $modelOrArray->getRequired();
        }

        $all = self::all();
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($all[$field]) || empty($all[$field])) {
                $missingFields[] = $field;
            }
        }

        return $missingFields;
    }

    public static function hasRequiredFields($modelOrArray)
    {
        $missingFields = self::requiredFields($modelOrArray);
        return empty($missingFields);
    }

    public static function validate($modelOrArray)
    {
        $missingFields = self::requiredFields($modelOrArray);
        if (!empty($missingFields)) {
            $response = self::Response();

            foreach ($missingFields as $field) {
                $response->addAction("wbeShowNotify", ["Error!", "Required Field: " . $field, "error"]);
            }
            return $response->wpjson();
        }

        return true;
    }


}
