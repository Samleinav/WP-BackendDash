# WP-BackendDash

**WP-BackendDash** es un plugin de WordPress que facilita la creación de interfaces administrativas personalizadas, simulando una arquitectura MVC al estilo Laravel. Permite definir páginas de backend con rutas personalizadas, controladores, modelos y endpoints API de forma modular y organizada.

## 🧠 Estructura lógica del plugin

```
WP-BackendDash/
├── includes/
│   ├── helpers/               # Funciones auxiliares y utilitarias
│   ├── hooks/                 # Registro de hooks y filtros de WordPress
│   ├── installer.php          # Script de instalación o setup inicial del plugin
│   ├── WBESourceLoader.php    # Loader base o de recursos comunes
│   └── WPBackendDashLoader.php# Loader principal, inicia todo el plugin
│
├── src/
│   ├── assets/
│   │   ├── css/           # Hojas de estilo personalizadas
│   │   └── js/            # Scripts JS propios del plugin
│   ├── controllers/           # Controladores estilo MVC (similar a Laravel)
│   ├── models/                # Modelos para acceder a datos (simulación de Eloquent)
│   └── views/                 # Vistas para renderizar contenido del backend
│
├── web/
│   ├── api.php                # Definición de rutas API
│   ├── pages.php              # Definición de páginas administrativas
│   └── routes.php             # Mapeo de rutas personalizadas
│
├── WPBackendDash.php          # Archivo principal del plugin
└── .gitignore                 # Exclusiones para Git
```

## 🔌 Loader principal (`includes/WPBackendDashLoader.php`)

Este archivo se encarga de iniciar todos los componentes del plugin. También es responsable de registrar y cargar assets globales como scripts JS o estilos CSS.

### Registro de scripts y estilos globales

Usamos un helper de assets centralizado (`WBEAssets`) para registrar recursos de forma ordenada:

```php
use WPBackendDash\Helpers\WBEAssets;

WBEAssets::add_js(
    'wpbackenddashactions-js',
    WBE_PLUGIN_URL . 'src/assets/js/wpbackendactions.js',
    ['jquery'],
    '1.0.0'
);

WBEAssets::add_css(
    'nice-forms-css',
    WBE_PLUGIN_URL . 'src/assets/css/nice-forms/nice-forms.css',
    [],
    '1.0.0'
);
```

Esto permite tener una gestión clara y reutilizable de los recursos que necesita el backend.

## 🌐 Definición de Rutas API (`web/api.php`)

Permite registrar endpoints personalizados para el API REST de WordPress usando una sintaxis clara inspirada en Laravel.

### Ejemplo de rutas:

```php
use WPBackendDash\Helpers\WBEAPIManager;
use WPBackendDash\Controllers\WBEApiController;
use WPBackendDash\Controllers\WBEChatsRooms;

WBEAPIManager::get(
    "chat.getform",
    '/chat/get_chat_create',
    [WBEApiController::class, 'getChatCreateModal'],
    [],
    WBEAPIManager::require_login()
);

WBEAPIManager::post(
    "chat.create",
    '/chat/create',
    [WBEChatsRooms::class, 'store'],
    [],
    WBEAPIManager::require_login()
);
```

### Internamente (`WBEAPIManager`):

```php
public static function add_route($name, $route, $methods, $callback, $args = [], $permission_callback = null) {
    self::$routes[] = [
        'name' => $name,
        'full_route' => 'wp-json/' . self::$namespace . $route,
        'route' => $route,
        'methods' => $methods,
        'callback' => $callback,
        'args' => $args,
        'permission_callback' => $permission_callback
    ];
}
```

### Ventajas:

- Sintaxis clara y modular
- Callback directo hacia controladores
- Permisos opcionales con `require_login()`
- Fácil extensión para métodos HTTP

## 🧩 Páginas de Administración (`web/pages.php`)

Permite registrar páginas administrativas para el backend usando `WBEPage::add()`.

### Firma del método:

```php
WBEPage::add(
    string $slug,
    string $title,
    callable|array|string $callback,
    string $icon = '',
    string $capability = 'manage_options',
    int $position = 100,
    bool $visible = false
): void
```

### Ejemplos:

#### Página visible

```php
WBEPage::add(
    'wbe_admin_page_chats_rooms',
    __('Chats Rooms', 'wp-backend-dash'),
    [WBEChatsRooms::init(), 'index'],
    'dashicons-admin-generic',
    'wbe_view_chats_rooms',
    0,
    true
);
```

#### Subpágina oculta

```php
WBEPage::add(
    'wbe_admin_page_chats_room_create',
    __('Chats Rooms', 'wp-backend-dash'),
    [WBEChatsRooms::init(), 'create'],
    'dashicons-admin-generic',
    'wbe_view_chats_room_create',
    1,
    false
);
```

## 📊 Controladores estilo MVC

Los controladores se ubican en `src/controllers/` y se usan como callbacks para las páginas. Ejemplo:

```php
use WPBackendDash\Controllers\WBEChatsRooms;
use WPBackendDash\Helpers\WBEPage;

WBEPage::add(
    'wbe_admin_page_chats_rooms',
    __('Chats Rooms', 'wp-backend-dash'),
    [WBEChatsRooms::init(), 'index'],
    'dashicons-admin-generic',
    'wbe_view_chats_rooms',
    0,
    true
);
```

### Estructura:

```php
src/
└── controllers/
    └── WBEChatsRooms.php
```

