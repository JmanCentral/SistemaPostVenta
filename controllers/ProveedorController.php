<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\ProveedorModel;

class ProveedorController {
    private const REDIRECT_PROVEEDORES = "Location: index.php?action=proveedores";
    private const REDIRECT_PERMISOS = "Location: permisos.php";

    private $proveedorModel;

    public function __construct() {
        session_start();
        $this->proveedorModel = new ProveedorModel();
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
        $permiso = "proveedores";
        $existe = $this->proveedorModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: index.php?action=denegado");
            exit();
        }

        // Cargar la vista
        View::render('proveedores', [
            'proveedores' => $this->proveedorModel->obtenerProveedores(),
            'alert' => $this->_handleCreateForm()
        ]);
    }

    private function _handleCreateForm(): string
    {
        if (empty($_POST)) {
            return "";
        }

        $requiredFields = ['NIT', 'nombre', 'apellido', 'telefono', 'email', 'direccion'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                return '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            }
        }

        $nit = $_POST['NIT'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        $usuario_id = $_SESSION['idUser'];

        if ($this->proveedorModel->verificarProveedor($nombre)) {
            return '<div class="alert alert-warning" role="alert">El proveedor ya existe</div>';
        }

        $query_insert = $this->proveedorModel->insertarProveedor($nit, $nombre, $apellido, $telefono, $email, $direccion, $usuario_id);
        return $query_insert
            ? '<div class="alert alert-success" role="alert">Proveedor registrado</div>'
            : '<div class="alert alert-danger" role="alert">Error al registrar</div>';
    }

    public function editar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "proveedores";
        $existe = $this->proveedorModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header(self::REDIRECT_PERMISOS);
            exit();
        }

        // Verificar si el ID está presente en la URL
        if (empty($_GET['id'])) {
            header(self::REDIRECT_PROVEEDORES);
            exit();
        }

        $id = $_GET['id'];

        // Obtener datos del proveedor para mostrar en el formulario
        $proveedor = $this->proveedorModel->obtenerProveedorPorId($id);

        // Verificar si el proveedor existe
        if (!$proveedor) {
            header(self::REDIRECT_PROVEEDORES);
            exit();
        }

        // Procesar formulario de edición
        $alert = "";
        if (!empty($_POST)) {
            if (empty($_POST['NIT']) || empty($_POST['nombre']) || empty($_POST['apellido']) ||
                empty($_POST['telefono']) || empty($_POST['email']) || empty($_POST['direccion'])) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            } else {
                $nit = $_POST['NIT'];
                $nombre = $_POST['nombre'];
                $apellido = $_POST['apellido'];
                $telefono = $_POST['telefono'];
                $email = $_POST['email'];
                $direccion = $_POST['direccion'];

                // Actualizar proveedor
                $result = $this->proveedorModel->actualizarProveedor($id, $nit, $nombre, $apellido, $telefono, $email, $direccion);
                if ($result) {
                    header(self::REDIRECT_PROVEEDORES);
                    exit();
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">Error al actualizar</div>';
                }
            }
        }

        // Pasar los datos del proveedor a la vista
        View::render('editar_proveedor', array_merge($proveedor, ['alert' => $alert]));
    }

    public function eliminar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "proveedores";
        $existe = $this->proveedorModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header(self::REDIRECT_PERMISOS);
            exit();
        }

        // Eliminar proveedor (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->proveedorModel->eliminarProveedor($id);
            if ($result) {
                header(self::REDIRECT_PROVEEDORES);
                exit();
            } else {
                echo "Error al eliminar el proveedor.";
            }
        } else {
            header(self::REDIRECT_PROVEEDORES);
            exit();
        }
    }

    public function activarProveedor() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "proveedores";
        $existe = $this->proveedorModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header(self::REDIRECT_PERMISOS);
            exit();
        }

        // Eliminar proveedor (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->proveedorModel->activarProveedor($id);
            if ($result) {
                header(self::REDIRECT_PROVEEDORES);
                exit();
            } else {
                echo "Error al eliminar el proveedor.";
            }
        } else {
            header(self::REDIRECT_PROVEEDORES);
            exit();
        }
    }
}