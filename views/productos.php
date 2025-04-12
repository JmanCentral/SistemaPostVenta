<?php include_once "includes/header.php"; ?>


<!-- Botón para abrir el modal de nuevo producto -->
<button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#nuevo_producto">
    <i class="fas fa-plus"></i> Nuevo Producto
</button>

<!-- Mostrar alertas -->
<?php echo isset($alert) ? $alert : ''; ?>

<!-- Tabla de productos -->
<div class="table-responsive">
    <table class="table table-striped table-bordered tabla-productos" id="tbl">
        <thead class="mi-encabezado">
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Producto</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Imagen</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($productos)) : ?>
                <?php while ($data = mysqli_fetch_assoc($productos)) : ?>
                    <tr>
                        <td><?php echo $data['codproducto']; ?></td>
                        <td><?php echo $data['codigo']; ?></td>
                        <td><?php echo $data['descripcion']; ?></td>
                        <td><?php echo number_format($data['precio_compra'], 2); ?></td>
                        <td><?php echo number_format($data['precio_venta'], 2); ?></td>
                        <td>
                            <?php if (!empty($data['imagen'])) : ?>
                                <img src="<?php echo $data['imagen']; ?>" alt="Imagen del producto">
                            <?php else : ?>
                                Sin imagen
                            <?php endif; ?>
                        </td>
                        <td><?php echo ($data['estado'] == 1) ? '<span class="badge badge-pill badge-success">Activo</span>' : '<span class="badge badge-pill badge-danger">Inactivo</span>'; ?></td>
                        <td>
                            <?php if ($data['estado'] == 1) : ?>
                                <a href="index.php?action=editar_producto&id=<?php echo $data['codproducto']; ?>" class="btn btn-success">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="index.php?action=eliminar_producto&id=<?php echo $data['codproducto']; ?>" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                                <?php else : ?>
                                <a href="index.php?action=activar_producto&id=<?php echo $data['codproducto']; ?>" class="btn btn-primary">
                                <i class="fas fa-check"></i> Activar
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="9">No se encontraron productos.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar nuevo producto -->
<div id="nuevo_producto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dialog text-white">
                <h5 class="modal-title" id="my-modal-title">Nuevo Producto</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="form-group">
                        <label for="codigo">Código de Barras</label>
                        <input type="text" placeholder="Ingrese código de barras" name="codigo" id="codigo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="producto">Producto</label>
                        <input type="text" placeholder="Ingrese nombre del producto" name="producto" id="producto" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="precio_compra">Precio de Compra</label>
                        <input type="number" step="0.01" placeholder="Ingrese precio de compra" class="form-control" name="precio_compra" id="precio_compra" required>
                    </div>
                    <div class="form-group">
                        <label for="precio_venta">Precio de Venta</label>
                        <input type="number" step="0.01" placeholder="Ingrese precio de venta" class="form-control" name="precio_venta" id="precio_venta" required>
                    </div>
                    <div class="form-group">
                        <label for="imagen">Imagen del Producto</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*" required>
                    </div>
                    <input type="submit" value="Guardar Producto" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>