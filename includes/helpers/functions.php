<?php

use WPBackendDash\Helpers\WBERoute;

/**
 * Genera una URL para una ruta nombrada.
 *
 * @param string $name   El nombre de la ruta registrada en WBERoute.
 * @param array  $params Un array asociativo de parámetros para la URL (ej: ['id' => 123]).
 * @return string La URL completa y generada.
 */
function wberoute(string $name, array $params = []): string
{
    // 1. Obtener la información de la ruta por su nombre
    $route = WBERoute::getRoute($name);

    // Si la ruta no existe, devuelve un enlace roto para evitar errores fatales.
    if (!$route) {
        // Opcional: puedes registrar un aviso para el desarrollador.
        trigger_error("La ruta nombrada '{$name}' no existe.", E_USER_WARNING);
        return '#';
    }

    // 2. Tomar la URL "pretty" como plantilla
    $url = $route['pretty'];

    // 3. Reemplazar los placeholders con los parámetros proporcionados
    foreach ($params as $key => $value) {
        $url = str_replace('{' . $key . '}', $value, $url);
    }

    // 4. (Opcional) Limpiar placeholders que no fueron reemplazados
    // Esto evita que la URL final contenga algo como "/orders/{id}/edit" si no se pasó el id.
    $url = preg_replace('/\/{\w+}/', '', $url);

    // 5. Construir la URL base (muy importante en entornos como WordPress)
    // Reemplaza esto con la función adecuada de tu framework o CMS.
    // Para WordPress, usarías home_url().
    $baseUrl = rtrim(home_url(), '/'); 
    
    return $baseUrl . '/' . ltrim($url, '/');
}

function aplicar_paquete_ai($user_id, $paquete_slug) {
    $paquetes = [
        'basic' => [
            'mepr_ai_tokens' => 2000,
            'mepr_rooms' => 3,
            'mepr_ai_hours' => 2,
            'mepr_ai_plan' => 'basic',
        ],
        'pro' => [
            'mepr_ai_tokens' => 10000,
            'mepr_rooms' => 5,
            'mepr_ai_hours' => 10,
            'mepr_ai_plan' => 'pro',
        ],
        'premium' => [
            'mepr_ai_tokens' => 50000,
            'mepr_rooms' => 10,
            'mepr_ai_hours' => 25,
            'mepr_ai_plan' => 'premium',
        ],
    ];

    if (!isset($paquetes[$paquete_slug])) return;

    foreach ($paquetes[$paquete_slug] as $key => $valor) {
        $actual = get_user_meta($user_id, $key, true);

        if (is_numeric($valor)) {
            $nuevo = floatval($actual) + $valor;
        } else {
            $nuevo = $valor;
        }

        update_user_meta($user_id, $key, $nuevo);
    }
}


// Agrega este código al archivo functions.php de tu tema o en un plugin personalizado
function shortcode_mepr_ai_tokens() {
    // Verifica si el usuario está logueado
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $tokens = get_user_meta($user_id, 'mepr_ai_tokens', true);

        // Si no hay valor, devuelve 0 por defecto
        if ($tokens === '') {
            $tokens = 7200;
        }

        return $tokens;
    } else {
        return 0;
    }
}
add_shortcode('mepr_ai_tokens', 'shortcode_mepr_ai_tokens');