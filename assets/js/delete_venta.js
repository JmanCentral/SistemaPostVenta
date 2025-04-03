function eliminarVenta(id) {
    console.log("ID a eliminar:", id);

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('index.php?action=deleteVenta', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ id: id })
            })
            .then(response => {
                console.log("Estado HTTP:", response.status);
                if (!response.ok) {
                    throw new Error("Error en la respuesta del servidor");
                }
                return response.json();
            })
            .then(data => {
                console.log("Respuesta JSON:", data);
                if (data.success) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Venta eliminada correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    setTimeout(() => {
                        window.location.reload(); // Refrescar la página sin abrir nueva ventana
                    }, 2000);
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error',
                    text: "Hubo un problema con la solicitud",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        }
    });
}
