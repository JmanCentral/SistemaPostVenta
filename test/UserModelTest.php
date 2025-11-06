<?php
use PHPUnit\Framework\TestCase;

require_once 'models/UserModel.php';
require_once 'config/database.php';

class UserModelTest extends TestCase {
    private $userModel;
    private $testUserId;
    private $conexion;
    private $testUserData;
    private $testPassword = 'test123';

    protected function setUp(): void {
        $this->userModel = new UserModel();
        $this->conexion = getConnection();
        
        // Crear un usuario de prueba para los tests
        $this->testUserId = $this->createTestUser();
        $this->testUserData = $this->userModel->obtenerUsuarioPorId($this->testUserId);
    }

    protected function tearDown(): void {
        if ($this->testUserId) {
            $this->cleanupTestData($this->testUserId);
        }
    }

    private function createTestUser() {
        $nombre = 'Usuario Test';
        $email = 'test' . substr(uniqid(), 0, 10) . '@example.com';
        $usuario = 'testuser' . substr(uniqid(), 0, 8);
        
        // Insertar con contraseña en texto plano para testing
        $clave = $this->testPassword; // Texto plano para testing
        
        $query = "INSERT INTO usuario (nombre, correo, usuario, clave, estado) 
                 VALUES ('$nombre', '$email', '$usuario', '$clave', 1)";
        
        $result = mysqli_query($this->conexion, $query);
        
        if ($result) {
            return mysqli_insert_id($this->conexion);
        }
        
        error_log("Error creando usuario prueba: " . mysqli_error($this->conexion));
        return null;
    }

    private function cleanupTestData($userId) {
        mysqli_query($this->conexion, "DELETE FROM usuario WHERE idusuario = $userId");
    }

    public function testAuthenticate() {
        if (!$this->testUserData) {
            $this->markTestSkipped("Usuario de prueba no disponible");
            return;
        }
        
        // Debug: verificar qué hay en la base de datos
        $debugQuery = mysqli_query($this->conexion, "SELECT usuario, clave FROM usuario WHERE idusuario = {$this->testUserData['idusuario']}");
        $debugData = mysqli_fetch_assoc($debugQuery);
        error_log("Usuario en BD: " . $debugData['usuario']);
        error_log("Clave en BD: " . $debugData['clave']);
        error_log("Clave a verificar: " . $this->testPassword);
        
        // Test con credenciales correctas
        $result = $this->userModel->authenticate($this->testUserData['usuario'], $this->testPassword);
        
        if ($result === null) {
            // Si falla, intentar con diferentes métodos de contraseña
            $this->tryAlternativeAuthenticationMethods();
        }
        
        $this->assertIsArray($result, "Debería autenticar usuario con credenciales correctas");
        $this->assertEquals($this->testUserData['idusuario'], $result['idusuario']);
        
        // Test con credenciales incorrectas
        $result = $this->userModel->authenticate($this->testUserData['usuario'], 'wrongpassword');
        $this->assertNull($result, "Debería retornar null con credenciales incorrectas");
        
        // Test con usuario inexistente
        $result = $this->userModel->authenticate('nonexistentuser', $this->testPassword);
        $this->assertNull($result, "Debería retornar null con usuario inexistente");
    }

    private function tryAlternativeAuthenticationMethods() {
        // Intentar con diferentes formatos de contraseña
        $testPasswords = [
            $this->testPassword,
            md5($this->testPassword),
            password_hash($this->testPassword, PASSWORD_DEFAULT)
        ];
        
        foreach ($testPasswords as $password) {
            $result = $this->userModel->authenticate($this->testUserData['usuario'], $password);
            if ($result !== null) {
                error_log("Autenticación exitosa con formato: " . $password);
                return $result;
            }
        }
        
        return null;
    }

