<?php include_once "includes/header.php"; ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Modificar Cliente
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <?php echo isset($data['alert']) ? $data['alert'] : ''; ?>
                        <input type="hidden" name="id" value="<?php echo $data['idcliente']; ?>">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" placeholder="Ingrese Nombre" name="nombre" class="form-control" id="nombre" value="<?php echo $data['nombre']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" placeholder="Ingrese Apellido" name="apellido" class="form-control" id="apellido" value="<?php echo $data['apellido']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="identificacion">Identificación</label>
                            <input type="text" placeholder="Ingrese Identificación" name="identificacion" class="form-control" id="identificacion" value="<?php echo $data['identificacion']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="number" placeholder="Ingrese Teléfono" name="telefono" class="form-control" id="telefono" value="<?php echo $data['telefono']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" placeholder="Ingrese Dirección" name="direccion" class="form-control" id="direccion" value="<?php echo $data['direccion']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" placeholder="Ingrese Email" name="email" class="form-control" id="email" value="<?php echo $data['email']; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-user-edit"></i> Editar Cliente</button>
                        <a href="index.php?action=clientes" class="btn btn-danger">Atrás</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php include_once "includes/footer.php"; ?>