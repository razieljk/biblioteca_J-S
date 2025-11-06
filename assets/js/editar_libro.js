document.addEventListener("DOMContentLoaded", () => {
  if (mensaje && tipo) {
    Swal.fire({
      icon: tipo,
      title: mensaje,
      showConfirmButton: false,
      timer: 1800,
    }).then(() => {
      if (tipo === "success") {
        window.location.href = window.location.pathname;
      }
    });
  }
});
