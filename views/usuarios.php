<?php include_once "includes/header.php"; ?>

<!-- Botón para agregar nuevo usuario -->
<button class="btn-usuario" type="button" data-toggle="modal" data-target="#nuevo_usuario">
    <i class="fas fa-plus"></i> Nuevo Usuario
</button>

<!-- Modal para nuevo usuario -->
<div id="nuevo_usuario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="my-modal-title">Nuevo Usuario</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- El formulario debe enviar los datos al controlador -->
                <form action="index.php?action=usuarios" method="post" autocomplete="off">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombre" id="nombre">
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" class="form-control" placeholder="Ingrese Correo Electrónico" name="correo" id="correo">
                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuario</label>
                        <input type="text" class="form-control" placeholder="Ingrese Usuario" name="usuario" id="usuario">
                    </div>
                    <div class="form-group">
                        <label for="clave">Contraseña</label>
                        <input type="password" class="form-control" placeholder="Ingrese Contraseña" name="clave" id="clave">
                    </div>
                    <input type="submit" value="Registrar" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de usuarios -->
<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered mt-2" id="tbl">
        <thead class="mi-encabezado">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)) : ?>
                <?php foreach ($usuarios as $data) : ?>
                    <tr>
                        <td><?php echo $data['idusuario']; ?></td>
                        <td><?php echo $data['nombre']; ?></td>
                        <td><?php echo $data['correo']; ?></td>
                        <td><?php echo $data['usuario']; ?></td>
                        <td><?php echo ($data['estado'] == 1) ? '<span class="badge badge-pill badge-success">Activo</span>' : '<span class="badge badge-pill badge-danger">Inactivo</span>'; ?></td>
                        <td>
                            <?php if ($data['estado'] == 1) : ?>
                                <!-- Botón para editar -->
                                <a href="index.php?action=editar_usuario&id=<?php echo $data['idusuario']; ?>" class="btn btn-success">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <!-- Botón para eliminar -->
                                <a href="index.php?action=eliminar_usuario&id=<?php echo $data['idusuario']; ?>" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                                <!-- Botón para permisos -->
                                <a href="index.php?action=editar_permisos&id=<?php echo $data['idusuario']; ?>" class="btn btn-info">
                                    <i class="fas fa-key"></i> Permisos
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6">No se encontraron usuarios.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once "includes/footer.php"; ?>