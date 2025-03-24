<?php
session_start();
require_once 'models/UserModel.php';

class UsuarioController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UserModel();
    }

    public function index() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "usuarios";
        $existe = $this->usuarioModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: index.php?action=denegado");
            exit();
        }

        // Procesar formulario de nuevo usuario
        $alert = "";
        if (!empty($_POST)) {
            if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave'])) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            } else {
                $nombre = $_POST['nombre'];
                $email = $_POST['correo'];
                $user = $_POST['usuario'];
                $clave = $_POST['clave'];

                // Verificar si el correo ya existe
                $result = $this->usuarioModel->verificarCorreo($email);
                if ($result) {
                    $alert = '<div class="alert alert-warning" role="alert">El correo ya existe</div>';
                } else {
                    // Insertar nuevo usuario
                    $query_insert = $this->usuarioModel->insertarUsuario($nombre, $email, $user, $clave);
                    if ($query_insert) {
                        $alert = '<div class="alert alert-primary" role="alert">Usuario registrado</div>';
                        header("Location: index.php?action=usuarios");
                        exit();
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al registrar</div>';
                    }
                }
            }
        }

        // Obtener todos los usuarios para la tabla
        $usuarios = $this->usuarioModel->obtenerUsuarios();


        // Cargar la vista
        require_once 'views/usuarios.php';
    }

    public function editar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "usuarios";
        $existe = $this->usuarioModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Procesar formulario de edición
        $alert = "";
        if (!empty($_POST)) {
            if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario'])) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            } else {
                $id = $_GET['id'];
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $usuario = $_POST['usuario'];

                // Actualizar usuario
                $result = $this->usuarioModel->actualizarUsuario($id, $nombre, $correo, $usuario);
                if ($result) {
                    $alert = '<div class="alert alert-success" role="alert">Usuario actualizado</div>';
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">Error al actualizar</div>';
                }
            }
        }

        // Obtener datos del usuario para mostrar en el formulario
        if (empty($_GET['id'])) {
            header("Location: index.php?action=usuarios");
            exit();
        }

        $id = $_GET['id'];
        $usuario = $this->usuarioModel->obtenerUsuarioPorId($id);

        if (!$usuario) {
            header("Location: index.php?action=usuarios");
            exit();
        }

        // Cargar la vista de edición
        require_once 'views/editar_usuario.php';
    }

    // Método para eliminar un usuario
    public function eliminar() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "usuarios";
        $existe = $this->usuarioModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Eliminar usuario (cambiar estado a inactivo)
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->usuarioModel->eliminarUsuario($id);
            if ($result) {
                header("Location: index.php?action=usuarios");
                exit();
            } else {
                echo "Error al eliminar el usuario.";
            }
        } else {
            header("Location: usuarios.php");
            exit();
        }
    }

}
?>