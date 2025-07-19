<?php
namespace WPBackendDash\Models;
/**
 * Clase para manejar los metadatos del usuario relacionados con AI.
 */
class AIUserMetaManager {
    private $user_id;
    private $fields = [
        'mepr_ai_tokens',
        'mepr_ai_hours',
        'mepr_rooms',
        'mepr_ai_plan'
    ];

    public function __construct($user_id) {
        $this->user_id = $user_id;
    }

    // Obtener un valor específico
    public function get($key) {
        if (!in_array($key, $this->fields)) return null;
        return get_user_meta($this->user_id, $key, true);
    }

    // Establecer un valor específico
    public function set($key, $value) {
        if (!in_array($key, $this->fields)) return false;
        return update_user_meta($this->user_id, $key, $value);
    }

    // Sumar a un valor
    public function add($key, $amount) {
        if (!in_array($key, $this->fields) || !is_numeric($amount)) return false;
        $actual = floatval($this->get($key));
        return $this->set($key, $actual + $amount);
    }

    // Restar a un valor (opcional)
    public function subtract($key, $amount) {
        if (!in_array($key, $this->fields) || !is_numeric($amount)) return false;
        $actual = floatval($this->get($key));
        return $this->set($key, max(0, $actual - $amount));
    }

    // Obtener todos los valores disponibles
    public function get_all() {
        $result = [];
        foreach ($this->fields as $field) {
            $result[$field] = $this->get($field);
        }
        return $result;
    }

    // Ejemplo específico: sumar tokens
    public function add_tokens($amount) {
        return $this->add('mepr_ai_tokens', $amount);
    }

    // Ejemplo específico: restar horas
    public function subtract_hours($amount) {
        return $this->subtract('mepr_ai_hours', $amount);
    }
	
	public function use_time($amount) {
        if (!is_numeric($amount) || $amount <= 0) return false;

        $actual = floatval($this->get('mepr_ai_hours'));
        if ($actual < $amount) {
            // No hay suficiente tiempo
            return false;
        }

        $nuevo = $actual - $amount;
        $this->set('mepr_ai_hours', $nuevo);

        return $nuevo; // Tiempo restante
    }
}
