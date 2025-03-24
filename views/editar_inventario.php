<?php include_once "includes/header.php"; ?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Modificar Registro de Inventario
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <?php echo isset($data['alert']) ? $data['alert'] : ''; ?>
                    
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" placeholder="Ingrese cantidad" name="cantidad" id="cantidad" class="form-control" value="<?php echo $data['data_inventario']['cantidad']; ?>">
                    </div>
                    <input type="submit" value="Actualizar Inventario" class="btn btn-primary">
                    <a href="index.php?action=inventario" class="btn btn-danger">Atrás</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>