<?php include_once "includes/header.php"; ?>

<?php
$totalCantidad = 0;
$totalGanancia = 0;
$totalMargen = 0;

foreach ($ganancias as $row) {
    $totalCantidad += $row['cantidad_vendida'];
    $totalGanancia += $row['ganancia'];
    $totalMargen += $row['margen_ganancia']; // puedes ajustar esto si prefieres promedio
}
?>

<div class="table-responsive">
    <table class="table table-light" id="tbl">
        <thead class="mi-encabezado">
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad Vendida</th>
                <th>Ganancia</th>
                <th>Margen de Ganancia</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ganancias as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['codproducto']) ?></td>
                    <td><?= htmlspecialchars($row['producto']) ?></td>
                    <td><?= htmlspecialchars($row['cantidad_vendida']) ?></td>
                    <td>
                        <span style="color: green; font-weight: bold;">
                            <i class="fas fa-dollar-sign"></i> <?= number_format($row['ganancia'], 2) ?>
                        </span>
                    </td>
                    <td>
                        <span style="color: green; font-weight: bold;">
                            <i class="fas fa-dollar-sign"></i> <?= number_format($row['margen_ganancia'], 2) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>

            <!-- Fila de Totales -->
            <tr style="background-color: #f1f1f1; font-weight: bold;">
                <td colspan="2" style="text-align: right;">Totales:</td>
                <td><?= $totalCantidad ?></td>
                <td>
                    <span style="color: blue;">
                        <i class="fas fa-dollar-sign"></i> <?= number_format($totalGanancia, 2) ?>
                    </span>
                </td>
                <td>
                    <span style="color: blue;">
                        <i class="fas fa-dollar-sign"></i> <?= number_format($totalMargen, 2) ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php include_once "includes/footer.php"; ?>
