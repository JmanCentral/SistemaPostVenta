<?php
// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enrutador básico
$action = $_GET['action'] ?? 'login'; // Por defecto, muestra el login

// Definir las rutas permitidas
$routes = [
    'login' => ['controller' => 'AuthController', 'method' => 'login'],
    'dashboard' => ['controller' => 'DashboardController', 'method' => 'index'],
    'usuarios' => ['controller' => 'UsuarioController', 'method' => 'index'], // Mostrar usuarios
    'editar_usuario' => ['controller' => 'UsuarioController', 'method' => 'editar'], // Editar usuario
    'eliminar_usuario' => ['controller' => 'UsuarioController', 'method' => 'eliminar'], // Eliminar usuario
    'clientes' => ['controller' => 'ClienteController', 'method' => 'index'], // Mostrar clientes
    'editar_cliente' => ['controller' => 'ClienteController', 'method' => 'editar'], // Editar cliente
    'eliminar_cliente' => ['controller' => 'ClienteController', 'method' => 'eliminar'], 
    'proveedores' => ['controller' => 'ProveedorController', 'method' => 'index'], // Mostrar proveedores
    'editar_proveedor' => ['controller' => 'ProveedorController', 'method' => 'editar'], // Editar proveedor
    'eliminar_proveedor' => ['controller' => 'ProveedorController', 'method' => 'eliminar'], 
    'productos' => ['controller' => 'ProductoController', 'method' => 'index'],
    'editar_producto' => ['controller' => 'ProductoController', 'method' => 'editar'],
    'eliminar_producto' => ['controller' => 'ProductoController', 'method' => 'eliminar'],
    'inventario' => ['controller' => 'InventarioController', 'method' => 'index'], // Mostrar inventario
    'editar_inventario' => ['controller' => 'InventarioController', 'method' => 'editar'], // Editar inventario
    'eliminar_inventario' => ['controller' => 'InventarioController', 'method' => 'eliminar'],
    'configuracion' => ['controller' => 'ConfiguracionController', 'method' => 'index'],
    'permisos' => ['controller' => 'PermisoController', 'method' => 'index'],
    'editar_permisos' => ['controller' => 'PermisoController', 'method' => 'editarPermisos'],
    'nueva_venta' => ['controller' => 'VentaController', 'method' => 'index'],
    'denegado' => ['controller' => 'DenegacionController', 'method' => 'index'],
];

// Verificar si la ruta existe
if (array_key_exists($action, $routes)) {
    $controllerName = $routes[$action]['controller'];
    $methodName = $routes[$action]['method'];

    // Incluir el controlador
    $controllerFile = "controllers/$controllerName.php";
    if (file_exists($controllerFile)) {
        require_once $controllerFile;

        // Crear una instancia del controlador y llamar al método
        $controller = new $controllerName();
        if (method_exists($controller, $methodName)) {
            $controller->$methodName();
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "Método no encontrado: $methodName";
        }
    } else {
        header('HTTP/1.0 404 Not Found');
        echo "Controlador no encontrado: $controllerFile";
    }
} else {
    header('HTTP/1.0 404 Not Found');
    echo "Página no encontrada";
}
?>