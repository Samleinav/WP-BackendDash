<?php
defined('ABSPATH') || exit;

class WPBackendDashInstaller {
    public static function install() {
        global $wpdb;

        $tabla = $wpdb->prefix . 'ai_interviews';

        // Verifica si la tabla ya existe
        $existe = $wpdb->get_var(
            $wpdb->prepare(
                "SHOW TABLES LIKE %s",
                $tabla
            )
        );

        if ($existe === $tabla) {
            // Ya existe, no hacer nada
            return;
        }
        
        $charset = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE $tabla (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            meeting_link TEXT,
            type VARCHAR(100),
            details TEXT,
            attachments TEXT,
            time FLOAT DEFAULT 0,
            tokens INT DEFAULT 0,
            interview_complete TINYINT(1) DEFAULT 0,
            in_use TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY in_use (in_use)
        ) $charset;";

        dbDelta($sql);
    }
}
