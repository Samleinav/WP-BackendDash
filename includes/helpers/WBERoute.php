<?php

namespace WPBackendDash\Helpers;

class WBERoute {
    protected static $routes = [];
    protected static $sectionStart = '# BEGIN WBE CustomRoutes';
    protected static $sectionEnd   = '# END WBE CustomRoutes';
    protected static $htaccessPath;

    /**
     * Establece la ruta del archivo .htaccess si se necesita
     */
    protected static function getHtaccessPath() {
        return self::$htaccessPath ?? ABSPATH . '.htaccess';
    }

    /**
     * Agrega una ruta si no existe ya.
     */
    public static function route($regex, $redirect, $pretty = null, $flags = 'QSA,NC,L') {
        $normalized = [
            'regex' => trim($regex),
            'redirect' => trim($redirect),
            'pretty' => $pretty,
            'flags' => self::normalizeFlags($flags),
        ];

        // Verifica si ya fue registrada (ignora el orden de flags)
        foreach (self::$routes as $route) {
            if (
                $route['regex'] === $normalized['regex'] &&
                $route['redirect'] === $normalized['redirect'] &&
                self::normalizeFlags($route['flags']) === $normalized['flags']
            ) {
                return; // Ya existe, no agregar
            }
        }

        self::$routes[] = $normalized;
    }
    /**
     * Obtiene todas las rutas registradas
     */
    public static function getRoutes() {
        return self::$routes;
    }

    public static function matchCurrentRoute()
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        foreach (self::$routes as $route) {
            $pattern = '#^' . $route['regex'] . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // quitamos el full match

                // Extraer variables de `pretty`
                preg_match_all('/{([^}]+)}/', $route['pretty'], $varMatches);
                $varNames = $varMatches[1] ?? [];

                $named = [];
                foreach ($varNames as $i => $var) {
                    $named[$var] = $matches[$i] ?? null;
                }

                return [
                    'route' => $route,
                    'params' => $named, 
                    'matches' => $matches,
                ];
            }
        }

        return null;
    }

    /**
     * Normaliza los flags para comparación (orden alfabético)
     */
    protected static function normalizeFlags($flags) {
        $parts = array_map('trim', explode(',', strtoupper($flags)));
        sort($parts);
        return implode(',', $parts);
    }

    /**
     * Genera el contenido del bloque para .htaccess
     */
    protected static function buildHtaccessBlock() {
        $lines = [
            self::$sectionStart,
            '<IfModule mod_rewrite.c>',
            'RewriteEngine On',
        ];

        foreach (self::$routes as $route) {
            $lines[] = 'RewriteRule ' . $route['regex'] . ' ' . $route['redirect'] . ' [' . $route['flags'] . ']';
        }

        $lines[] = '</IfModule>';
        $lines[] = self::$sectionEnd;

        return implode("\n", $lines);
    }

    /**
     * Aplica los cambios solo si hay diferencias
     */
    public static function applyChangesIfNeeded() {
        $newBlock = self::buildHtaccessBlock();
        $path = self::getHtaccessPath();

        if (!file_exists($path)) {
            file_put_contents($path, $newBlock, LOCK_EX);
            return true;
        }

        $content = file_get_contents($path);
        $pattern = "/".preg_quote(self::$sectionStart, '/').".*?".preg_quote(self::$sectionEnd, '/')."/s";

        if (preg_match($pattern, $content, $matches)) {
            if (trim($matches[0]) === trim($newBlock)) {
                return false; // Sin cambios
            }

            $updated = preg_replace($pattern, $newBlock, $content);
            file_put_contents($path, $updated, LOCK_EX);
            return true;
        }

        // Si no existe la sección, se agrega al final
        $content .= "\n\n" . $newBlock;
        file_put_contents($path, $content, LOCK_EX);
        return true;
    }
}
