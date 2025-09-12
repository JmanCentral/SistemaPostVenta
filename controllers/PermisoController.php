<?php
namespace App\Controllers;

use App\Models\PermisoModel;
use App\Core\View;

class PermisoController {
    private $model;

    public function __construct() {
        session_start();
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

        // Procesar el formulario de permisos
        if (isset($_POST['permisos'])) {
            $nuevosPermisos = $_POST['permisos'];
            $this->model->eliminarPermisosUsuario($id);
            if (!empty($nuevosPermisos)) {
                $this->model->asignarPermisosUsuario($id, $nuevosPermisos);
                header("Location: index.php?action=editar_permisos&id=" . $id . "&m=si");
                exit();
            }
        }

        // Cargar la vista y pasarle los datos necesarios
        View::render('permisos_view', [
            'usuario' => $usuario,
            'permisos_asignados' => $this->model->obtenerPermisosUsuario($id),
            'permisos_disponibles' => $this->model->obtenerPermisos()
        ]);
    }
}