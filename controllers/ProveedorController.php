<?php
session_start();
require_once 'models/ProveedorModel.php';

class ProveedorController {
    private $proveedorModel;

    public function __construct() {
        $this->proveedorModel = new ProveedorModel();
    }

    public function index() {
        // Verificar permisos

        if (isset($_SESSION['idUser'])) {
            ini_set('display_errors', 0); 
        } else {
            echo "⚠️ Por favor inicia sesión.";
        }
        
        $id_user = $_SESSION['idUser'];
        $permiso = "proveedores";
        $existe = $this->proveedorModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: index.php?action=denegado");
            exit();
        }

        // Procesar formulario de nuevo proveedor
        $alert = "";
        if (!empty($_POST)) {
            if (empty($_POST['NIT']) || empty($_POST['nombre']) || empty($_POST['apellido']) || 
                empty($_POST['telefono']) || empty($_POST['email']) || empty($_POST['direccion'])) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            } else {
                $NIT = $_POST['NIT'];
                $nombre = $_POST['nombre'];
                $apellido = $_POST['apellido'];
                $telefono = $_POST['telefono'];
                $email = $_POST['email'];
                $direccion = $_POST['direccion'];
                $usuario_id = $_SESSION['idUser'];

                // Verificar si el proveedor ya existe
                $result = $this->proveedorModel->verificarProveedor($nombre);
                if ($result) {
                    $alert = '<div class="alert alert-warning" role="alert">El proveedor ya existe</div>';
                } else {
                    // Insertar nuevo proveedor
                    $query_insert = $this->proveedorModel->insertarProveedor($NIT, $nombre, $apellido, $telefono, $email, $direccion, $usuario_id);
                    if ($query_insert) {
                        $alert = '<div class="alert alert-success" role="alert">Proveedor registrado</div>';
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al registrar</div>';
                    }
                }
            }
        }

        // Obtener todos los proveedores para la tabla
        $proveedores = $this->proveedorModel->obtenerProveedores();

        // Cargar la vista
        require_once 'views/proveedores.php';
    }

    public function editar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "proveedores";
        $existe = $this->proveedorModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Verificar si el ID está presente en la URL
        if (empty($_GET['id'])) {
            header("Location: index.php?action=proveedores");
            exit();
        }

        $id = $_GET['id'];

        // Obtener datos del proveedor para mostrar en el formulario
        $proveedor = $this->proveedorModel->obtenerProveedorPorId($id);

        // Verificar si el proveedor existe
        if (!$proveedor) {
            header("Location: index.php?action=proveedores");
            exit();
        }

        // Procesar formulario de edición
        $alert = "";
        if (!empty($_POST)) {
            if (empty($_POST['NIT']) || empty($_POST['nombre']) || empty($_POST['apellido']) || 
                empty($_POST['telefono']) || empty($_POST['email']) || empty($_POST['direccion'])) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            } else {
                $NIT = $_POST['NIT'];
                $nombre = $_POST['nombre'];
                $apellido = $_POST['apellido'];
                $telefono = $_POST['telefono'];
                $email = $_POST['email'];
                $direccion = $_POST['direccion'];

                // Actualizar proveedor
                $result = $this->proveedorModel->actualizarProveedor($id, $NIT, $nombre, $apellido, $telefono, $email, $direccion);
                if ($result) {
                    $alert = '<div class="alert alert-success" role="alert">Proveedor actualizado</div>';
                    header("Location: index.php?action=proveedores");
                    exit();
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">Error al actualizar</div>';
                }
            }
        }

        // Pasar los datos del proveedor a la vista
        $data = [
            'idproveedor' => $proveedor['idproveedor'],
            'NIT' => $proveedor['NIT'],
            'nombre' => $proveedor['nombre'],
            'apellido' => $proveedor['apellido'],
            'telefono' => $proveedor['telefono'],
            'email' => $proveedor['email'],
            'direccion' => $proveedor['direccion'],
            'alert' => $alert
        ];

        // Cargar la vista de edición
        require_once 'views/editar_proveedor.php';
    }

    public function eliminar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "proveedores";
        $existe = $this->proveedorModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Eliminar proveedor (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->proveedorModel->eliminarProveedor($id);
            if ($result) {
                header("Location: index.php?action=proveedores");
                exit();
            } else {
                echo "Error al eliminar el proveedor.";
            }
        } else {
            header("Location: index.php?action=proveedores");
            exit();
        }
    }

    public function activarProveedor() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "proveedores";
        $existe = $this->proveedorModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Eliminar proveedor (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->proveedorModel->activarProveedor($id);
            if ($result) {
                header("Location: index.php?action=proveedores");
                exit();
            } else {
                echo "Error al eliminar el proveedor.";
            }
        } else {
            header("Location: index.php?action=proveedores");
            exit();
        }
    }
}
?>