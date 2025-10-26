import Swal from "sweetalert2";

document.addEventListener("submit", function (e) {
    const form = e.target.closest(".form-eliminar-cuenta");
    if (!form) return;

    // Evita loop cuando se vuelve a enviar tras confirmar
    if (form.dataset.confirmed === "true") return;

    e.preventDefault();

    Swal.fire({
        title: "¿Eliminar tu cuenta?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6b7280",
        background: document.documentElement.classList.contains("dark")
            ? "#1f2937"
            : "#fff",
        color: document.documentElement.classList.contains("dark")
            ? "#f9fafb"
            : "#111827",
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = "true";
            form.submit();
        }
    });
});
