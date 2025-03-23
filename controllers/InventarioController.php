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

        // Procesar formulario de nuevo registro de inventario
        $alert = "";
        if (!empty($_POST)) {
            $codproducto = $_POST['codproducto'];
            $idproveedor = $_POST['idproveedor'];
            $cantidad = $_POST['cantidad'];
            $usuario_id = $_SESSION['idUser'];

            if (empty($codproducto) || empty($idproveedor) || empty($cantidad) || $cantidad < 0) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            } else {
                // Verificar si ya existe un registro de inventario para este producto y proveedor
                $result = $this->inventarioModel->verificarInventario($codproducto, $idproveedor);

                if ($result) {
                    // Si existe, actualizar la cantidad
                    $query_update = $this->inventarioModel->actualizarInventario($codproducto, $idproveedor, $cantidad);
                    if ($query_update) {
                        $alert = '<div class="alert alert-success" role="alert">Inventario actualizado correctamente</div>';
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al actualizar el inventario</div>';
                    }
                } else {
                    // Si no existe, insertar un nuevo registro
                    $query_insert = $this->inventarioModel->insertarInventario($codproducto, $idproveedor, $cantidad, $usuario_id);
                    if ($query_insert) {
                        $alert = '<div class="alert alert-success" role="alert">Inventario registrado correctamente</div>';
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al registrar el inventario</div>';
                    }
                }
            }
        }

        // Obtener todos los registros de inventario para la tabla
        $inventario = $this->inventarioModel->obtenerInventario();

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
        $inventario = $this->inventarioModel->obtenerInventarioPorId($id);

        // Verificar si el registro existe
        if (!$inventario) {
            header("Location: index.php?action=inventario");
            exit();
        }

        // Procesar formulario de edición
        $alert = "";
        if (!empty($_POST)) {
            $codproducto = $_POST['codproducto'];
            $idproveedor = $_POST['idproveedor'];
            $cantidad = $_POST['cantidad'];

            if (empty($codproducto) || empty($idproveedor) || empty($cantidad) || $cantidad < 0) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            } else {
                // Actualizar registro de inventario
                $result = $this->inventarioModel->actualizarInventario($codproducto, $idproveedor, $cantidad);
                if ($result) {
                    $alert = '<div class="alert alert-success" role="alert">Inventario actualizado correctamente</div>';
                    header("Location: index.php?action=inventario");
                    exit();
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">Error al actualizar el inventario</div>';
                }
            }
        }

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