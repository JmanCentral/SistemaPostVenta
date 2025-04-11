<?php include_once "includes/header.php"; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-dialog text-white">
                Datos de la Empresa
            </div>
            <div class="card-body">
                <form action="" method="post" class="p-3">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="hidden" name="id" value="<?php echo isset($data['id']) ? $data['id'] : ''; ?>">
                        <input type="text" name="nombre" class="form-control" value="<?php echo isset($data['nombre']) ? $data['nombre'] : ''; ?>" placeholder="Nombre de la Empresa" required>
                    </div>
                    <div class="form-group">
                        <label>NIT:</label>
                        <input type="number" name="NIT" class="form-control" value="<?php echo isset($data['NIT']) ? $data['NIT'] : ''; ?>" placeholder="Código de la empresa" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="number" name="telefono" class="form-control" value="<?php echo isset($data['telefono']) ? $data['telefono'] : ''; ?>" placeholder="Teléfono de la Empresa" required>
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico:</label>
                        <input type="email" name="email" class="form-control" value="<?php echo isset($data['email']) ? $data['email'] : ''; ?>" placeholder="Correo de la Empresa" required>
                    </div>
                    <div class="form-group">
                        <label>Dirección:</label>
                        <input type="text" name="direccion" class="form-control" value="<?php echo isset($data['direccion']) ? $data['direccion'] : ''; ?>" placeholder="Dirección de la Empresa" required>
                    </div>
                    <?php echo isset($data['alert']) ? $data['alert'] : ''; ?>
                    <div>
                        <?php if (empty($data['id'])) : ?>
                            <button type="submit" name="insertar" class="btn btn-primary"><i class="fas fa-save"></i> Insertar Datos</button>
                        <?php else : ?>
                            <button type="submit" name="modificar" class="btn btn-primary"><i class="fas fa-edit"></i> Modificar Datos</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>