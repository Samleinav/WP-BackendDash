<?php
// src/helpers/ResponseBuilder.php
namespace WPBackendDash\Helpers;

use WPBackendDash\Helpers\ActionTodoBuilder;

class WBEResponseBuilder {

    private ActionTodoBuilder $actionTodoBuilder;

    public function __construct() {
        $this->actionTodoBuilder = new ActionTodoBuilder();
    }

    public function addAction(string $methodRef, array|string|int|float|null $params = []) {
        $this->actionTodoBuilder->add($methodRef, $params);
        return $this;
    }

    public function actions(array $actions) {
        foreach ($actions as $action) {
            if (is_array($action) && isset($action[0])) {
                $this->addAction($action[0], $action[1] ?? []);
            } elseif (is_string($action)) {
                $this->addAction($action);
            }
        }
        return $this;
    }

    protected function prepareResponse($data = []) {
        return $this->actionTodoBuilder->response($data);
    }

    public function json($data = [], $code = 200) {
        wp_send_json($this->prepareResponse($data), $code);
    }

    public function send($data = [], $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($this->prepareResponse($data));
        exit;
    }

    public function wpjson($data = [], $success = true) {
        if ($success) {
            wp_send_json_success($this->prepareResponse($data));
        } else {
            wp_send_json_error($this->prepareResponse($data));
        }
    }

    public function redirect($url, $status = 302) {
        wp_redirect($url, $status);
        exit;
    }

    public function text($string, $status = 200) {
        http_response_code($status);
        header('Content-Type: text/plain');
        echo $string;
        exit;
    }
}
