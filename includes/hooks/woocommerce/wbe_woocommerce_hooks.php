<?php
/**
 * Modifica la página de perfil (/wp-admin/profile.php) para un rol de usuario específico,
 * mostrando los campos de WooCommerce en lugar de los campos de WordPress por defecto.
 *
 * VERSIÓN CORREGIDA Y ACTUALIZADA.
 */
add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
// --- Define aquí el rol de usuario que quieres modificar ---
define('ROL_PARA_MODIFICAR_PERFIL', 'client'); // Cambia 'customer' por el rol que necesites.

// 1. Hook para añadir nuestro CSS y JS personalizados a la página de perfil.
add_action('admin_enqueue_scripts', 'ocultar_campos_perfil_por_defecto_v2');
function ocultar_campos_perfil_por_defecto_v2($hook) {
    if ('profile.php' !== $hook) {
        return;
    }

    $user = wp_get_current_user();
    if (in_array(ROL_PARA_MODIFICAR_PERFIL, $user->roles)) {
        wp_add_inline_style('wp-admin', "
            /* Ocultar secciones de perfil de WordPress */
            body.profile-php h2,
            body.profile-php form > table:not(.wc-customer-fields),
            body.profile-php p.submit,
            body.profile-php #application-passwords-section {
                display: none !important;
            }

            /* Mostrar solo títulos y botón de guardado de WooCommerce */
            body.profile-php h2.wc-customer-fields-title,
            body.profile-php p.wc-profile-submit {
                display: block !important;
            }
        ");
    }
}

// 2. Hook para mostrar los campos de WooCommerce en la página de perfil.
add_action('show_user_profile', 'mostrar_campos_woocommerce_en_perfil_v2');
function mostrar_campos_woocommerce_en_perfil_v2($profileuser) {
    $current_user = wp_get_current_user();
    if (!in_array(ROL_PARA_MODIFICAR_PERFIL, $current_user->roles)) {
        return;
    }

    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Obtenemos los campos de facturación y envío usando el método moderno y correcto.
    $billing_fields = WC()->checkout()->get_checkout_fields('billing');
    $shipping_fields = WC()->checkout()->get_checkout_fields('shipping');

    // Mostramos los campos de facturación
    echo '<h2 class="wc-customer-fields-title">Dirección de facturación</h2>';
    echo '<table class="form-table wc-customer-fields" id="fieldset-billing">';
    foreach ($billing_fields as $key => $field) {
        // Añadimos el prefijo 'billing_' si no lo tiene, para que coincida con el user_meta
        if (strpos($key, 'billing_') !== 0) {
            $key = 'billing_' . $key;
        }
        woocommerce_form_field($key, $field, get_user_meta($profileuser->ID, $key, true));
    }
    echo '</table>';

    // Mostramos los campos de envío
    echo '<h2 class="wc-customer-fields-title">Dirección de envío</h2>';
    echo '<table class="form-table wc-customer-fields" id="fieldset-shipping">';
    foreach ($shipping_fields as $key => $field) {
        // Añadimos el prefijo 'shipping_' si no lo tiene
        if (strpos($key, 'shipping_') !== 0) {
            $key = 'shipping_' . $key;
        }
        woocommerce_form_field($key, $field, get_user_meta($profileuser->ID, $key, true));
    }
    echo '</table>';
    
    // Añadimos nuestro propio botón de guardar para que sea visible
    echo '<p class="submit wc-profile-submit">';
    echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Actualizar perfil">';
    echo '</p>';
}

// 3. Hook para guardar los datos de WooCommerce cuando se actualiza el perfil.
add_action('personal_options_update', 'guardar_campos_woocommerce_en_perfil_v2');
function guardar_campos_woocommerce_en_perfil_v2($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    // No es necesario comprobar el rol aquí, ya que el hook se encarga de los permisos.
    if (!class_exists('WooCommerce')) {
        return;
    }

    $billing_fields = WC()->checkout()->get_checkout_fields('billing');
    $shipping_fields = WC()->checkout()->get_checkout_fields('shipping');
    
    // Unimos ambos arrays de campos para procesarlos
    $all_fields = array_merge($billing_fields, $shipping_fields);

    foreach (array_keys($all_fields) as $key) {
        if (isset($_POST[$key])) {
            update_user_meta($user_id, $key, wc_clean(wp_unslash($_POST[$key])));
        }
    }
}
