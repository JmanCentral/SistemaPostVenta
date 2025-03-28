<?php include_once "includes/header.php"; ?>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($ventas)): ?>
<table class="table table-light" id="tbl">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ventas as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['total']); ?></td>
                <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                <td>
                    <a href="index.php?action=generar_pdf_fisico&cl=<?php echo $row['id_cliente'] ?>&v=<?php echo $row['id'] ?>" 
                       target="_blank" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    <form method="POST" action="index.php?action=delete_factura" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-warning" 
                                onclick="return confirm('¿Estás seguro de eliminar esta venta?');">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <div class="alert alert-info">No hay facturas registradas</div>
<?php endif; ?>

<?php include_once "includes/footer.php"; ?>