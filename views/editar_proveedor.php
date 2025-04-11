<?php include_once "includes/header.php"; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card">
                <div class="card-header bg-dialog text-white">
                    Modificar Proveedor
                </div>
                <div class="card-body">
                    <form action="index.php?action=editar_proveedor&id=<?php echo htmlspecialchars($data['idproveedor']); ?>" method="post">
                        <?php echo isset($data['alert']) ? $data['alert'] : ''; ?>
                        
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['idproveedor']); ?>">

                        <div class="form-group">
                            <label for="NIT">NIT</label>
                            <input type="text" id="NIT" name="NIT" class="form-control" placeholder="Ingrese su NIT de proveedor" 
                                value="<?php echo htmlspecialchars($data['NIT']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" placeholder="Ingrese Nombre" name="nombre" class="form-control" id="nombre" 
                                value="<?php echo htmlspecialchars($data['nombre']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" placeholder="Ingrese Apellido" name="apellido" class="form-control" id="apellido" 
                                value="<?php echo htmlspecialchars($data['apellido']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" placeholder="Ingrese Teléfono" name="telefono" class="form-control" id="telefono" 
                                value="<?php echo htmlspecialchars($data['telefono']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" placeholder="Ingrese Email" name="email" class="form-control" id="email" 
                                value="<?php echo htmlspecialchars($data['email']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" placeholder="Ingrese Dirección" name="direccion" class="form-control" id="direccion" 
                                value="<?php echo htmlspecialchars($data['direccion']); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-edit"></i> Editar Proveedor
                        </button>
                        <a href="index.php?action=proveedores" class="btn btn-danger">Atrás</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
