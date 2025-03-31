function gestionarInventario(codproducto, idproveedor, cantidad, usuario_id) {
    $.ajax({
        url: 'index.php?action=gestionar_inventario',
        type: 'POST',
        dataType: 'json',
        data: {
            codproducto: codproducto,
            idproveedor: idproveedor,
            cantidad: cantidad,
            usuario_id: usuario_id
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                });
                
                // Actualizar la vista si es necesario
                if (response.action === 'update') {
                    actualizarVistaInventario(codproducto, cantidad);
                } else if (response.action === 'insert') {
                    agregarNuevoInventarioAVista(response.data);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    showConfirmButton: true
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor',
                showConfirmButton: true
            });
        }
    });
}

// Funciones auxiliares para actualizar la vista
function actualizarVistaInventario(codproducto, nuevaCantidad) {
    // Implementa la lógica para actualizar la cantidad en la tabla
    $(`#cantidad-${codproducto}`).text(nuevaCantidad);
}

function agregarNuevoInventarioAVista(data) {
    // Implementa la lógica para agregar una nueva fila a la tabla
    const nuevaFila = `
        <tr id="fila-${data.codproducto}">
            <td>${data.codproducto}</td>
            <td>${data.nombre_producto}</td>
            <td id="cantidad-${data.codproducto}">${data.cantidad}</td>
            <td>${data.proveedor}</td>
            <td>
                <button class="btn btn-primary btn-sm" onclick="editarInventario('${data.codproducto}')">
                    <i class="fas fa-edit"></i>
                </button>
            </td>
        </tr>`;
    $('#tabla-inventario tbody').append(nuevaFila);
}


$(document).ready(function () {
    $("#formInventario").submit(function (event) {
        event.preventDefault(); // Evita la recarga de la página

        $.ajax({
            url: "index.php?action=inventario",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                Swal.fire({
                    position: 'center',
                    icon: response.status === "success" ? 'success' : 'error',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000
                });

                if (response.status === "success") {
                    setTimeout(() => {
                        location.reload(); // Recargar la página solo si fue exitoso
                    }, 2000);
                }
            },
            error: function (xhr, status, error) {
                console.log("Error AJAX:", xhr.responseText);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Error en la solicitud AJAX"
                });
            }
        });
    });
});


