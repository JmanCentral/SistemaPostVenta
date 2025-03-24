<?php
session_start();
require_once 'models/ProductoModel.php';

class ProductoController {
    private $productoModel;

    public function __construct() {
        $this->productoModel = new ProductoModel();
    }

    public function index() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "productos";
        $existe = $this->productoModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: index.php?action=denegado");
            exit();
        }

        // Procesar formulario de nuevo producto
        $alert = "";
        if (!empty($_POST)) {
            $codigo = $_POST['codigo'];
            $descripcion = $_POST['producto'];
            $precio_compra = $_POST['precio_compra'];
            $precio_venta = $_POST['precio_venta'];
            $usuario_id = $_SESSION['idUser'];

            if (empty($codigo) || empty($descripcion) || empty($precio_compra) || empty($precio_venta) || $precio_compra < 0 || $precio_venta < 0) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios y los precios deben ser positivos</div>';
            } else {
                // Verificar si el código ya existe
                $result = $this->productoModel->verificarCodigo($codigo);
                if ($result) {
                    $alert = '<div class="alert alert-warning" role="alert">El código ya existe</div>';
                } else {
                    // Procesar la imagen si se subió
                    if ($_FILES['imagen']['error'] == 0) {
                        $nombre_imagen = $_FILES['imagen']['name'];
                        $ruta_temporal = $_FILES['imagen']['tmp_name'];
                        $carpeta_destino = "uploads/";

                        // Crear la carpeta si no existe
                        if (!is_dir($carpeta_destino)) {
                            mkdir($carpeta_destino, 0777, true);
                        }

                        // Generar un nombre único para la imagen
                        $nombre_unico = uniqid() . "_" . $nombre_imagen;
                        $ruta_destino = $carpeta_destino . $nombre_unico;

                        // Mover la imagen a la carpeta de destino
                        if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
                            // Insertar el producto con la imagen
                            $query_insert = $this->productoModel->insertarProducto($codigo, $descripcion, $precio_compra, $precio_venta, $usuario_id, $ruta_destino);
                            if ($query_insert) {
                                $alert = '<div class="alert alert-success" role="alert">Producto registrado correctamente</div>';
                            } else {
                                $alert = '<div class="alert alert-danger" role="alert">Error al registrar el producto</div>';
                            }
                        } else {
                            $alert = '<div class="alert alert-danger" role="alert">Error al subir la imagen</div>';
                        }
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al subir la imagen</div>';
                    }
                }
            }
        }

        // Obtener todos los productos para la tabla
        $productos = $this->productoModel->obtenerProductos();

        // Cargar la vista
        require_once 'views/productos.php';
    }

    public function editar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "productos";
        $existe = $this->productoModel->verificarPermisos($id_user, $permiso);
    
        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }
    
        // Verificar si el ID está presente en la URL
        if (empty($_GET['id'])) {
            header("Location: index.php?action=productos");
            exit();
        }
    
        $id = $_GET['id'];
    
        // Obtener datos del producto para mostrar en el formulario
        $producto = $this->productoModel->obtenerProductoPorId($id);
    
        // Verificar si el producto existe
        if (!$producto) {
            header("Location: index.php?action=productos");
            exit();
        }
    
        // Procesar formulario de edición
        $alert = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = trim($_POST['codigo']);
            $descripcion = trim($_POST['producto']);
            $precio_compra = floatval($_POST['precio_compra']);
            $precio_venta = floatval($_POST['precio_venta']);
    
            if (empty($codigo) || empty($descripcion) || $precio_compra < 0 || $precio_venta < 0) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios y los precios deben ser positivos</div>';
            } else {
                // Procesar la imagen si se subió
                $imagen = $producto['imagen']; // Mantener la imagen actual por defecto
                if ($_FILES['imagen']['error'] == 0) {
                    $nombre_imagen = $_FILES['imagen']['name'];
                    $ruta_temporal = $_FILES['imagen']['tmp_name'];
                    $carpeta_destino = "uploads/";
    
                    // Crear la carpeta si no existe
                    if (!is_dir($carpeta_destino)) {
                        mkdir($carpeta_destino, 0777, true);
                    }
    
                    // Generar un nombre único para la imagen
                    $nombre_unico = uniqid() . "_" . $nombre_imagen;
                    $ruta_destino = $carpeta_destino . $nombre_unico;
    
                    // Mover la imagen a la carpeta de destino
                    if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
                        $imagen = $ruta_destino;
                        // Eliminar la imagen anterior si existe
                        if (!empty($producto['imagen']) && file_exists($producto['imagen'])) {
                            unlink($producto['imagen']);
                        }
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al subir la imagen</div>';
                    }
                }
    
                // Actualizar el producto
                $result = $this->productoModel->actualizarProducto($id, $codigo, $descripcion, $precio_compra, $precio_venta, $imagen);
                if ($result) {
                    $alert = '<div class="alert alert-success" role="alert">Producto actualizado correctamente</div>';
                    header("Location: index.php?action=productos");
                    exit();
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">Error al actualizar el producto</div>';
                }
            }
        }
    
        // Pasar los datos del producto a la vista
        $data = [
            'idproducto' => $producto['idproducto'],
            'codigo' => $producto['codigo'],
            'descripcion' => $producto['descripcion'],
            'precio_compra' => $producto['precio_compra'],
            'precio_venta' => $producto['precio_venta'],
            'imagen' => $producto['imagen'],
            'alert' => $alert
        ];
    
        // Cargar la vista de edición
        require_once 'views/editar_producto.php';
    }
    
    public function eliminar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "productos";
        $existe = $this->productoModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Eliminar producto (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->productoModel->eliminarProducto($id);
            if ($result) {
                header("Location: index.php?action=productos");
                exit();
            } else {
                echo "Error al eliminar el producto.";
            }
        } else {
            header("Location: index.php?action=productos");
            exit();
        }
    }
}
?>