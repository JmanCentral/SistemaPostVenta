<?php include_once "includes/header.php"; ?>

<!-- Botón para abrir el modal de nuevo inventario -->
<button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#nuevo_inventario">
    <i class="fas fa-plus"></i> Nuevo Registro de Inventario
</button>

<!-- Mostrar alertas -->
<?php echo isset($alert) ? $alert : ''; ?>

<!-- Tabla de inventario -->
<div class="table-responsive">
    <table class="table table-striped table-bordered" id="tbl">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Proveedor</th>
                <th>Cantidad</th>
                <th>Fecha de Ingreso</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($inventario)) : ?>
                <?php while ($data = mysqli_fetch_assoc($inventario)) : ?>
                    <tr>
                        <td><?php echo $data['idinventario']; ?></td>
                        <td><?php echo $data['producto']; ?></td>
                        <td><?php echo $data['proveedor']; ?></td>
                        <td><?php echo $data['cantidad']; ?></td>
                        <td><?php echo $data['fecha_ingreso']; ?></td>
                        <td><?php echo ($data['estado'] == 1) ? '<span class="badge badge-pill badge-success">Activo</span>' : '<span class="badge badge-pill badge-danger">Inactivo</span>'; ?></td>
                        <td>
                            <?php if ($data['estado'] == 1) : ?>
                                <a href="index.php?action=editar_inventario&id=<?php echo $data['idinventario']; ?>" class="btn btn-success">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="index.php?action=eliminar_inventario&id=<?php echo $data['idinventario']; ?>" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7">No se encontraron registros de inventario.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar nuevo registro de inventario -->
<div id="nuevo_inventario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="my-modal-title">Nuevo Registro de Inventario</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="codproducto">Producto</label>
                        <select name="codproducto" id="codproducto" class="form-control" required>
                            <option value="">Seleccione un producto</option>
                            <?php
                            $query_productos = mysqli_query($conexion, "SELECT * FROM producto WHERE estado = 1");
                            while ($producto = mysqli_fetch_assoc($query_productos)) {
                                echo '<option value="' . $producto['codproducto'] . '">' . $producto['descripcion'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idproveedor">Proveedor</label>
                        <select name="idproveedor" id="idproveedor" class="form-control" required>
                            <option value="">Seleccione un proveedor</option>
                            <?php
                            $query_proveedores = mysqli_query($conexion, "SELECT * FROM proveedores WHERE estado = 1");
                            while ($proveedor = mysqli_fetch_assoc($query_proveedores)) {
                                echo '<option value="' . $proveedor['idproveedor'] . '">' . $proveedor['nombre'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" placeholder="Ingrese cantidad" name="cantidad" id="cantidad" class="form-control" required>
                    </div>
                    <input type="submit" value="Guardar Registro" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>