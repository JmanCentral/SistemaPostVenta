
<?php include_once "includes/header.php"; ?>


<div class="table-responsive">
    <table class="table table-light" id="tbl">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Proveedor</th>
                <th>Stock Disponible</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($productosStockBajo)): ?>
                <?php foreach ($productosStockBajo as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['codproducto']) ?></td>
                        <td><?= htmlspecialchars($row['descripcion']) ?></td>
                        <td><?= htmlspecialchars($row['proveedor']) ?></td>
                        <td>
                            <span style="color: <?= $row['cantidad'] < 5 ? 'red' : 'orange' ?>; font-weight: bold;">
                                <?= htmlspecialchars($row['cantidad']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">No hay productos con stock bajo.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once "includes/footer.php"; ?>