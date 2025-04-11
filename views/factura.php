<?php include_once "includes/header.php"; ?>



<?php if (!empty($ventas)): ?>
<table class="table table-light" id="tbl">
    <thead class="mi-encabezado">
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
            <tr id="venta-<?php echo $row['id']; ?>">
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['total']); ?></td>
                <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                <td>
                   
                    <a href="index.php?action=generar_pdf_fisico&cl=<?php echo $row['id_cliente'] ?>&v=<?php echo $row['id'] ?>" 
                       target="_blank" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    
                    <!-- Nuevo botón de eliminar con AJAX -->
                    <button onclick="eliminarVenta(<?php echo (int) $row['id']; ?>)" 
                            class="btn btn-warning">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <div class="alert alert-info">No hay facturas registradas</div>
<?php endif; ?>
           

<?php include_once "includes/footer.php"; ?>