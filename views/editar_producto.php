<?php include_once "includes/header.php"; ?>

<<div class="row">
    <div class="col-lg-6 m-auto">
        <div class="card">
            <div class="card-header bg-dialog text-white">
                Modificar producto
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <?php echo isset($data['alert']) ? $data['alert'] : ''; ?>
                    <div class="form-group">
                        <label for="codigo">Código de Barras</label>
                        <input type="text" placeholder="Ingrese código de barras" name="codigo" id="codigo" class="form-control" value="<?php echo htmlspecialchars($data['codigo']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="producto">Producto</label>
                        <input type="text" class="form-control" placeholder="Ingrese nombre del producto" name="producto" id="producto" value="<?php echo htmlspecialchars($data['descripcion']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="precio_compra">Precio de Compra</label>
                        <input type="number" step="0.01" placeholder="Ingrese precio de compra" class="form-control" name="precio_compra" id="precio_compra" value="<?php echo htmlspecialchars($data['precio_compra']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="precio_venta">Precio de Venta</label>
                        <input type="number" step="0.01" placeholder="Ingrese precio de venta" class="form-control" name="precio_venta" id="precio_venta" value="<?php echo htmlspecialchars($data['precio_venta']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="imagen">Imagen del Producto</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                        <?php if (!empty($data['imagen'])) : ?>
                            <img src="<?php echo htmlspecialchars($data['imagen']); ?>" alt="Imagen actual" style="max-width: 100px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>
                    <input type="submit" value="Actualizar Producto" class="btn btn-primary">
                    <a href="index.php?action=productos" class="btn btn-danger">Atrás</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>