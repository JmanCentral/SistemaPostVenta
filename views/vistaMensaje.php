<?php
$mensaje = $_GET['mensaje'] ?? "Ha ocurrido un error inesperado.";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .container {
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            width: 50%;
            margin: auto;
            border-radius: 10px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Notificación</h2>
        <p class="<?php echo strpos($mensaje, 'Error') !== false ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </p>
        <a href="index.php?action=nueva_venta">Volver al apartado de ventas</a>
        <a href="/">Descargar factura</a>
    </div>
</body>
</html>
