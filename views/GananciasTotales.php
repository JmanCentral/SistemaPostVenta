<?php include_once "includes/header.php"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ganancias Totales</title>
    <!-- Incluye Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
    <h1 class="mb-4 text-center">Resumen de Ganancias Totales</h1>

    <div class="row g-4">
        <!-- Cantidad Vendida -->
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 shadow">
                <div class="card-header text-center">Cantidad Vendida</div>
                <div class="card-body">
                    <h5 class="card-title text-center"><?php echo $cantidadVendidaTotal; ?> unidades</h5>
                </div>
            </div>
        </div>

        <!-- Precio de Compra -->
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3 shadow">
                <div class="card-header text-center">Total en Compras</div>
                <div class="card-body">
                    <h5 class="card-title text-center">$<?php echo number_format($precioCompraTotal, 2); ?></h5>
                </div>
            </div>
        </div>

        <!-- Precio de Venta -->
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3 shadow">
                <div class="card-header text-center">Total en Ventas</div>
                <div class="card-body">
                    <h5 class="card-title text-center">$<?php echo number_format($precioVentaTotal, 2); ?></h5>
                </div>
            </div>
        </div>

        <!-- Ganancia -->
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3 shadow">
                <div class="card-header text-center">Ganancia Total</div>
                <div class="card-body">
                    <h5 class="card-title text-center">$<?php echo number_format($gananciaTotal, 2); ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluye Bootstrap JS (opcional, para componentes que lo necesiten) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php include_once "includes/footer.php"; ?>