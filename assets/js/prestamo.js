document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formPrestamo");

    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const res = await fetch("views/procesar_prestamo.php", {
                method: "POST",
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "¡Préstamo exitoso!",
                    html: data.message,
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: "#4CAF50"
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Atención",
                    text: data.message,
                    confirmButtonColor: "#d33"
                });
            }

        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error al conectar con el servidor",
                text: error.message,
                confirmButtonColor: "#d33"
            });
        }
    });
});
