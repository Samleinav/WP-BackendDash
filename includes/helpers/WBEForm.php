<?php
 namespace WPBackendDash\Helpers;
 
Class WBEForm {
    /**
     * Bootstrap the WBEForm.
     */
    public static function bootstrap() {
        add_action('admin_enqueue_scripts', function ($hook) {

            // Bootstrap 5 CSS desde CDN
            wp_enqueue_style('bootstrap5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

            // Bootstrap 5 JS (opcional)
            wp_enqueue_script('bootstrap5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], null, true);

        });
    }
}
