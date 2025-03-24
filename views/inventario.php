<?php include_once "includes/header.php"; ?>

<button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#nuevo_inventario">
    <i class="fas fa-plus"></i> Nuevo Registro de Inventario
</button>

<?php echo isset($data['alert']) ? $data['alert'] : ''; ?>

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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['inventario'])) : ?>
                <?php while ($row = mysqli_fetch_assoc($data['inventario'])) : ?>
                    <tr>
                        <td><?php echo $row['idinventario']; ?></td>
                        <td><?php echo $row['producto']; ?></td>
                        <td><?php echo $row['proveedor']; ?></td>
                        <td><?php echo $row['cantidad']; ?></td>
                        <td><?php echo $row['fecha_ingreso']; ?></td>
                        <td>
                            <a href="index.php?action=editar_inventario&id=<?php echo $row['idinventario']; ?>" class="btn btn-success">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="index.php?action=eliminar_inventario&id=<?php echo $row['idinventario']; ?>" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6">No se encontraron registros de inventario.</td>
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
                            <?php while ($producto = mysqli_fetch_assoc($data['productos'])) : ?>
                                <option value="<?php echo $producto['codproducto']; ?>"><?php echo $producto['descripcion']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idproveedor">Proveedor</label>
                        <select name="idproveedor" id="idproveedor" class="form-control" required>
                            <option value="">Seleccione un proveedor</option>
                            <?php while ($proveedor = mysqli_fetch_assoc($data['proveedores'])) : ?>
                                <option value="<?php echo $proveedor['idproveedor']; ?>"><?php echo $proveedor['nombre']; ?></option>
                            <?php endwhile; ?>
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