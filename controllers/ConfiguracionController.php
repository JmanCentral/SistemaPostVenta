<?php
session_start();
require_once 'models/ConfiguracionModel.php';

class ConfiguracionController {
    private $configuracionModel;

    public function __construct() {
        $this->configuracionModel = new ConfiguracionModel();
    }

    public function index() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "configuracion";
        $existe = $this->configuracionModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Obtener los datos de configuración
        $data = $this->configuracionModel->obtenerConfiguracion();

        // Procesar formulario
        $alert = "";
        if (!empty($_POST)) {
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $email = $_POST['email'];
            $direccion = $_POST['direccion'];

            if (empty($nombre) || empty($telefono) || empty($email) || empty($direccion)) {
                $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
            } else {
                if (isset($_POST['insertar'])) {
                    // Insertar nueva configuración
                    $result = $this->configuracionModel->insertarConfiguracion($nombre, $telefono, $email, $direccion);
                    if ($result) {
                        $alert = '<div class="alert alert-success" role="alert">Configuración insertada correctamente</div>';
                        // Recargar la página para actualizar el estado de los botones
                        echo "<script>window.location.href = 'index.php?action=configuracion';</script>";
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al insertar la configuración</div>';
                    }
                } elseif (isset($_POST['modificar'])) {
                    // Actualizar configuración
                    $id = $_POST['id'];
                    $result = $this->configuracionModel->actualizarConfiguracion($id, $nombre, $telefono, $email, $direccion);
                    if ($result) {
                        $alert = '<div class="alert alert-success" role="alert">Configuración modificada correctamente</div>';
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">Error al modificar la configuración</div>';
                    }
                }
            }
        }

        // Pasar los datos a la vista
        $data['alert'] = $alert;

        // Cargar la vista
        require_once 'views/configuracion.php';
    }
}
?>