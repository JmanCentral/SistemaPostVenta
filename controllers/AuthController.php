<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\UserModel;

session_start();

class AuthController {
    
    public function login() {
        // Si ya hay una sesión activa, redirigir al dashboard
        if (isset($_SESSION['active']) && $_SESSION['active'] === true) {
            header('location: index.php?action=dashboard');
            exit();
        }

        $alert = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
            if (empty($_POST['usuario']) || empty($_POST['clave'])) {
                $alert = '<div class="alert alert-danger" role="alert">Ingrese su usuario y su clave</div>';
            } else {
                $userModel = new UserModel();
                $user = $_POST['usuario'];
                $clave = $_POST['clave'];

                // Autenticar al usuario
                $resultado = $userModel->authenticate($user, $clave);

                if ($resultado) {
                    // Crear la sesión
                    $_SESSION['active'] = true;
                    $_SESSION['idUser'] = $resultado['idusuario'];
                    $_SESSION['nombre'] = $resultado['nombre'];
                    $_SESSION['user'] = $resultado['usuario'];

                    // Redirigir al dashboard
                    header('location: index.php?action=dashboard');
                    exit();
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">Usuario o Contraseña Incorrecta</div>';
                    session_destroy(); // Destruir la sesión si la autenticación falla
                }
            }
        }

        // Mostrar la vista de login
        View::render('login', ['alert' => $alert]);
    }
}