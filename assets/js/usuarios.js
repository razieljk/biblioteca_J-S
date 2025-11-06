document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const success = urlParams.get("success");
  const error = urlParams.get("error");

  if (success === "agregado") {
    Swal.fire("Éxito", "Usuario agregado correctamente", "success");
  } else if (success === "editado") {
    Swal.fire("Éxito", "Usuario editado correctamente", "success");
  } else if (error === "email") {
    Swal.fire("Error", "Ya existe un usuario con este correo", "error");
  } else if (error === "insert") {
    Swal.fire("Error", "Hubo un problema al agregar el usuario", "error");
  } else if (error === "update") {
    Swal.fire("Error", "No se pudo actualizar el usuario", "error");
  }

  const eliminarBtns = document.querySelectorAll(".eliminar-btn");
  eliminarBtns.forEach(btn => {
    btn.addEventListener("click", e => {
      const id = btn.getAttribute("data-id");
      Swal.fire({
        title: "¿Eliminar usuario?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      }).then(result => {
        if (result.isConfirmed) {
          window.location.href = `eliminar_usuario.php?id=${id}`;
        }
      });
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const botonesEliminar = document.querySelectorAll(".eliminar-btn");

  botonesEliminar.forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.getAttribute("data-id");

      Swal.fire({
        title: "¿Eliminar usuario?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `eliminar_usuario.php?id=${id}`;
        }
      });
    });
  });

  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has("success") && urlParams.get("success") === "eliminado") {
    Swal.fire({
      icon: "success",
      title: "Usuario eliminado",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (urlParams.has("error") && urlParams.get("error") === "delete") {
    Swal.fire({
      icon: "error",
      title: "Error al eliminar",
      text: "No se pudo eliminar el usuario",
      showConfirmButton: true
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  if (typeof pageError !== "undefined" && pageError === "correo_existente") {
    Swal.fire({
      icon: "error",
      title: "Correo duplicado",
      text: "ya existe un usuario con este correo",
      confirmButtonColor: "#1e6091"
    });
  }
});


