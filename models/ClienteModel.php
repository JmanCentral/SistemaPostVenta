<?php
require_once 'config/database.php'; // Asegúrate de incluir la conexión a la base de datos
require_once 'services/ZeroBounceService.php';

class ClienteModel {
    private $conexion;

    public function __construct() {
        $this->conexion = getConnection(); // Obtiene la conexión a la base de datos
        $this->zeroBounce = new ZeroBounceService('cb9caa09ea224fc0a2edc52617447690');
    }

    private function validarEmail($email) {
        $response = $this->zeroBounce->validateEmail($email);
        
        if ($response === null) {
            return ['valido' => false, 'error' => 'No se pudo conectar con el servicio de validación'];
        }
        
        // Estados considerados como "válidos" (puedes ajustar según tus necesidades)
        $estadosValidos = ['valid', 'catch-all'];
        
        return [
            'valido' => in_array($response['status'], $estadosValidos),
            'status' => $response['status'],
            'sub_status' => $response['sub_status'] ?? null,
            'response' => $response
        ];
    }

    // Verifica si el cliente ya existe
    public function verificarCliente($identificacion , $email) {
        $query = mysqli_query($this->conexion, "SELECT * FROM cliente WHERE identificacion = '$identificacion' AND email ='$email'");
        return mysqli_fetch_array($query);
    }

    public function verificarPermisos($id_user, $permiso) {
        $sql = mysqli_query($this->conexion, "SELECT p.*, d.* FROM permisos p 
                                              INNER JOIN detalle_permisos d ON p.id = d.id_permiso 
                                              WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
        
        return ($sql) ? mysqli_fetch_all($sql, MYSQLI_ASSOC) : []; // Retorna un array asociativo
    }

    // Inserta un nuevo cliente
    public function insertarCliente($nombre, $apellido, $identificacion, $telefono, $direccion, $email, $usuario_id) {
        // Validar el email primero
        $validacion = $this->validarEmail($email);
        
        if (!$validacion['valido']) {
            throw new Exception("Email no válido. Estado: " . $validacion['status'] . 
                              ($validacion['sub_status'] ? " (" . $validacion['sub_status'] . ")" : ""));
        }
        
        // Si el email es válido, proceder con la inserción
        $query = mysqli_query($this->conexion, "INSERT INTO cliente(nombre, apellido, identificacion, telefono, direccion, email, usuario_id) 
                                              VALUES ('$nombre', '$apellido', '$identificacion', '$telefono', '$direccion', '$email', '$usuario_id')");
        return $query;
    }

    // Obtiene todos los clientes
    public function obtenerClientes() {
        $query = mysqli_query($this->conexion, "SELECT * FROM cliente");
        return $query;
    }

    public function obtenerClientesInactivos() {
        $query = mysqli_query($this->conexion, "SELECT * FROM cliente WHERE estado = 0");
        return $query;
    }

    // Obtiene un cliente por su ID
    public function obtenerClientePorId($id) {
        $query = mysqli_query($this->conexion, "SELECT * FROM cliente WHERE idcliente = $id");
        return mysqli_fetch_assoc($query);
    }

    // Actualiza un cliente
    public function actualizarCliente($id, $nombre, $apellido, $identificacion, $telefono, $direccion, $email) {
        $query = mysqli_query($this->conexion, "UPDATE cliente SET nombre = '$nombre', apellido = '$apellido', identificacion = '$identificacion', 
                                                telefono = '$telefono', direccion = '$direccion', email = '$email' 
                                                WHERE idcliente = $id");
        return $query;
    }

    // Elimina un cliente (cambia su estado a inactivo)
    public function eliminarCliente($id) {
        $query = mysqli_query($this->conexion, "UPDATE cliente SET estado = 0 WHERE idcliente = $id");
        return $query;
    }

    public function eliminarClienteDefinitivo($id) {
        $query = mysqli_query($this->conexion, "DELETE FROM cliente WHERE idcliente = $id");
        return $query;
    }

    public function activarCliente($id) {

        $query = mysqli_query($this->conexion, "UPDATE cliente SET estado = 1 WHERE idcliente = $id");
        return $query;
    }
}
?>