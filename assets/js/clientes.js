// Función para eliminar/desactivar
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Qué acción deseas realizar?',
        html: `<b>Opciones:</b><br>
              <span class="text-danger">Eliminar: Borrado permanente</span><br>
              <span class="text-warning">Desactivar: Se puede reactivar después</span>`,
        icon: 'question',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash"></i> Eliminar',
        denyButtonText: '<i class="fas fa-ban"></i> Desactivar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        confirmButtonColor: '#dc3545',
        denyButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // Eliminación definitiva
            realizarAccion(id, 'eliminar');
        } else if (result.isDenied) {
            // Desactivar
            realizarAccion(id, 'desactivar');
        }
    });
}

// Función para activar
function activarCliente(id) {
    Swal.fire({
        title: '¿Reactivar este cliente?',
        text: 'El cliente volverá a estar disponible',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Sí, activar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            realizarAccion(id, 'activar');
        }
    });
}

// Función común para todas las acciones AJAX
// Función mejorada para manejar errores
async function realizarAccion(id, accion) {
    try {
        const response = await fetch(`index.php?action=${accion === 'activar' ? 'activarCliente' : 'eliminar_cliente'}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ id, tipo: accion })
        });
        
        if (!response.ok) throw new Error('Error HTTP');
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Acción fallida');
        }
        
        await Swal.fire({
            icon: 'success',
            title: getSuccessMessage(data.action || accion),
            timer: 1500,
            showConfirmButton: false
        });
        
        // Actualización optimizada
        if (window.tablaClientes && typeof window.tablaClientes.ajax.reload === 'function') {
            window.tablaClientes.ajax.reload(null, false);
        } else {
            window.location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error en la conexión',
            timer: 3000
        });
    }
}

// Mensajes personalizados
function getSuccessMessage(accion) {
    const messages = {
        'eliminar': 'Cliente eliminado permanentemente',
        'desactivar': 'Cliente desactivado correctamente',
        'activar': 'Cliente reactivado con éxito'
    };
    return messages[accion] || 'Acción completada';
}