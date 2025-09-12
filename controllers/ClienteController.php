<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\ClienteModel;

class ClienteController {
    private $clienteModel;

    public function __construct() {
        session_start();
        $this->clienteModel = new ClienteModel();
    }

    public function index() {
        if (!isset($_SESSION['idUser'])) {
            echo "⚠️ Por favor inicia sesión.";
            // Considerar una redirección o una vista de error aquí
            return;
        }
        ini_set('display_errors', 0);

        $id_user = $_SESSION['idUser'];
        $permiso = "clientes";
        $existe = $this->clienteModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: index.php?action=denegado");
            exit();
        }
        
        // Cargar la vista y pasarle los datos
        View::render('clientes', ['clientes' => $this->clienteModel->obtenerClientes(), 'alert' => $this->_handleFormSubmission()]);
    }

    /**
     * Procesa el envío del formulario para crear un nuevo cliente.
     * @return string La alerta HTML resultante.
     */
    private function _handleFormSubmission(): string
    {
        if (empty($_POST)) {
            return "";
        }

        $requiredFields = ['nombre', 'apellido', 'identificacion', 'telefono', 'direccion', 'email'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                return '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            }
        }

        $clienteData = [
            'nombre' => $_POST['nombre'],
            'apellido' => $_POST['apellido'],
            'identificacion' => $_POST['identificacion'],
            'telefono' => $_POST['telefono'],
            'direccion' => $_POST['direccion'],
            'email' => $_POST['email'],
            'usuario_id' => $_SESSION['idUser']
        ];

        if ($this->clienteModel->verificarCliente($clienteData['identificacion'], $clienteData['email'])) {
            return '<div class="alert alert-warning" role="alert">El cliente ya existe</div>';
        }

        try {
            $query_insert = $this->clienteModel->insertarCliente(...array_values($clienteData));
            return $query_insert
                ? '<div class="alert alert-success" role="alert">Cliente registrado</div>'
                : '<div class="alert alert-danger" role="alert">Error al registrar</div>';
        } catch (\Exception $e) {
            return '<div class="alert alert-danger" role="alert">Error: ' . $e->getMessage() . '</div>';
        }
    }

    public function editar() {
        if (isset($_SESSION['idUser'])) {
            ini_set('display_errors', 0);
        } else {
            echo "⚠️ Por favor inicia sesión.";
        }
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "clientes";
        $existe = $this->clienteModel->verificarPermisos($id_user, $permiso);
    
        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }
    
        // Verificar si el ID está presente en la URL
        if (empty($_GET['id'])) {
            header("Location: index.php?action=clientes");
            exit();
        }
    
        $id = $_GET['id'];
    
        // Obtener datos del cliente para mostrar en el formulario
        $cliente = $this->clienteModel->obtenerClientePorId($id);
    
        // Verificar si el cliente existe
        if (!$cliente) {
            header("Location: index.php?action=clientes");
            exit();
        }
    
        // Procesar formulario de edición
        $alert = "";
        if (!empty($_POST)) {
            if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['identificacion']) || 
                empty($_POST['telefono']) || empty($_POST['direccion']) || empty($_POST['email'])) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            } else {
                $nombre = $_POST['nombre'];
                $apellido = $_POST['apellido'];
                $identificacion = $_POST['identificacion'];
                $telefono = $_POST['telefono'];
                $direccion = $_POST['direccion'];
                $email = $_POST['email'];
    
                // Actualizar cliente
                $result = $this->clienteModel->actualizarCliente($id, $nombre, $apellido, $identificacion, $telefono, $direccion, $email);
                if ($result) {
                    $alert = '<div class="alert alert-success" role="alert">Cliente actualizado</div>';
                    header("Location: index.php?action=clientes");
                    exit();
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">Error al actualizar</div>';
                }
            }
        }
    
        // Pasar los datos del cliente a la vista
        $data = [
            'idcliente' => $cliente['idcliente'],
            'nombre' => $cliente['nombre'],
            'apellido' => $cliente['apellido'],
            'identificacion' => $cliente['identificacion'],
            'telefono' => $cliente['telefono'],
            'direccion' => $cliente['direccion'],
            'email' => $cliente['email'],
            'alert' => $alert
        ];
    
        // Cargar la vista de edición y pasarle los datos
        View::render('editar_cliente', $data);
    }

    public function eliminar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "clientes";
        $existe = $this->clienteModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Eliminar cliente (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->clienteModel->eliminarCliente($id);
            if ($result) {
                header("Location: index.php?action=clientes");
                exit();
            } else {
                echo "Error al eliminar el cliente.";
            }
        } else {
            header("Location: index.php?action=clientes");
            exit();
        }
    }

    public function activarCliente() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "clientes";
        $existe = $this->clienteModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Actualizar cliente (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->clienteModel->activarCliente($id);
            if ($result) {
                header("Location: index.php?action=clientes");
                exit();
            } else {
                echo "Error al activar el cliente.";
            }
        } else {
            header("Location: index.php?action=clientes");
            exit();
        }
    }

    public function obtenerClientesInactivos() {

    $id_user = $_SESSION['idUser'];
    $permiso = "clientes";
    $existe = $this->clienteModel->verificarPermisos($id_user, $permiso);

    if (empty($existe) && $id_user != 1) {
        header("Location: permisos.php");
        exit();
    }

    // Actualizar cliente (cambiar estado a inactivo)
    if (!empty($_GET['id'])) {
        $id = $_GET['id'];
        $result = $this->clienteModel->obtenerClientesInactivos();
        if ($result) {
            header("Location: index.php?action=clientes");
            exit();
        } else {
            echo "Error al activar el cliente.";
        }
    } else {
        header("Location: index.php?action=clientes");
        exit();
        }

    }   
}