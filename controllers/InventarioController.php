<?php
session_start();
require_once 'models/InventarioModel.php';

class InventarioController {
    private $inventarioModel;

    public function __construct() {
        $this->inventarioModel = new InventarioModel();
    }

    public function index() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "inventario";
        $existe = $this->inventarioModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Obtener todos los productos y proveedores para el modal
        $productos = $this->inventarioModel->obtenerProductos();
        $proveedores = $this->inventarioModel->obtenerProveedores();

        // Procesar formulario de nuevo registro de inventario
        $alert = "";
        if (!empty($_POST)) {
            $codproducto = $_POST['codproducto'];
            $idproveedor = $_POST['idproveedor'];
            $cantidad = $_POST['cantidad'];
            $usuario_id = $_SESSION['idUser'];

            // Verificar si el producto existe
            $producto = $this->inventarioModel->verificarProducto($codproducto);
            if (!$producto) {
                $alert = '<div class="alert alert-danger" role="alert">El producto no existe o está inactivo</div>';
            }

            // Verificar si el proveedor existe
            $proveedor = $this->inventarioModel->verificarProveedor($idproveedor);
            if (!$proveedor) {
                $alert = '<div class="alert alert-danger" role="alert">El proveedor no existe o está inactivo</div>';
            }

            // Si no hay errores, proceder con la inserción o actualización
            if (empty($alert)) {
                // Verificar si ya existe un registro de inventario para este producto y proveedor
                $inventarioExistente = $this->inventarioModel->verificarInventario($codproducto, $idproveedor);

                if ($inventarioExistente) {
                    // Si existe, actualizar la cantidad
                    $result = $this->inventarioModel->actualizarInventario($codproducto, $idproveedor, $cantidad);
                    if ($result) {
                        $alert = '<div class="alert alert-success" role="alert">Inventario actualizado correctamente</div>';
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al actualizar el inventario</div>';
                    }
                } else {
                    // Si no existe, insertar un nuevo registro
                    $result = $this->inventarioModel->insertarInventario($codproducto, $idproveedor, $cantidad, $usuario_id);
                    if ($result) {
                        $alert = '<div class="alert alert-success" role="alert">Inventario registrado correctamente</div>';
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al registrar el inventario</div>';
                    }
                }
            }
        }

        // Obtener todos los registros de inventario para la tabla
        $inventario = $this->inventarioModel->obtenerInventario();

        // Pasar los datos a la vista
        $data = [
            'inventario' => $inventario,
            'productos' => $productos,
            'proveedores' => $proveedores,
            'alert' => $alert
        ];

        // Cargar la vista
        require_once 'views/inventario.php';
    }

    public function editar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "inventario";
        $existe = $this->inventarioModel->verificarPermisos($id_user, $permiso);
    
        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }
    
        // Verificar si el ID está presente en la URL
        if (empty($_GET['id'])) {
            header("Location: index.php?action=inventario");
            exit();
        }
    
        $id = $_GET['id'];
    
        // Obtener datos del registro de inventario para mostrar en el formulario
        $data_inventario = $this->inventarioModel->obtenerInventarioPorId($id);
    
        // Verificar si el registro existe
        if (!$data_inventario) {
            header("Location: index.php?action=inventario");
            exit();
        }
    
        // Obtener todos los productos y proveedores para el modal (similar al index)
        $productos = $this->inventarioModel->obtenerProductos();
        $proveedores = $this->inventarioModel->obtenerProveedores();
    
        // Procesar formulario de edición
        $alert = "";
        if (!empty($_POST)) {
            $cantidad = $_POST['cantidad'];
    
            if (empty($cantidad) || $cantidad < 0) {
                $alert = '<div class="alert alert-danger" role="alert">La cantidad es obligatoria y debe ser un número positivo</div>';
            } else {
                // Actualizar la cantidad en el inventario
                $result = $this->inventarioModel->actualizarCantidadInventario($id, $cantidad);
                if ($result) {
                    $alert = '<div class="alert alert-success" role="alert">Inventario actualizado correctamente</div>';
                    header("Location: index.php?action=inventario");
                    exit();
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">Error al actualizar el inventario</div>';
                }
            }
        }
    
        // Pasar los datos del inventario, productos y proveedores a la vista
        $data = [
            'data_inventario' => $data_inventario,
            'productos' => $productos,
            'proveedores' => $proveedores,
            'alert' => $alert
        ];
    
        // Cargar la vista de edición
        require_once 'views/editar_inventario.php';
    }

    public function eliminar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "inventario";
        $existe = $this->inventarioModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Eliminar registro de inventario (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->inventarioModel->eliminarInventario($id);
            if ($result) {
                header("Location: index.php?action=inventario");
                exit();
            } else {
                echo "Error al eliminar el registro de inventario.";
            }
        } else {
            header("Location: index.php?action=inventario");
            exit();
        }
    }
}
?>