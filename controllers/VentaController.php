<?php
session_start();
require_once 'models/VentaModel.php';
require_once 'services/FacturadorService.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';

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
        try {
            // Validar autenticación
            if (!$this->id_usuario) {
                throw new Exception('No autorizado');
            }
    
            // Validar parámetros
            if (!isset($_POST['id']) || !isset($_POST['cantidad'])) {
                throw new Exception('Parámetros incompletos');
            }
    
            // Sanitizar entradas
            $id_detalle = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            $cantidad = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);
    
            if ($id_detalle === false || $cantidad === false || $cantidad <= 0) {
                throw new Exception('Datos inválidos');
            }
    
            // Llamar al modelo
            $resultado = $this->ventasModel->eliminarDetalleTemp($id_detalle, $cantidad);
    
            // Devolver respuesta JSON
            header('Content-Type: application/json');
            echo $resultado;
    
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'estado' => 'error',
                'mensaje' => $e->getMessage()
            ]);
        }
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

    public function obtenerUtilidadesTotales() {
        $permiso = "ventas";
        $existe = $this->ventasModel->verificarPermisos($this->id_usuario, $permiso);

        if (empty($existe) && $this->id_usuario != 1) { // Corregido $this->$id_user a $this->id_usuario
            header("Location: index.php?action=denegado");
            exit();
        }

        $cantidadVendidaTotal = $this->ventasModel->calcularCantidadesVendidas();
        $precioCompraTotal = $this->ventasModel->calcularPrecioCompra();
        $precioVentaTotal = $this->ventasModel->calcularPrecioVenta();
        $gananciaTotal = $this->ventasModel->calcularGanancia();

        require_once "views/gananciasTotales.php";
    }


    public function generarPdf() {
        try {
            // Validar parámetros
            $idVenta = $_GET['v'] ?? null;
            $idCliente = $_GET['cl'] ?? null;
            $correo = $_GET['f'] ?? null;
            
            if (!$idVenta || !$idCliente || !$correo) {
                throw new Exception("Parámetros inválidos soroco");
            }
            
            // Generar PDF
            $pdf = $this->facturador->generarFactura($idVenta, $idCliente);
            
            // Guardar PDF temporalmente
            $filename = 'factura_'.$idVenta.'.pdf';
            $pdf->Output($filename, 'F'); // 'F' guarda en archivo local
            
            // Configurar PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Cambia esto
                $mail->SMTPAuth = true;
                $mail->Username = 'Finazapp2@gmail.com'; // Cambia esto
                $mail->Password = 'ndio iksr ksiw ktgh'; // Cambia esto
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Remitente y destinatario
                $mail->setFrom('Finazapp2@gmail.com', 'PostVenta');
                $mail->addAddress($correo);
                
                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Factura de tu compra';
                $mail->Body    = 'Adjunto encontrarás la factura de tu compra. Gracias por tu preferencia.';
                $mail->AltBody = 'Adjunto encontrarás la factura de tu compra.';
                
                // Adjuntar PDF
                $mail->addAttachment($filename);
                
                $mail->send();
                
                // Eliminar archivo temporal
                unlink($filename);
                
                header("Location: views/vistaMensaje.php?mensaje=" . urlencode("La factura ha sido enviada al correo electrónico"));
                exit;
                
            } catch (Exception $e) {
                // Eliminar archivo temporal en caso de error
                if (file_exists($filename)) {
                    unlink($filename);
                }
                throw new Exception("Error al enviar el correo: {$mail->ErrorInfo}");
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            die("Error al generar factura: " . $e->getMessage());
        }
    }

    public function generarPdffisico() {
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