<?php
use PHPUnit\Framework\TestCase;

require_once 'models/PermisoModel.php';
require_once 'config/database.php';

class PermisoModelTest extends TestCase {
    private $permisoModel;
    private $testUserId;

    protected function setUp(): void {
        $this->permisoModel = new PermisoModel();
        
        // Crear un usuario de prueba para los tests
        $this->testUserId = $this->createTestUser();
    }

    protected function tearDown(): void {
        // Limpiar datos de prueba
        if ($this->testUserId) {
            $this->cleanupTestData($this->testUserId);
        }
    }

    private function createTestUser() {
        // Conectar a la base de datos para crear usuario de prueba
        $conexion = getConnection();
        
        $username = 'test_user_' . uniqid();
        $email = 'test_' . uniqid() . '@example.com';
        
        // Usar la estructura correcta de la tabla usuario
        $query = "INSERT INTO usuario (nombre, apellido, correo, usuario, clave, estado) 
                 VALUES ('Test', 'User', '$email', '$username', 'password123', 1)";
        
        mysqli_query($conexion, $query);
        return mysqli_insert_id($conexion);
    }

    private function cleanupTestData($userId) {
        $conexion = getConnection();
        // Eliminar permisos de prueba
        mysqli_query($conexion, "DELETE FROM detalle_permisos WHERE id_usuario = $userId");
        // Eliminar usuario de prueba
        mysqli_query($conexion, "DELETE FROM usuario WHERE idusuario = $userId");
    }

    public function testObtenerPermisos() {
        $result = $this->permisoModel->obtenerPermisos();
        
        $this->assertInstanceOf(mysqli_result::class, $result, 
            "Debería retornar un objeto mysqli_result");
        
        $numRows = mysqli_num_rows($result);
        $this->assertGreaterThanOrEqual(0, $numRows, 
            "Debería retornar cero o más filas");
        
        // Verificar que hay permisos en la base de datos (según el SQL hay 9)
        if ($numRows > 0) {
            $this->assertEquals(9, $numRows, "Debería haber 9 permisos según la base de datos");
        }
    }

    public function testObtenerUsuario() {
        // Test con usuario existente
        $usuario = $this->permisoModel->obtenerUsuario($this->testUserId);
        
        $this->assertIsArray($usuario, "Debería retornar un array");
        $this->assertArrayHasKey('idusuario', $usuario);
        $this->assertArrayHasKey('nombre', $usuario);
        $this->assertArrayHasKey('apellido', $usuario);
        $this->assertArrayHasKey('correo', $usuario);
        $this->assertArrayHasKey('usuario', $usuario);
        $this->assertArrayHasKey('clave', $usuario);
        
        // Test con usuario inexistente
        $usuarioInexistente = $this->permisoModel->obtenerUsuario(999999);
        $this->assertNull($usuarioInexistente, 
            "Debería retornar null para usuario inexistente");
    }

    public function testObtenerPermisosUsuario() {
        // Primero asignar algunos permisos de prueba (usando permisos existentes 1, 2, 3)
        $permisosTest = [1, 2, 3];
        $this->permisoModel->asignarPermisosUsuario($this->testUserId, $permisosTest);
        
        // Obtener permisos del usuario
        $permisosUsuario = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        
        $this->assertIsArray($permisosUsuario, "Debería retornar un array");
        
        // Verificar que los permisos asignados estén presentes
        foreach ($permisosTest as $permisoId) {
            $this->assertArrayHasKey($permisoId, $permisosUsuario, 
                "El permiso $permisoId debería estar asignado al usuario");
            $this->assertTrue($permisosUsuario[$permisoId], 
                "El valor del permiso debería ser true");
        }
        
        // Test con usuario sin permisos (crear otro usuario)
        $userIdSinPermisos = $this->createTestUser();
        $permisosVacios = $this->permisoModel->obtenerPermisosUsuario($userIdSinPermisos);
        $this->assertEmpty($permisosVacios, 
            "Debería retornar array vacío para usuario sin permisos");
        
        $this->cleanupTestData($userIdSinPermisos);
    }

