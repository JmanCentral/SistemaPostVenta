<?php
session_start();
require_once 'models/VentaModel.php';
require_once 'services/FacturadorService.php';

class VentaController {
    private $ventasModel;
    private $id_usuario;
    private $facturador;


    public function __construct() {
        $this->ventasModel = new VentaModel();
        $this->id_usuario = $_SESSION['idUser'] ?? null;
        $this->facturador = new FacturadorService();
    }

    public function index() {
        // Verificar permisos

        $permiso = "ventas";
        $existe = $this->ventasModel->verificarPermisos($this->id_usuario, $permiso);

        if (empty($existe) && $this->id_usuario != 1) { // Corregido $this->$id_user a $this->id_usuario
            header("Location: index.php?action=denegado");
            exit();
        }

        require_once "views/nueva_venta.php";
    }

    public function buscarCliente() {
        if (!isset($_GET['q'])) {
            echo json_encode([]);
            return;
        }

        $identificacion = $_GET['q'];
        $datos = $this->ventasModel->buscarCliente($identificacion);
        echo json_encode($datos);
    }

    public function buscarProducto() {
        if (!isset($_GET['pro'])) {
            echo json_encode([]);
            return;
        }

        $nombre = $_GET['pro'];
        $datos = $this->ventasModel->buscarProducto($nombre);
        echo json_encode($datos);
    }

    public function obtenerDetalle() {
        if (!$this->id_usuario) {
            echo json_encode([]);
            return;
        }

        $datos = $this->ventasModel->obtenerDetalleTemp($this->id_usuario);
        echo json_encode($datos);
    }

    public function eliminarDetalle() {
        if (!isset($_GET['id']) || !$this->id_usuario) {
            echo "Error";
            return;
        }

        $id_detalle = $_GET['id'];
        $msg = $this->ventasModel->eliminarDetalleTemp($id_detalle);
        echo $msg;
    }

    public function procesarVenta() {

        $id_cliente = $_POST['id_cliente'];
        $tipo_pago = $_POST['tipo_pago'];
        $fecha_venta = $_POST['fecha_venta'];

        $resultado = $this->ventasModel->procesarVenta($id_cliente, $this->id_usuario, $tipo_pago, $fecha_venta);
        echo json_encode($resultado);
    }

    public function agregarProducto() {
        if (!$this->id_usuario || !isset($_POST['id']) || !isset($_POST['cant']) || !isset($_POST['precio'])) {
            echo json_encode("Error al ingresar");
            return;
        }

        $id = $_POST['id'];
        $cant = $_POST['cant'];
        $precio = $_POST['precio'];

        $msg = $this->ventasModel->agregarProductoTemp($id, $cant, $precio, $this->id_usuario);
        echo json_encode($msg);
    }

    public function obtenerUtilidades(){

        $permiso = "ventas";
        $existe = $this->ventasModel->verificarPermisos($this->id_usuario, $permiso);

        if (empty($existe) && $this->id_usuario != 1) { // Corregido $this->$id_user a $this->id_usuario
            header("Location: index.php?action=denegado");
            exit();
        }

        $ganancias = $this->ventasModel->obtenerGanancias();
        require_once "views/ganancias.php";
    }   


    public function generarPdf() {
        try {
            // Limpiar buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            // Validar parámetros
            $idVenta = $_GET['v'] ?? null;
            $idCliente = $_GET['cl'] ?? null;
            
            if (!$idVenta || !$idCliente) {
                throw new Exception("Parámetros inválidos");
            }
            
            // Generar PDF
            $pdf = $this->facturador->generarFactura($idVenta, $idCliente);
            
            // Configurar cabeceras
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="factura_'.$idVenta.'.pdf"');
            
            // Salida del PDF
            $pdf->Output('I', 'factura_'.$idVenta.'.pdf');
            exit;
            
        } catch (Exception $e) {
            http_response_code(500);
            die("Error al generar factura: " . $e->getMessage());
        }
    }

}
?>