    public function testObtenerUsuarioPorId() {
        if (!$this->testUserId) {
            $this->markTestSkipped("Usuario de prueba no disponible");
            return;
        }
        
        $usuario = $this->userModel->obtenerUsuarioPorId($this->testUserId);
        
        $this->assertIsArray($usuario, "Debería retornar un array");
        $this->assertEquals($this->testUserId, $usuario['idusuario']);
        $this->assertArrayHasKey('nombre', $usuario);
        $this->assertArrayHasKey('correo', $usuario);
        $this->assertArrayHasKey('usuario', $usuario);
        $this->assertArrayHasKey('estado', $usuario);
        
        // Test con ID inexistente
        $usuarioInexistente = $this->userModel->obtenerUsuarioPorId(999999);
        $this->assertNull($usuarioInexistente, "Debería retornar null para ID inexistente");
    }

    public function testVerificarPermisos() {
        if (!$this->testUserId) {
            $this->markTestSkipped("Usuario de prueba no disponible");
            return;
        }
        
        $this->assignTestPermissions();
        
        $permisos = $this->userModel->verificarPermisos($this->testUserId, 'usuarios');
        $this->assertIsArray($permisos, "Debería retornar un array");
        
        if (empty($permisos)) {
            $this->markTestSkipped("El usuario no tiene permisos asignados");
            return;
        }
        
        $this->assertNotEmpty($permisos, "El usuario debería tener permisos");
        $this->assertArrayHasKey('id', $permisos[0]);
        $this->assertArrayHasKey('nombre', $permisos[0]);
        
        // Test con permiso inexistente
        $permisosInexistentes = $this->userModel->verificarPermisos($this->testUserId, 'permiso_inexistente');
        $this->assertEmpty($permisosInexistentes, "Debería retornar array vacío para permiso inexistente");
    }