    public function testEliminarPermisosUsuario() {
        // Primero asignar permisos
        $permisosTest = [1, 2];
        $this->permisoModel->asignarPermisosUsuario($this->testUserId, $permisosTest);
        
        // Verificar que se asignaron
        $permisosAntes = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        $this->assertCount(2, $permisosAntes, 
            "Debería tener 2 permisos antes de eliminar");
        
        // Eliminar permisos
        $this->permisoModel->eliminarPermisosUsuario($this->testUserId);
        
        // Verificar que se eliminaron
        $permisosDespues = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        $this->assertEmpty($permisosDespues, 
            "Debería retornar array vacío después de eliminar permisos");
    }

    public function testAsignarPermisosUsuario() {
        $permisosTest = [1, 3, 5]; // IDs de permisos existentes
        
        // Asignar permisos
        $this->permisoModel->asignarPermisosUsuario($this->testUserId, $permisosTest);
        
        // Verificar que se asignaron correctamente
        $permisosAsignados = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        
        $this->assertCount(3, $permisosAsignados, 
            "Debería tener 3 permisos asignados");
        
        foreach ($permisosTest as $permisoId) {
            $this->assertArrayHasKey($permisoId, $permisosAsignados, 
                "El permiso $permisoId debería estar asignado");
        }
        
        // Test de reassignación (debería eliminar los anteriores y poner nuevos)
        $nuevosPermisos = [2, 4];
        $this->permisoModel->eliminarPermisosUsuario($this->testUserId);
        $this->permisoModel->asignarPermisosUsuario($this->testUserId, $nuevosPermisos);
        
        $permisosFinales = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        $this->assertCount(2, $permisosFinales, 
            "Debería tener 2 permisos después de reassignación");
    }

    public function testFlujoCompletoPermisos() {
        // Test del flujo completo: asignar -> verificar -> eliminar -> verificar
        $permisosIniciales = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        $this->assertEmpty($permisosIniciales, 
            "Usuario nuevo debería empezar sin permisos");
        
        // Asignar permisos
        $permisosAsignar = [1, 2, 3];
        $this->permisoModel->asignarPermisosUsuario($this->testUserId, $permisosAsignar);
        
        // Verificar asignación
        $permisosDespuesAsignar = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        $this->assertCount(3, $permisosDespuesAsignar);
        
        // Eliminar permisos
        $this->permisoModel->eliminarPermisosUsuario($this->testUserId);
        
        // Verificar eliminación
        $permisosFinales = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        $this->assertEmpty($permisosFinales, 
            "Debería quedar sin permisos después de eliminar");
    }

    public function testAsignarPermisosInexistentes() {
        // El sistema actual permite asignar permisos aunque no existan en la tabla permisos
        // porque no hay restricción de clave foránea. Este test verifica ese comportamiento.
        
        $permisosInexistentes = [999, 1000];
        
        // El sistema actualmente permite asignar estos permisos
        $this->permisoModel->asignarPermisosUsuario($this->testUserId, $permisosInexistentes);
        
        $permisos = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        
        // Verificar que se asignaron los permisos (aunque no existan en la tabla permisos)
        $this->assertCount(2, $permisos, 
            "El sistema permite asignar permisos aunque no existan en la tabla permisos");
        
        foreach ($permisosInexistentes as $permisoId) {
            $this->assertArrayHasKey($permisoId, $permisos, 
                "El permiso inexistente $permisoId fue asignado");
        }
        
        // Limpiar estos permisos de prueba
        $this->permisoModel->eliminarPermisosUsuario($this->testUserId);
    }

    public function testAsignarPermisosMixtos() {
        // Test para asignar permisos existentes e inexistentes mezclados
        $permisosMixtos = [1, 999, 2, 1000]; // 1 y 2 existen, 999 y 1000 no
        
        $this->permisoModel->asignarPermisosUsuario($this->testUserId, $permisosMixtos);
        
        $permisos = $this->permisoModel->obtenerPermisosUsuario($this->testUserId);
        
        // Todos los permisos deberían estar asignados
        $this->assertCount(4, $permisos, 
            "Deberían asignarse todos los permisos (existentes e inexistentes)");
        
        foreach ($permisosMixtos as $permisoId) {
            $this->assertArrayHasKey($permisoId, $permisos, 
                "El permiso $permisoId debería estar asignado");
        }
        
        // Limpiar
        $this->permisoModel->eliminarPermisosUsuario($this->testUserId);
    }
}