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
        // Asegurar respuesta JSON
        header('Content-Type: application/json');
        ob_clean(); 
    
        try {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception("ID de venta no proporcionado o inválido.");
            }
    
            // Intentar eliminar la venta
            $this->model->EliminarVenta($id);
    
            echo json_encode([
                'success' => true,
                'message' => 'Venta eliminada correctamente.',
                'redirect' => 'index.php?action=facturas'
            ]);
        } catch (Exception $e) {
            // Registrar error y devolver JSON
            $this->registrarError('Error al eliminar venta', $e->getMessage());
    
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    
        exit();
    }
    
}
?>