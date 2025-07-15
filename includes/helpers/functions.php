<?php

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