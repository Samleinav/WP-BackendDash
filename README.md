#WP-BackendDash

**WP-BackendDash** is a WordPress plugin that makes it easy to create custom administrative interfaces, simulating a Laravel-style MVC architecture. It allows you to define backend pages with custom routes, controllers, models, and API endpoints in a modular and organized way.

## 🧠 Plugin Logical Structure

```
WP-BackendDash/
├── includes/
│ ├── helpers/ # Helper and utility functions
│ ├── hooks/ # Registering WordPress hooks and filters
│ ├── installer.php # Initial plugin installation or setup script
│ ├── WBESourceLoader.php # Base or common resource loader
│ └── WPBackendDashLoader.php # Main loader, starts the entire plugin
│
├── src/
│ ├── assets/
│ │ ├── css/ # Stylesheets custom
│ │ └── js/ # Plugin-specific JS scripts
│ ├── controllers/ # MVC-style controllers (similar to Laravel)
│ ├── models/ # Models for accessing data (Eloquent simulation)
│ └── views/ # Views for rendering backend content
│
├── web/
│ ├── api.php # Defining API routes
│ ├── pages.php # Defining administrative pages
│ └── routes.php # Mapping custom routes
│
├── WPBackendDash.php # Main plugin file
└── .gitignore # Git Exclusions
```

## 🔌 Main Loader (`includes/WPBackendDashLoader.php`)

This file is responsible for starting all plugin components. It is also responsible for registering and loading global assets such as JS scripts or CSS styles.

### Registering Global Scripts and Styles

We use a centralized asset helper (`WBEAssets`) to register resources in an orderly manner:

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

This allows for clear and reusable management of the resources needed by the backend.

## 🌐 Defining API Routes (`web/api.php`)

Allows you to register custom endpoints for the WordPress REST API using a clear, Laravel-inspired syntax.

### Example routes:

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

### Internally (`WBEAPIManager`):

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

### Advantages:

- Clear and modular syntax
- Direct callbacks to controllers
- Optional permissions with `require_login()`
- Easy extension for HTTP methods

## 🧩 Administration Pages (`web/pages.php`)

Allows you to register administrative pages for the backend using `WBEPage::add()`.

### Method signature:

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

### Examples:

#### Page visible

```php
WBEPage::add( 
'wbe_admin_page_chats_rooms', 
__('Chats Rooms', 'wp-backend-dash'), 
[WBEChatsRooms::init(), 'index'], 
'dashicons-admin-generic', 
'wbe_view_chats_rooms', 
0. 
true
);
```

#### Hidden Subpage

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

## 📊 MVC-Style Controllers

Controllers are located in `src/controllers/` and are used as callbacks for pages. Example:

```php
use WPBackendDash\Controllers\WBEChatsRooms;
use WPBackendDash\Helpers\WBEPage;

WBEPage::add( 
'wbe_admin_page_chats_rooms', 
__('Chats Rooms', 'wp-backend-dash'), 
[WBEChatsRooms::init(), 'index'], 
'dashicons-admin-generic', 
'wbe_view_chats_rooms', 
0. 
true
);
```

### Structure:

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

## 🛠️ Custom Routes (`web/routes.php`)

Allows you to define Pretty URLs to redirect internally to the backend:

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

Internally:

- Converts `center/rooms/{$room_id}/view` to a regex and redirects to the internal URL with captured parameters.

### Advantages:

- Clean and readable URLs
- Separation of routes and logic
- Support for subdirectories and parameters

---
## 🔄 Dynamic parameters from the URL
Thanks to the integration between WBEPage and WBERoute, controllers can receive parameters directly from custom URLs.

#### Route definition:

```php
WBERoute::route(
'center.rooms.view',
'center/rooms/{$room_id}/view',
'/wp-admin/admin.php?page=wbe_admin_page_chats_room_view&$room_id=$2',
);
```

This line defines a friendly route (center/rooms/{$room_id}/view) that includes a {$room_id} placeholder. When a user accesses a URL like /center/rooms/123/view, the system captures the value 123 for room_id.

#### Associated Page:
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
Here, an administration page (wbe_admin_page_chats_room_view) is registered and assigned the room_view method of the WBEChatsRooms controller as a callback. This page is not visible in the menu, but is accessible internally.

#### Controller:

```php
public function room_view($room_id) {
return self::view('chats_rooms/view', compact('room_id'));
}
```

When the friendly URL (/center/rooms/123/view) is triggered, WP-BackendDash's internal routing system redirects to the associated admin page. Crucially, the captured value of {$room_id} (in this case, 123) is automatically passed as an argument to the controller's room_view method. This allows the controller to directly access and use this parameter to load specific data or render a custom view.

In short: The system automatically detects that room_view requires a parameter called $room_id and injects it from the URL (e.g., /center/rooms/123/view is translated to room_id = 123 in the controller).

### 💡 Advantages
- Modularity: Each system module can have its own controller.
- Testability: Separating business logic from presentation improves maintenance.
- Direct integration with backend routes and pages.



