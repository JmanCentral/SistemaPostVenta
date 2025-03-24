<?php
// PermisoController.php
require_once "models/PermisoModel.php";

class PermisoController {
    private $model;

    public function __construct() {
        $this->model = new PermisoModel();
    }

    // Método para manejar la edición de permisos
    public function editarPermisos() {
        $id = $_GET['id'];

        // Verificar si el usuario existe
        $usuario = $this->model->obtenerUsuario($id);
        if (empty($usuario)) {
            header("Location: index.php?action=usuarios");
            exit();
        }

        // Obtener permisos asignados al usuario
        $datos = $this->model->obtenerPermisosUsuario($id);

        // Procesar el formulario de permisos
        if (isset($_POST['permisos'])) {
            $permisos = $_POST['permisos'];
            $this->model->eliminarPermisosUsuario($id);
            if (!empty($permisos)) {
                $this->model->asignarPermisosUsuario($id, $permisos);
                header("Location: index.php?action=editar_permisos&id=" . $id . "&m=si");
                exit();
            }
        }

        // Obtener todos los permisos disponibles
        $permisos = $this->model->obtenerPermisos();

        // Cargar la vista
        require_once "views/permisos_view.php";
    }
}
?>