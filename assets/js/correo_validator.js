document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar notificaciones bonitas
    function showNotification(message, type = 'success') {

        Swal.fire({
            icon: type,
            title: message,
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Manejar el envío del formulario con AJAX
    const clienteForm = document.querySelector('#nuevo_cliente form');
    if (clienteForm) {
        clienteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validación básica del email en el cliente
            const emailInput = this.querySelector('#email');
            if (emailInput && !validateEmail(emailInput.value)) {
                showNotification('Por favor ingrese un email válido', 'error');
                emailInput.focus();
                return;
            }
            
            // Mostrar loader o indicador de carga
            const submitBtn = this.querySelector('[type="submit"]');
            const originalBtnText = submitBtn.value;
            submitBtn.value = 'Guardando...';
            submitBtn.disabled = true;
            
            // Enviar datos por AJAX
            fetch('', { // La URL vacía envía a la misma página
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                showNotification(data.message, data.type);
                
                if (data.success) {
                    // Limpiar formulario y cerrar modal si fue exitoso
                    this.reset();
                    $('#nuevo_cliente').modal('hide');
                    
                    // Recargar la tabla o actualizar la lista de clientes
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                showNotification('Error en la conexión: ' + error.message, 'error');
            })
            .finally(() => {
                submitBtn.value = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Función para validar email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }
    
    // Validación en tiempo real del email
    const emailInput = document.querySelector('#email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (this.value && !validateEmail(this.value)) {
                showNotification('Por favor ingrese un email válido', 'error');
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
});