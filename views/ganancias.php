<?php include_once "includes/header.php"; ?>

<div class="table-responsive">
    <table class="table table-light" id="tbl">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad Vendida</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Ganancia</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ganancias as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['codproducto']) ?></td>
                    <td><?= htmlspecialchars($row['producto']) ?></td>
                    <td><?= htmlspecialchars($row['cantidad_vendida']) ?></td>
                    <td><?= number_format($row['precio_compra'], 2) ?></td>
                    <td><?= number_format($row['precio_venta'], 2) ?></td>
                    <td>
                        <span style="color: green; font-weight: bold;">
                            <i class="fas fa-dollar-sign"></i> <?= number_format($row['ganancia'], 2) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include_once "includes/footer.php"; ?>