<?php
session_start();
require_once 'models/VentaModel.php';

class VentaController {
    private $ventaModel;

    public function __construct() {
        $this->ventaModel = new VentaModel();
    }

    public function index() {
        // Verificar permisos
        $id_user = $_SESSION['idUser'];
        $permiso = "nueva_venta";
        $existe = $this->ventaModel->verificarPermisos($id_user, $permiso);

        if (empty($existe) && $id_user != 1) {
            header("Location: index.php?action=denegado");
            exit();
        }

        // Cargar la vista
        require_once 'views/nueva_venta.php';
    }

    public function buscarCliente() {
        if (isset($_GET['q'])) {
            $identificacion = $_GET['q'];
            $datos = $this->ventaModel->buscarCliente($identificacion);
            echo json_encode($datos);
            exit();
        }
    }

    public function buscarProducto() {
        if (isset($_GET['pro'])) {
            $nombre = $_GET['pro'];
            $datos = $this->ventaModel->buscarProducto($nombre);
            echo json_encode($datos);
            exit();
        }
    }

    public function obtenerDetalleTemporal() {
        if (isset($_GET['detalle'])) {
            $id_usuario = $_SESSION['idUser'];
            $datos = $this->ventaModel->obtenerDetalleTemporal($id_usuario);
            echo json_encode($datos);
            exit();
        }
    }

    public function eliminarDetalleTemporal() {
        if (isset($_GET['delete_detalle'])) {
            $id_detalle = $_GET['id'];
            $msg = $this->ventaModel->eliminarDetalleTemporal($id_detalle);
            echo $msg;
            exit();
        }
    }

    public function procesarVenta() {
        if (isset($_GET['procesarVenta'])) {
            $id_cliente = $_GET['id'];
            $id_user = $_SESSION['idUser'];
            $tipo_pago = $_GET['tipo_pago'];
            $fecha_venta = $_GET['fecha_venta'];

            $msg = $this->ventaModel->procesarVenta($id_cliente, $id_user, $tipo_pago, $fecha_venta);
            echo json_encode($msg);
            exit();
        }
    }

    public function agregarDetalleTemporal() {
        if (isset($_POST['action'])) {
            $id = $_POST['id'];
            $cant = $_POST['cant'];
            $precio = $_POST['precio'];
            $id_user = $_SESSION['idUser'];

            $msg = $this->ventaModel->agregarDetalleTemporal($id, $cant, $precio, $id_user);
            echo json_encode($msg);
            exit();
        }
    }
}
?>