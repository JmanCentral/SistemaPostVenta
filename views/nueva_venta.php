<?php include_once "includes/header.php"; ?>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h4 class="text-center">Datos del Cliente</h4>
        </div>
        <div class="card">
            <div class="card-body">
                <form id="formCliente">
                    <div class="row">
                        <div class="col-lg-4">
                            <div>
                            <input type="hidden" id="id_cliente" name="id_cliente" required>
                                <label>Identificación</label>
                                <input type="text" name="identificacion_cliente" id="identificacion_cliente" class="form-control" placeholder="Ingrese DNI del cliente" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" name="nom_cliente" id="nom_cliente" class="form-control" disabled required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Apellido</label>
                                <input type="text" name="apellido_cliente" id="apellido_cliente" class="form-control" disabled required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="number" name="tel_cliente" id="tel_cliente" class="form-control" disabled required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" name="dir_cliente" id="dir_cliente" class="form-control" disabled required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Correo</label>
                                <input type="text" name="correo_cliente" id="correo_cliente" class="form-control" disabled required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                Datos Venta
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> VENDEDOR</label>
                            <p style="font-size: 16px; text-transform: uppercase; color: red;"><?php echo $_SESSION['nombre']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                Buscar Producto
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input id="producto" class="form-control" type="text" name="producto" placeholder="Ingresa el código o nombre">
                                    <div id="listaProductos" class="list-group mt-2" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><i class="fas fa-money-bill-wave"></i> Tipo de Pago</label>
                            <select id="tipo_pago" class="form-control" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Nequi">Nequi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Fecha de Venta</label>
                            <input type="datetime-local" id="fecha_venta" name="fecha_venta" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover" id="tblDetalle">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Descripción<th>Cantidad</th>
                        <th>Precio</th></th>
                        
                        <th>Precio Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="detalle_venta"></tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan=4>Total Pagar</td>
                        <td id="total_pagar">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-md-12 text-right">
        <button class="btn btn-primary" id="btn_generar"><i class="fas fa-save"></i> Generar Venta</button>
    </div>
</div>

<!-- Modal para cantidad de producto -->
<div class="modal fade" id="modalCantidad" tabindex="-1" role="dialog" aria-labelledby="modalCantidadLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCantidadLabel">Cantidad de Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCantidad">
                    <input type="hidden" id="id_producto">
                    <div class="form-group">
                        <label>Cantidad</label>
                        <input type="number" class="form-control" id="cantidad_producto" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Unitario</label>
                        <input type="number" class="form-control" id="precio_producto" step="0.01" min="0.01" disabled required>
                    </div>
                    <div class="form-group">
                        <label>Stock disponible: <span id="stock_disponible">0</span></label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnAgregarProducto">Agregar</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
