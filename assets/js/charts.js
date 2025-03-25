// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que los datos están disponibles
    if (!window.chartData) {
        console.error('No se encontraron datos para los gráficos');
        return;
    }

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
    initStockMinimoChart();
    
    // 2. Gráfico de productos más vendidos
    initProductosVendidosChart();
    
    // 3. Gráfico de ventas por fecha
    initVentasPorFechaChart();
    
    // 4. Gráfico de ganancias por producto
    initGananciasChart();

    function initStockMinimoChart() {
        const ctx = document.getElementById('stockMinimoChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: window.chartData.productosStockMinimo.map(p => p.descripcion),
                datasets: [{
                    label: 'Existencia',
                    data: window.chartData.productosStockMinimo.map(p => p.existencia),
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
        });
    }

    function initProductosVendidosChart() {
        const ctx = document.getElementById('productosVendidosChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: window.chartData.productosVendidos.map(p => p.descripcion),
                datasets: [{
                    label: 'Total Vendido',
                    data: window.chartData.productosVendidos.map(p => p.total_vendido),
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
        });
    }

    function initVentasPorFechaChart() {
        const ctx = document.getElementById('ventasPorFechaChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.chartData.ventasPorFecha.map(v => v.fecha),
                datasets: [{
                    label: 'Total de Ventas',
                    data: window.chartData.ventasPorFecha.map(v => v.total_ventas),
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
        });
    }

    function initGananciasChart() {
        const ctx = document.getElementById('GananciasChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: window.chartData.gananciasProductos.map(p => p.descripcion),
                datasets: [{
                    label: 'Ganancia',
                    data: window.chartData.gananciasProductos.map(p => p.ganancia),
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
        });
    }
});