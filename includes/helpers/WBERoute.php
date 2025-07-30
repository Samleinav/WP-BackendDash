<?php

namespace WPBackendDash\Helpers;

class WBERoute {
    protected static $routes = [];
    protected static $routesNamed = [];
    protected static $sectionStart = '# BEGIN WBE CustomRoutes';
    protected static $sectionEnd   = '# END WBE CustomRoutes';
    protected static $htaccessPath;
    protected static $paramCounter = 0;
    /**
     * Establece la ruta del archivo .htaccess si se necesita
     */
    protected static function getHtaccessPath() {
        return self::$htaccessPath ?? ABSPATH . '.htaccess';
    }

    /**
     * Agrega una ruta si no existe ya.
     */
    public static function route(string $name, string $route, string $redirect, array $options = [])
    { 
        
        $pretty = null;
        $regex = $route;

        // CAMBIO: Capturamos la opción de prefijo
        $usePrefix = $options['prefix'] ?? true;

        if (strpos($route, '{') !== false) {
            $pretty = $route;
            // CAMBIO: Pasamos la opción de prefijo al conversor
            list($regex, $redirect) = self::convertPrettyToRegex($pretty, $redirect, $usePrefix);
        } else {
            // CAMBIO: También aplicamos el prefijo a las rutas sin placeholders
            $regex = '^' . ($usePrefix ? '([^/]+/)?' : '') . trim($route, '/') . '/?$';
        }
        
        $normalized = [
            'name'     => $name,
            'regex'    => $regex,
            'redirect' => $redirect,
            'pretty'   => $pretty ?? $route,
            'flags'    => self::normalizeFlags($options['flags'] ?? 'QSA,NC,L'),
        ];

        // Verifica si ya fue registrada (ignora el orden de flags)
        foreach (self::$routes as $route) {
            if (
                $route['name'] === $name &&
                $route['regex'] === $normalized['regex'] &&
                $route['redirect'] === $normalized['redirect'] &&
                self::normalizeFlags($route['flags']) === $normalized['flags']
            ) {
                return; // Ya existe, no agregar
            }
        }

        self::$routes[] = $normalized;
        self::$routesNamed[$name] = $normalized;
    }

    /**
     * Convierte una ruta "pretty" en un regex y adapta el redirect.
     *
     * @param string $pretty
     * @param string $redirect* 
     * @param bool $addPrefix Si es true, añade el prefijo y ajusta los índices de captura.
     */
    private static function convertPrettyToRegex(string $pretty, string $redirect, bool $addPrefix): array
    {
        // CAMBIO: El contador de parámetros empieza en 1 si hay prefijo, si no en 0.
        // Esto es CRUCIAL para que los $1, $2 se asignen correctamente.
        self::$paramCounter = $addPrefix ? 1 : 0;
        $paramMap = [];

        $regex = preg_replace_callback('/\{(\w+)\}/', function ($matches) use (&$paramMap) {
            self::$paramCounter++;
            $paramName = $matches[1];
            $paramMap[$paramName] = self::$paramCounter;
            return '([^/]+)';
        }, $pretty);

        // CAMBIO: Añade el prefijo al inicio del regex si se solicita.
        $prefixRegex = $addPrefix ? '([^/]+/)?' : '';
        $regex = '^' . $prefixRegex . trim($regex, '/') . '/?$';

        foreach ($paramMap as $name => $index) {
            $redirect = str_replace('{' . $name . '}', '$' . $index, $redirect);
        }

        return [$regex, $redirect];
    }

     /**
     * Obtiene una ruta por su nombre.
     *
     * @param string $name
     * @return array|null
     */
    public static function getRoute(string $name): ?array
    {
        return self::$routesNamed[$name] ?? null;
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
               array_shift($matches); // Quita el resultado completo

				// --- LA LÍNEA CLAVE ---
				// Filtra las capturas vacías y re-indexa el array para alinear los parámetros.
				// Esto elimina los 'placeholders' de los grupos opcionales que no coincidieron.
				$matches = array_values(array_filter($matches, 'strlen'));

				// Extraer nombres de la URL 'pretty'
				preg_match_all('/\{(\w+)\}/', $route['pretty'] ?? '', $nameMatches);
				$varNames = $nameMatches[1];

				// Combinar nombres con valores ya limpios
				$named = [];
				foreach ($varNames as $i => $name) {
					$named[$name] = $matches[$i] ?? null;
				}

                return [
					'route'   => $route,
					'params'  => $named, // Ahora ['custom_order_serial' => 'O000000000016514']
					'matches' => $matches,
				];
            }
        }

        return null;
    }

    public static function extractParamsFromPretty(string $pretty) {
        $regex = preg_quote($pretty, '#');
        $regex = preg_replace('#\\\\\{(\w+)\\\\\}#', '([^/]+)', $regex); // convierte {param} a ([^/]+)
        $pattern = '#^' . $regex . '$#';

        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // Busca coincidencias
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // elimina el match completo

            // Extrae nombres de parámetros
            preg_match_all('/\{(\w+)\}/', $pretty, $nameMatches);
            $varNames = $nameMatches[1];

            // Asocia nombre => valor
            $named = [];
            foreach ($varNames as $i => $name) {
                $named[$name] = $matches[$i] ?? null;
            }

            return $named;
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
