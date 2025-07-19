<?php
namespace WPBackendDash\Helpers;
// Incluir este código en tu plugin principal WPBackendDash.php o en un archivo incluido por él

class WBEUpdater {
    private $plugin_file;
    private $plugin_slug;
    private $github_raw_url = 'https://raw.githubusercontent.com/Samleinav/WP-BackendDash/main/WPBackendDash.php';

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($plugin_file);

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_api_data'], 10, 3);
    }

    public function get_remote_version() {
        $response = wp_remote_get($this->github_raw_url);
        if (is_wp_error($response)) return false;

        $body = wp_remote_retrieve_body($response);

        if (preg_match('/^.*Version:\s*(.*)$/mi', $body, $matches)) {
            return trim($matches[1]);
        }

        return false;
    }

    public function check_for_update($transient) {
        if (empty($transient->checked)) return $transient;

        $current_version = get_plugin_data($this->plugin_file)['Version'];
        $remote_version = $this->get_remote_version();

        if (version_compare($remote_version, $current_version, '>')) {
            $plugin_info = [
                'slug' => dirname($this->plugin_slug),
                'new_version' => $remote_version,
                'url' => 'https://github.com/Samleinav/WP-BackendDash',
                'package' => 'https://github.com/Samleinav/WP-BackendDash/archive/refs/heads/main.zip',
            ];

            $transient->response[$this->plugin_slug] = (object) $plugin_info;
        }

        return $transient;
    }

    public function plugin_api_data($result, $action, $args) {
        if ($action !== 'plugin_information') return false;

        if (isset($args->slug) && $args->slug === dirname($this->plugin_slug)) {
            $remote_version = $this->get_remote_version();

            return (object)[
                'name' => 'WPBackendDash',
                'slug' => $this->plugin_slug,
                'version' => $remote_version,
                'author' => '<a href="https://github.com/Samleinav">Samleinav</a>',
                'homepage' => 'https://github.com/Samleinav/WP-BackendDash',
                'download_link' => 'https://github.com/Samleinav/WP-BackendDash/archive/refs/heads/main.zip',
                'sections' => [
                    'description' => 'Actualización automática desde GitHub.',
                ],
            ];
        }

        return $result;
    }

    public static function init($plugin_file) {
        add_action('init', function () use ($plugin_file) {
            $updater = new self($plugin_file);
        });
    }
}
