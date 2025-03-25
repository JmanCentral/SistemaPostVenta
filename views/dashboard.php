

<?php include_once 'includes/header.php'; ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray">Panel de Administración</h1>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Usuarios -->
    <a class="col-xl-3 col-md-6 mb-4" href="index.php?action=usuarios">
        <div class="card border-left-primary shadow h-100 py-2 bg-warning">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Usuarios</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $data['totalU']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <!-- Clientes -->
    <a class="col-xl-3 col-md-6 mb-4" href="index.php?action=clientes">
        <div class="card border-left-success shadow h-100 py-2 bg-success">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Clientes</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $data['totalC']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <!-- Productos -->
    <a class="col-xl-3 col-md-6 mb-4" href="index.php?action=productos">
        <div class="card border-left-info shadow h-100 py-2 bg-primary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Productos</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-white"><?php echo $data['totalP']; ?></div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <!-- Ventas -->
    <a class="col-xl-3 col-md-6 mb-4" href="index.php?action=ventas">
        <div class="card border-left-warning bg-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Ventas</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $data['totalV']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-white-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <!-- Proveedores -->
    <a class="col-xl-3 col-md-6 mb-4" href="index.php?action=proveedores">
        <div class="card border-left-secondary shadow h-100 py-2 bg-secondary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Proveedores</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $data['totalProv']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <!-- Inventario -->
    <a class="col-xl-3 col-md-6 mb-4" href="index.php?action=inventario">
        <div class="card border-left-info shadow h-100 py-2 bg-info">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Registros de Inventario</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $data['totalInv']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <!-- Productos con stock mínimo -->
    <a class="col-xl-3 col-md-6 mb-4" href="index.php?action=inventario">
        <div class="card border-left-danger shadow h-100 py-2 bg-danger">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Productos con Stock Mínimo</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo count($data['productos_stock_minimo']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Gráficos -->
<div class="row">
    <!-- Gráfico de productos con stock mínimo -->
    <div class="col-lg-6">
        <div class="au-card m-b-30">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-40">Productos con stock mínimo</h3>
                <canvas id="stockMinimoChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de productos más vendidos -->
    <div class="col-lg-6">
        <div class="au-card m-b-30">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-40">Productos más vendidos</h3>
                <canvas id="productosVendidosChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de ventas por fecha -->
    <div class="col-lg-12">
        <div class="au-card m-b-30">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-40">Ventas por Fecha</h3>
                <canvas id="ventasPorFechaChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12">
        <div class="au-card m-b-30">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-40">Ganancias por producto</h3>
                <canvas id="GananciasChart"></canvas>
            </div>
        </div>
    </div>
</div>


<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Script para los gráficos -->
<script>
    // Datos para el gráfico de productos con stock mínimo
    const productosStockMinimo = <?php echo json_encode($data['productos_stock_minimo']); ?>;
    const labelsStockMinimo = productosStockMinimo.map(producto => producto.descripcion);
    const dataStockMinimo = productosStockMinimo.map(producto => producto.existencia);

    // Crear el gráfico de barras para productos con stock mínimo
    const ctxStockMinimo = document.getElementById('stockMinimoChart').getContext('2d');
    const stockMinimoChart = new Chart(ctxStockMinimo, {
        type: 'bar',
        data: {
            labels: labelsStockMinimo,
            datasets: [{
                label: 'Existencia',
                data: dataStockMinimo,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad en Stock'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Productos'
                    }
                }
            }
        }
    });

    // Datos para el gráfico de productos más vendidos
    const productosVendidos = <?php echo json_encode($data['productos_vendidos']); ?>;
    const labelsVendidos = productosVendidos.map(producto => producto.descripcion);
    const dataVendidos = productosVendidos.map(producto => producto.total_vendido);

    // Crear el gráfico de barras para productos más vendidos
    const ctxVendidos = document.getElementById('productosVendidosChart').getContext('2d');
    const productosVendidosChart = new Chart(ctxVendidos, {
        type: 'bar',
        data: {
            labels: labelsVendidos,
            datasets: [{
                label: 'Total Vendido',
                data: dataVendidos,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad Vendida'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Productos'
                    }
                }
            }
        }
    });

    // Datos para el gráfico de ventas por fecha
    const ventasPorFecha = <?php echo json_encode($data['ventas_fecha']); ?>;
    const labelsVentas = ventasPorFecha.map(venta => venta.fecha);
    const dataVentas = ventasPorFecha.map(venta => venta.total_ventas);

    // Crear el gráfico de líneas para ventas por fecha
    const ctxVentas = document.getElementById('ventasPorFechaChart').getContext('2d');
    const ventasPorFechaChart = new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: labelsVentas,
            datasets: [{
                label: 'Total de Ventas',
                data: dataVentas,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total de Ventas'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Fecha'
                    }
                }
            }
        }
    });

    


</script>

<?php include_once "includes/footer.php"; ?>