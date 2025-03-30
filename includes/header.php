<?php session_start();
if (empty($_SESSION['active'])) {
    header('location: /');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Panel de Administración</title>
    <link href="assets/css/estilos.css" rel="stylesheet" />
    <link href="assets/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="assets/js/jquery-ui/jquery-ui.min.css">
    <script src="assets/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php?action=dashboard">FactuTiendas</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>

        <!-- Navbar-->
        <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user fa-fw"></i> 
            <?php echo isset($_SESSION['usuario']) ? "Bienvenido, " . $_SESSION['usuario'] : "Invitado"; ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#nuevo_pass">Perfil</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="index.php">Cerrar Sesión</a>
        </div>
    </li>
</ul>

    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link" href="index.php?action=nueva_venta">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Nueva venta
                        </a>
                        <a class="nav-link" href="index.php?action=configuracion">
                            <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                            Configuración
                        </a>
                        <a class="nav-link" href="index.php?action=clientes">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Clientes
                        </a>
                        <a class="nav-link" href="index.php?action=proveedores">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Proveedores
                        </a>
                        
                        <a class="nav-link" href="index.php?action=facturas">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Ventas
                        </a>

                        <a class="nav-link" href="index.php?action=utilidades">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Ganacias
                        </a>

                        <a class="nav-link" href="index.php?action=productos">
                            <div class="sb-nav-link-icon"><i class="fab fa-product-hunt"></i></div>
                            Productos
                        </a>
                        <a class="nav-link" href="index.php?action=inventario">
                            <div class="sb-nav-link-icon"><i class="fab fa-product-hunt"></i></div>
                            Inventario
                        </a>

                        <a class="nav-link" href="index.php?action=usuarios">
                            <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                            Usuarios
                        </a>

                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid mt-2">