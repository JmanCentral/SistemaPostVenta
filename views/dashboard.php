

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
    <a class="col-xl-3 col-md-6 mb-4" href="index.php?action=facturas">
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
    <a class="col-xl-3 col-md-6 mb-4" href="index.php?action=stock">
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

<style>
    .chart-container {
        position: relative;
        height: 300px; /* Altura fija para ambos gráficos */
        width: 100%;
    }
</style>

<!-- Gráficos -->
<div class="row">
    <!-- Fila 1 -->
    <div class="col-lg-6">
        <div class="au-card m-b-30 chart-card">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-20">Productos con stock mínimo</h3>
                <div class="chart-container">
                    <canvas id="stockMinimoChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="au-card m-b-30 chart-card">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-20">Productos más vendidos</h3>
                <div class="chart-container">
                    <canvas id="productosVendidosChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Fila 2 -->
    <div class="col-lg-6">
        <div class="au-card m-b-30 chart-card">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-20">Ventas por Fecha</h3>
                <div class="chart-container">
                    <canvas id="ventasPorFechaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="au-card m-b-30 chart-card">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-20">Ganancias por producto</h3>
                <div class="chart-container">
                    <canvas id="GananciasChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .chart-card {
        height: 100%;
    }
    
    .chart-container {
        position: relative;
        height: 300px; /* Altura fija para todos los gráficos */
        width: 100%;
    }
    
    .au-card-inner {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .title-2 {
        flex-shrink: 0;
    }
</style>

<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Configuración común para todos los gráficos
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    };

    // 1. Gráfico de productos con stock mínimo
    const productosStockMinimo = <?php echo json_encode($data['productos_stock_minimo']); ?>;
    const stockMinimoChart = new Chart(
        document.getElementById('stockMinimoChart').getContext('2d'), 
        {
            type: 'bar',
            data: {
                labels: productosStockMinimo.map(p => p.descripcion),
                datasets: [{
                    label: 'Existencia',
                    data: productosStockMinimo.map(p => p.existencia),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Cantidad en Stock' }
                    },
                    x: {
                        title: { display: true, text: 'Productos' }
                    }
                }
            }
        }
    );

    // 2. Gráfico de productos más vendidos
    const productosVendidos = <?php echo json_encode($data['productos_vendidos']); ?>;
    const productosVendidosChart = new Chart(
        document.getElementById('productosVendidosChart').getContext('2d'), 
        {
            type: 'bar',
            data: {
                labels: productosVendidos.map(p => p.descripcion),
                datasets: [{
                    label: 'Total Vendido',
                    data: productosVendidos.map(p => p.total_vendido),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Cantidad Vendida' }
                    },
                    x: {
                        title: { display: true, text: 'Productos' }
                    }
                }
            }
        }
    );

    // 3. Gráfico de ventas por fecha
    const ventasPorFecha = <?php echo json_encode($data['ventas_fecha']); ?>;
    const ventasPorFechaChart = new Chart(
        document.getElementById('ventasPorFechaChart').getContext('2d'), 
        {
            type: 'line',
            data: {
                labels: ventasPorFecha.map(v => v.fecha),
                datasets: [{
                    label: 'Total de Ventas',
                    data: ventasPorFecha.map(v => v.total_ventas),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Total de Ventas' }
                    },
                    x: {
                        title: { display: true, text: 'Fecha' }
                    }
                }
            }
        }
    );

    // 4. Gráfico de ganancias por producto
    const gananciasProductos = <?php echo json_encode($data['ganancias']); ?>;
    const GananciasChart = new Chart(
        document.getElementById('GananciasChart').getContext('2d'), 
        {
            type: 'doughnut',
            data: {
                labels: gananciasProductos.map(p => p.descripcion),
                datasets: [{
                    label: 'Ganancia',
                    data: gananciasProductos.map(p => p.ganancia),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                cutout: '60%',
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: 'Ganancias por Producto'
                    }
                }
            }
        }
    );
</script>

<?php include_once "includes/footer.php"; ?>