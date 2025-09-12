<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\ProductoModel;

class ProductoController {
    private const REDIRECT_PRODUCTOS = "Location: index.php?action=productos";
    private const REDIRECT_PERMISOS = "Location: permisos.php";

    private $productoModel;

    public function __construct() {
        session_start();
        $this->productoModel = new ProductoModel();
    }

    public function index() {
        // Verificar permisos
        if (isset($_SESSION['idUser'])) {
            ini_set('display_errors', 0);
        } else {
            echo "⚠️ Por favor inicia sesión.";
            return;
        }

        $id_user = $_SESSION['idUser'];
        $permiso = "productos";
        $existe = $this->productoModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: index.php?action=denegado");
            exit();
        }

        View::render('productos', [
            'productos' => $this->productoModel->obtenerProductos(),
            'alert' => $this->_handleCreateForm()
        ]);
    }

    private function _handleCreateForm(): string
    {
        if (empty($_POST)) {
            return "";
        }

        $codigo = $_POST['codigo'];
        $descripcion = $_POST['producto'];
        $precio_compra = $_POST['precio_compra'];
        $precio_venta = $_POST['precio_venta'];

        if (empty($codigo) || empty($descripcion) || empty($precio_compra) || empty($precio_venta) || $precio_compra < 0 || $precio_venta < 0) {
            return '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios y los precios deben ser positivos</div>';
        }

        if ($this->productoModel->verificarCodigo($codigo)) {
            return '<div class="alert alert-warning" role="alert">El código ya existe</div>';
        }

        $ruta_destino = $this->_handleImageUpload();
        if ($ruta_destino === false) {
            return '<div class="alert alert-danger" role="alert">Error al subir la imagen</div>';
        }

        $usuario_id = $_SESSION['idUser'];
        $query_insert = $this->productoModel->insertarProducto($codigo, $descripcion, $precio_compra, $precio_venta, $usuario_id, $ruta_destino);

        return $query_insert
            ? '<div class="alert alert-success" role="alert">Producto registrado correctamente</div>'
            : '<div class="alert alert-danger" role="alert">Error al registrar el producto</div>';
    }

    public function editar() {
        // Verificar permisos
        if (isset($_SESSION['idUser'])) {
            ini_set('display_errors', 0);
        } else {
            echo "⚠️ Por favor inicia sesión.";
            return;
        }

        $id_user = $_SESSION['idUser'];
        $permiso = "productos";
        $existe = $this->productoModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header(self::REDIRECT_PERMISOS);
            exit();
        }

        // Verificar si el ID está presente en la URL
        if (empty($_GET['id'])) {
            header(self::REDIRECT_PRODUCTOS);
            exit();
        }

        $id = $_GET['id'];
        // Obtener datos del producto para mostrar en el formulario
        $producto = $this->productoModel->obtenerProductoPorId($id);

        // Verificar si el producto existe
        if (!$producto) {
            header(self::REDIRECT_PRODUCTOS);
            exit();
        }

        $alert = $this->_handleEditForm($id, $producto);

        // Pasar los datos del producto a la vista
        $producto['alert'] = $alert;

        // Cargar la vista de edición
        View::render('editar_producto', $producto);
    }

    private function _handleEditForm(int $id, array $producto): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = trim($_POST['codigo']);
            $descripcion = trim($_POST['producto']);
            $precio_compra = floatval($_POST['precio_compra']);
            $precio_venta = floatval($_POST['precio_venta']);

            if (empty($codigo) || empty($descripcion) || $precio_compra < 0 || $precio_venta < 0) {
                return '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios y los precios deben ser positivos</div>';
            }

            $imagen = $this->_handleImageUpload($producto['imagen']);
            if ($imagen === false) {
                return '<div class="alert alert-danger" role="alert">Error al subir la nueva imagen</div>';
            }

            $result = $this->productoModel->actualizarProducto($id, $codigo, $descripcion, $precio_compra, $precio_venta, $imagen);
            if ($result) {
                header(self::REDIRECT_PRODUCTOS);
                exit();
            }
            return '<div class="alert alert-danger" role="alert">Error al actualizar el producto</div>';
        }
        return "";
    }

    private function _handleImageUpload(string $existingImage = null)
    {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            return $existingImage ?? null; // No new image uploaded, return existing or null
        }

        $carpeta_destino = "uploads/";
        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }

        $nombre_imagen = $_FILES['imagen']['name'];
        $ruta_temporal = $_FILES['imagen']['tmp_name'];
        $nombre_unico = uniqid() . "_" . basename($nombre_imagen);
        $ruta_destino = $carpeta_destino . $nombre_unico;

        if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
            // Delete old image if it exists
            if ($existingImage && file_exists($existingImage)) {
                unlink($existingImage);
            }
            return $ruta_destino;
        }

        return false; // Upload failed
    }

    public function eliminar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "productos";
        $existe = $this->productoModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header(self::REDIRECT_PERMISOS);
            exit();
        }

        // Eliminar producto (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->productoModel->eliminarProducto($id);
            if ($result) {
                header(self::REDIRECT_PRODUCTOS);
                exit();
            } else {
                echo "Error al eliminar el producto.";
            }
        } else {
            header(self::REDIRECT_PRODUCTOS);
            exit();
        }
    }

    public function activarProducto() {
        // Verificar permisos

        $id_user = $_SESSION['idUser'];
        $permiso = "productos";
        $existe = $this->productoModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header(self::REDIRECT_PERMISOS);
            exit();
        }

        // Eliminar producto (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->productoModel->activarProducto($id);
            if ($result) {
                header(self::REDIRECT_PRODUCTOS);
                exit();
            } else {
                echo "Error al eliminar el producto.";
            }
        } else {
            header(self::REDIRECT_PRODUCTOS);
            exit();
        }
    }
}