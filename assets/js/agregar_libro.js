document.addEventListener("DOMContentLoaded", () => {
  if (mensaje && tipo) {
    Swal.fire({
      icon: tipo,
      title: mensaje,
      showConfirmButton: false,
      timer: 2000
    }).then(() => {
      const url = new URL(window.location);
      url.search = "";
      window.history.replaceState({}, document.title, url);
    });
  }
});
