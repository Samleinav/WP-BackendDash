<?php
namespace WPBackendDash\Helpers;

class WBEAssets {
    protected static $assets = [];

    /**
     * Agrega un JS directamente.
     */
    public static function add_js($handle, $src, $deps = [], $ver = false, $in_footer = true, $args = []) {
        return self::add($handle, 'js', $src, $deps, $ver, $in_footer, $args);
    }

    /**
     * Agrega un CSS directamente.
     */
    public static function add_css($handle, $src, $deps = [], $ver = false, $args = []) {
        return self::add($handle, 'css', $src, $deps, $ver, false, $args);
    }

    /**
     * Método base para registrar un asset.
     */
    public static function add($handle, $type = 'js', $src = '', $deps = [], $ver = false, $in_footer = true, $args = []) {
        self::$assets[$handle] = [
            'handle'     => $handle,
            'type'       => $type, // 'js' o 'css'
            'src'        => $src,
            'deps'       => $deps,
            'ver'        => $ver,
            'in_footer'  => $in_footer,
            'condition'  => $args['condition'] ?? null, // callable o ['role' => [], 'cap' => []]
            'admin_only' => $args['admin_only'] ?? false,
            'hook'       => $args['hook'] ?? null, // para admin_enqueue_scripts en una página específica
        ];
    }

    public static function init() {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_admin']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_front']);
    }

    public static function enqueue_admin($hook_suffix) {
        self::enqueue(true, $hook_suffix);
    }

    public static function enqueue_front() {
        self::enqueue(false);
    }

    protected static function enqueue($is_admin = false, $hook_suffix = null) {
        foreach (self::$assets as $asset) {
            if ($asset['admin_only'] && !$is_admin) {
                continue;
            }

            if (!$asset['admin_only'] && $is_admin && $asset['hook'] && $asset['hook'] !== $hook_suffix) {
                continue;
            }

            if (!self::check_condition($asset['condition'])) {
                continue;
            }

            if ($asset['type'] === 'js') {
                wp_enqueue_script(
                    $asset['handle'],
                    $asset['src'],
                    $asset['deps'],
                    $asset['ver'],
                    $asset['in_footer']
                );
            } elseif ($asset['type'] === 'css') {
                wp_enqueue_style(
                    $asset['handle'],
                    $asset['src'],
                    $asset['deps'],
                    $asset['ver']
                );
            }
        }
    }

    protected static function check_condition($condition) {
        if (is_null($condition)) return true;

        if (is_callable($condition)) {
            return call_user_func($condition);
        }

        if (is_array($condition)) {
            if (!empty($condition['role'])) {
                $user = wp_get_current_user();
                if (!array_intersect($condition['role'], (array) $user->roles)) {
                    return false;
                }
            }

            if (!empty($condition['cap'])) {
                foreach ($condition['cap'] as $cap) {
                    if (!current_user_can($cap)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
