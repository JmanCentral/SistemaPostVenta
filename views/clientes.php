<?php include_once "includes/header.php"; ?>

<button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#nuevo_cliente">
    <i class="fas fa-plus"></i> Nuevo Cliente
</button>


<a href="index.php?action=obtenerClienteInactivo" class="btn btn-secondary mb-2">
    <i class="fas fa-search"></i> Buscar Clientes Inactivos
</a>

<?php echo isset($alert) ? $alert : ''; ?>

<!-- Tabla de clientes -->
<div class="table-responsive">
    <table class="table table-striped table-bordered" id="tbl">
        <thead class="mi-encabezado">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Identificación</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Email</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($clientes)) : ?>
                <?php while ($data = mysqli_fetch_assoc($clientes)) : ?>
                    <tr>
                        <td><?php echo $data['idcliente']; ?></td>
                        <td><?php echo $data['nombre']; ?></td>
                        <td><?php echo $data['apellido']; ?></td>
                        <td><?php echo $data['identificacion']; ?></td>
                        <td><?php echo $data['telefono']; ?></td>
                        <td><?php echo $data['direccion']; ?></td>
                        <td><?php echo $data['email']; ?></td>
                        <td><?php echo ($data['estado'] == 1) ? '<span class="badge badge-pill badge-success">Activo</span>' : '<span class="badge badge-pill badge-danger">Inactivo</span>'; ?></td>
                        <td>
    <?php if ($data['estado'] == 1) : ?>
        <a href="index.php?action=editar_cliente&id=<?php echo $data['idcliente']; ?>" class="btn btn-success">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="index.php?action=eliminar_cliente&id=<?php echo $data['idcliente']; ?>" class="btn btn-danger">
            <i class="fas fa-trash"></i> Eliminar
        </a>
    <?php else : ?>
        <a href="index.php?action=activarCliente&id=<?php echo $data['idcliente']; ?>" class="btn btn-primary">
            <i class="fas fa-check"></i> Activar
        </a>
    <?php endif; ?>
</td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="9">No se encontraron clientes.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para nuevo cliente -->
<div id="nuevo_cliente" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dialog text-white">
                <h5 class="modal-title" id="my-modal-title">Nuevo Cliente</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" placeholder="Ingrese Nombre" name="nombre" id="nombre" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" placeholder="Ingrese Apellido" name="apellido" id="apellido" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="identificacion">Identificación</label>
                        <input type="text" id="identificacion" name="identificacion" class="form-control" placeholder="Ingrese identificación">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="number" placeholder="Ingrese Teléfono" name="telefono" id="telefono" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" placeholder="Ingrese Dirección" name="direccion" id="direccion" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" placeholder="Ingrese Email" name="email" id="email" class="form-control">
                    </div>
                    <input type="submit" value="Guardar Cliente" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>