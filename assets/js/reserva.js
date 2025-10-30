document.getElementById('confirmarReserva').addEventListener('click', async () => {
  const id_libro = new URLSearchParams(window.location.search).get('id_libro');

  try {
    const response = await fetch('procesar_reserva.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'id_libro=' + encodeURIComponent(id_libro)
    });

    const data = await response.json();

    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: '¡Reserva exitosa!',
        text: data.message,
        confirmButtonText: 'Aceptar'
      }).then(() => {
        window.location.href = '../dashboard.php';
      });
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Atención',
        text: data.message
      });
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error de conexión',
      text: 'No se pudo procesar la reserva.'
    });
  }
});