```php
class WBEChatsRooms extends ControllerHelper {
    public function index() {
        return self::view('chats_rooms/index');
    }

    public function create() {
        WBEForm::bootstrap();
        return self::view('chats_rooms/create');
    }

    public function store() {
        $data = WBERequest::all();
        $roomChat = new RoomChatModel();
        $roomChat->fill($data);

        if ($roomChat->save()) {
            return request()->Response()
                ->addAction("wbeShowNotify", ["Sala de chat creada exitosamente.", "success"])
                ->addAction("wbeRedirect", ["url" => wberoute('center.rooms.index')])
                ->send();
        } else {
            return request()->Response()
                ->addAction("wbeShowNotify", ["Error al crear la sala de chat.", "error"])
                ->send();
        }
    }
}
```
## 🗃️ Modelos (`src/models/`)

Los modelos representan tablas de la base de datos y permiten interactuar con ellas mediante una API orientada a objetos. Están basados en la clase `WBEModelBase` y ofrecen métodos similares a Eloquent de Laravel, como `find`, `where`, `create`, `save` y `delete`.

---

### 🧱 Ejemplo de un modelo

```php
namespace WPBackendDash\Models;

use WPBackendDash\Helpers\WBEModelBase;

class RoomChatModel extends WBEModelBase {
    protected $table = 'room_chats';

    protected $fillable = [
        'user_id', 
        'meeting_link', 
        'type', 
        'details',
        'attachments',
        'time',
        'tokens',
        'interview_complete',
        'in_use'
    ];
}
```
🔐 El atributo $fillable asegura que solo esos campos puedan ser insertados/actualizados masivamente con fill() o create().

### 🛠 Métodos disponibles
- Método	    Descripción
- fill($data)	Llena los atributos del modelo desde un array asociativo.
- find($id)	    Busca un registro por clave primaria.
- all()	        Retorna todos los registros de la tabla.
- where($col, $val)	Filtra registros por columna/valor.
- save()	    Guarda el modelo actual. Si existe, hace update; si no, crea uno nuevo.
- create($data)	Crea directamente un nuevo registro.
- update($id, $data)	Actualiza un registro específico.
- delete($id)	Elimina un registro por ID.
- exists($id?)	Verifica si el registro existe en la base de datos.
- toArray()	    Retorna los atributos actuales del modelo como array.

#### ✅ Ejemplo práctico

```php

use WPBackendDash\Helpers\WBERequest;
use WPBackendDash\Models\RoomChatModel;

$data = WBERequest::all();

$room = new RoomChatModel();
$room->fill($data);

if ($room->save()) {
    // Éxito: Redirigir o notificar
} else {
    // Error: Mostrar mensaje
}
```

También puedes hacer consultas directas:

```php
RoomChatModel::find(5);
RoomChatModel::where('type', 'interview');
```

## 🛠️ Rutas Personalizadas (`web/routes.php`)

Permite definir Pretty URLs para redirigir internamente al backend:

```php
WBERoute::route(
    'center.rooms.index',
    'center/rooms',
    '/wp-admin/admin.php?page=wbe_admin_page_chats_rooms'
);

WBERoute::route(
    'center.rooms.create',
    'center/rooms/create',
    '/wp-admin/admin.php?page=wbe_admin_page_chats_room_create'
);
```

Internamente:

- Convierte `center/rooms/{$room_id}/view` en regex y redirige a la URL interna con parámetros capturados.

### Ventajas:

- URLs limpias y legibles
- Separación entre rutas y lógica
- Compatible con subdirectorios y parámetros

---
## 🔄 Parámetros dinámicos desde la URL
Gracias a la integración entre WBEPage y WBERoute, los controladores pueden recibir parámetros directamente desde URLs personalizadas.

 #### Definición de ruta:

```php
WBERoute::route(
    'center.rooms.view',
    'center/rooms/{$room_id}/view',
    '/wp-admin/admin.php?page=wbe_admin_page_chats_room_view&$room_id=$2',
);
```

Esta línea define una ruta amigable (center/rooms/{$room_id}/view) que incluye un marcador de posición {$room_id}. Cuando un usuario accede a una URL como /center/rooms/123/view, el sistema captura el valor 123 para room_id.

 #### Página asociada:
```php
WBEPage::add(
    'wbe_admin_page_chats_room_view',
    __('Chat Room View', 'wp-backend-dash'),
    [WBEChatsRooms::init(), 'room_view'],
    'dashicons-admin-generic',
    'wbe_view_chats_room_view',
    1,
    false
);
```
Aquí, se registra una página de administración (wbe_admin_page_chats_room_view) y se le asigna el método room_view del controlador WBEChatsRooms como callback. Esta página no es visible en el menú, pero es accesible internamente.

 #### Controlador:

```php
public function room_view($room_id) {
    return self::view('chats_rooms/view', compact('room_id'));
}
```

Cuando la URL amigable (/center/rooms/123/view) se activa, el sistema de enrutamiento interno de WP-BackendDash redirige a la página de administración asociada. Crucialmente, el valor capturado de {$room_id} (en este caso, 123) se pasa automáticamente como argumento al método room_view del controlador. Esto permite que el controlador acceda y utilice directamente este parámetro para cargar datos específicos o renderizar una vista personalizada.

En resumen: El sistema detecta automáticamente que room_view requiere un parámetro llamado $room_id y se lo inyecta desde la URL (por ejemplo, /center/rooms/123/view se traduce a room_id = 123 en el controlador).

 ### 💡 Ventajas
 - Modularidad: Cada módulo del sistema puede tener su propio controlador.
 - Testeabilidad: Separar lógica de negocio y presentación mejora el mantenimiento.
 - Integración directa con rutas y páginas backend.



