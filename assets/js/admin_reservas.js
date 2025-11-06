document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-aprobar').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      Swal.fire({
        title: 'Aprobar reserva',
        text: '¿Confirmas que deseas aprobar esta reserva?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
      }).then(res => {
        if (res.isConfirmed) sendAction(id, 'aprobar');
      });
    });
  });

  document.querySelectorAll('.btn-rechazar').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      Swal.fire({
        title: 'Rechazar reserva',
        text: '¿Confirmas que deseas rechazar esta reserva?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar'
      }).then(res => {
        if (res.isConfirmed) sendAction(id, 'rechazar');
      });
    });
  });
});

function sendAction(id_reserva, accion) {
  fetch('/controller/procesar_reserva_admin.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id_reserva=' + encodeURIComponent(id_reserva) + '&accion=' + encodeURIComponent(accion)
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      Swal.fire('Listo', data.message, 'success').then(() => {
        location.reload();
      });
    } else {
      Swal.fire('Error', data.message || 'Algo falló', 'error');
    }
  })
  .catch(() => {
    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
  });
}