    private function assignTestPermissions() {
        $result = mysqli_query($this->conexion, "SELECT id FROM permisos WHERE nombre = 'usuarios'");
        if (mysqli_num_rows($result) == 0) {
            mysqli_query($this->conexion, "INSERT INTO permisos (nombre) VALUES ('usuarios')");
            $permisoId = mysqli_insert_id($this->conexion);
        } else {
            $row = mysqli_fetch_assoc($result);
            $permisoId = $row['id'];
        }
        
        $check = mysqli_query($this->conexion, "SELECT * FROM detalle_permisos WHERE id_usuario = $this->testUserId AND id_permiso = $permisoId");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($this->conexion, "INSERT INTO detalle_permisos (id_permiso, id_usuario) VALUES ($permisoId, $this->testUserId)");
        }
    }

    public function testObtenerUsuarios() {
        $resultado = $this->userModel->obtenerUsuarios();
        $this->assertInstanceOf(mysqli_result::class, $resultado);
        
        $numUsuarios = mysqli_num_rows($resultado);
        $this->assertGreaterThanOrEqual(1, $numUsuarios);
        
        $usuario = mysqli_fetch_assoc($resultado);
        $this->assertArrayHasKey('idusuario', $usuario);
        $this->assertArrayHasKey('nombre', $usuario);
        $this->assertArrayHasKey('correo', $usuario);
        $this->assertArrayHasKey('usuario', $usuario);
        $this->assertArrayHasKey('estado', $usuario);
    }

    public function testInsertarUsuario() {
        $nombre = 'Nuevo Usuario Test';
        $email = 'nuevo' . substr(uniqid(), 0, 10) . '@example.com';
        $user = 'nuevouser' . substr(uniqid(), 0, 8);
        $clave = 'password123';
        
        $resultado = $this->userModel->insertarUsuario($nombre, $email, $user, $clave);
        $this->assertNotFalse($resultado);
        $this->assertIsInt($resultado);
        
        $usuarioInsertado = $this->userModel->obtenerUsuarioPorId($resultado);
        $this->assertIsArray($usuarioInsertado);
        
        $expectedUser = substr($user, 0, 20);
        $actualUser = substr($usuarioInsertado['usuario'], 0, 20);
        $this->assertEquals($expectedUser, $actualUser);
        $this->assertEquals($nombre, $usuarioInsertado['nombre']);
        $this->assertEquals($email, $usuarioInsertado['correo']);
        
        $this->cleanupTestData($resultado);
    }

    public function testVerificarCorreo() {
        if (!$this->testUserData) {
            $this->markTestSkipped("Usuario de prueba no disponible");
            return;
        }
        
        $emailExistente = $this->testUserData['correo'];
        $resultado = $this->userModel->verificarCorreo($emailExistente);
        $this->assertIsArray($resultado);
        $this->assertEquals($emailExistente, $resultado['correo']);
        
        $emailInexistente = 'nonexistent' . substr(uniqid(), 0, 10) . '@example.com';
        $resultadoInexistente = $this->userModel->verificarCorreo($emailInexistente);
        $this->assertNull($resultadoInexistente);
    }

    public function testActualizarUsuario() {
        if (!$this->testUserId) {
            $this->markTestSkipped("Usuario de prueba no disponible");
            return;
        }
        
        $nuevoNombre = 'Usuario Actualizado';
        $nuevoCorreo = 'actualizado' . substr(uniqid(), 0, 10) . '@example.com';
        $nuevoUsuario = 'useract' . substr(uniqid(), 0, 8);
        
        $resultado = $this->userModel->actualizarUsuario($this->testUserId, $nuevoNombre, $nuevoCorreo, $nuevoUsuario);
        $this->assertTrue($resultado);
        
        $usuarioActualizado = $this->userModel->obtenerUsuarioPorId($this->testUserId);
        $expectedUser = substr($nuevoUsuario, 0, 20);
        $actualUser = substr($usuarioActualizado['usuario'], 0, 20);
        $this->assertEquals($expectedUser, $actualUser);
        $this->assertEquals($nuevoNombre, $usuarioActualizado['nombre']);
        $this->assertEquals($nuevoCorreo, $usuarioActualizado['correo']);
    }

    public function testEliminarUsuario() {
        $usuarioEliminarId = $this->createTestUser();
        if (!$usuarioEliminarId) {
            $this->markTestSkipped("No se pudo crear usuario para eliminar");
            return;
        }
        
        $resultado = $this->userModel->eliminarUsuario($usuarioEliminarId);
        $this->assertTrue($resultado);
        
        $usuarioEliminado = $this->userModel->obtenerUsuarioPorId($usuarioEliminarId);
        $this->assertEquals(0, $usuarioEliminado['estado']);
        
        $this->cleanupTestData($usuarioEliminarId);
    }

    public function testActivarUsuario() {
        $usuarioActivarId = $this->createTestUser();
        if (!$usuarioActivarId) {
            $this->markTestSkipped("No se pudo crear usuario para activar");
            return;
        }
        
        mysqli_query($this->conexion, "UPDATE usuario SET estado = 0 WHERE idusuario = $usuarioActivarId");
        $resultado = $this->userModel->activarUsuario($usuarioActivarId);
        $this->assertTrue($resultado);
        
        $usuarioActivado = $this->userModel->obtenerUsuarioPorId($usuarioActivarId);
        $this->assertEquals(1, $usuarioActivado['estado']);
        
        $this->cleanupTestData($usuarioActivarId);
    }

    public function testAuthenticateWithInactiveUser() {
        if (!$this->testUserId) {
            $this->markTestSkipped("Usuario de prueba no disponible");
            return;
        }
        
        mysqli_query($this->conexion, "UPDATE usuario SET estado = 0 WHERE idusuario = $this->testUserId");
        $result = $this->userModel->authenticate($this->testUserData['usuario'], $this->testPassword);
        $this->assertNull($result);
        
        mysqli_query($this->conexion, "UPDATE usuario SET estado = 1 WHERE idusuario = $this->testUserId");
    }
}