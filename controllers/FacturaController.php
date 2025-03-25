<?php
session_start();
require_once "models/FacturaModel.php";

class FacturaController {
    
    private $model;
    private $id_user;

    public function __construct() {
        $this->model = new FacturaModel();
        $this->id_user = $_SESSION['idUser'] ?? null;
    }

    public function index() {
        // Verify permissions
        $permiso = "ventas";
        $existe = $this->model->verificarPermisos($this->id_user, $permiso);

        if (empty($existe) && $this->id_user != 1) {
            header("Location: permisos.php");
            exit();
        }

        // Get ventas data and pass to view
        $ventas = $this->model->getVentas();
        require_once "views/factura.php";
    }

    public function delete() {
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            if ($this->model->deleteVenta($id)) {
                $_SESSION['message'] = "Venta eliminada correctamente";
            } else {
                $_SESSION['error'] = "Error al eliminar la venta";
            }
            header("Location: index.php?action=facturas");
            exit();
        }
    }
}
?>