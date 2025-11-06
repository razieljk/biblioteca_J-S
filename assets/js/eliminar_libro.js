document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formEliminar");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const select = form.querySelector("select[name='id']");
    const selected = select.options[select.selectedIndex].text;

    if (!select.value) {
      Swal.fire({
        icon: "warning",
        title: "Selecciona un libro antes de eliminar",
        timer: 2000,
        showConfirmButton: false
      });
      return;
    }

    Swal.fire({
      title: `¿Eliminar "${selected}"?`,
      text: "accion no reversible",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "si, eliminar",
      cancelButtonText: "cancelar",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d"
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });

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
