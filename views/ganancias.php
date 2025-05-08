<?php include_once "includes/header.php"; ?>

<div class="table-responsive">
    <table class="table table-light" id="tbl">
        <thead class="mi-encabezado">
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad Vendida</th>
                <th>Precio Compra Promedio</th>
                <th>Precio Venta Promedio</th>
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
                    <td><?= number_format($row['precio_compra_historico'], 2) ?></td>
                    <td><?= number_format($row['precio_venta_historico'], 2) ?></td>
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
        </tbody>
    </table>
</div>

<?php include_once "includes/footer.php"; ?>