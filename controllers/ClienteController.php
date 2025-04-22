<?php
session_start();
require_once 'models/ClienteModel.php';

class ClienteController {
    private $clienteModel;

    public function __construct() {
        $this->clienteModel = new ClienteModel();
    }

    public function index() {
       

        if (isset($_SESSION['idUser'])) {
            ini_set('display_errors', 0); 
        } else {
            echo "⚠️ Por favor inicia sesión.";
        }

        $id_user = $_SESSION['idUser'];
        
        $permiso = "clientes";
        $existe = $this->clienteModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: index.php?action=denegado");
            exit();
        }

        // Procesar formulario de nuevo cliente
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
                $usuario_id = $_SESSION['idUser'];
    
                // Verificar si el cliente ya existe
                $result = $this->clienteModel->verificarCliente($identificacion , $email);

                if ($result) {
                    $alert = '<div class="alert alert-warning" role="alert">El cliente ya existe</div>';
                } else {
                    try {
                        // Insertar nuevo cliente (esto ahora lanzará excepción si el email no es válido)
                        $query_insert = $this->clienteModel->insertarCliente($nombre, $apellido, $identificacion, $telefono, $direccion, $email, $usuario_id);
                        if ($query_insert) {
                            $alert = '<div class="alert alert-success" role="alert">Cliente registrado</div>';
                        } else {
                            $alert = '<div class="alert alert-danger" role="alert">Error al registrar</div>';
                        }
                    } catch (Exception $e) {
                        // Capturar excepción de email no válido
                        $alert = '<div class="alert alert-danger" role="alert">Error: ' . $e->getMessage() . '</div>';
                    }
                }
            }
        }
    

        // Obtener todos los clientes para la tabla
        $clientes = $this->clienteModel->obtenerClientes();

        // Cargar la vista
        require_once 'views/clientes.php';
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
    
        // Cargar la vista de edición
        require_once 'views/editar_cliente.php';
    }

    public function eliminar() {
        $this->verificarPermisosJson(); // Método común para verificación
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $id = $_POST['id'] ?? null;
            $tipo = $_POST['tipo'] ?? 'desactivar';
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                exit();
            }
            
            try {
                if ($tipo === 'eliminar') {
                    $result = $this->clienteModel->eliminarClienteDefinitivo($id);
                } else {
                    $result = $this->clienteModel->eliminarCliente($id);
                }
                
                echo json_encode([
                    'success' => (bool)$result,
                    'action' => $tipo,
                    'message' => $result ? 'Operación exitosa' : 'Error en la operación'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error del sistema: ' . $e->getMessage()
                ]);
            }
            exit();
        }
        
        header("Location: index.php?action=clientes");
        exit();
    }
    
    private function verificarPermisosJson() {
        $id_user = $_SESSION['idUser'];
        $permiso = "clientes";
        $existe = $this->clienteModel->verificarPermisos($id_user, $permiso);
    
        if (empty($existe) && $id_user != 1) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sin permisos']);
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $id = $_POST['id'] ?? null;
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                exit();
            }
            
            $result = $this->clienteModel->activarCliente($id);
            echo json_encode(['success' => $result, 'action' => 'activar']);
            exit();
        }
        
        header("Location: index.php?action=clientes");
        exit();
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
?>