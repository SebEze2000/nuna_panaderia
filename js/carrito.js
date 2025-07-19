// Utilidades de selección segura
const cuentaCarrito = document.getElementById('count');
const cuentaTotales = document.getElementById('unidades');
const precioTotales = document.getElementById('precio');
const divVacio = document.getElementById('carrito-vacio');
const botonVaciar = document.getElementById('vaciar-carrito');
const comprarBoton = document.getElementById("comprar");

// Crear producto con cantidad 1
function nuevoPrMemo(producto) {
    return { ...producto, cantidad: 1 };
}

// Actualizar todo el carrito: totales, unidades, mensaje vacío
function actualizarCarrito() {
    const memoria = JSON.parse(localStorage.getItem("carrito")) || [];
    const unidades = memoria.reduce((acum, p) => acum + p.cantidad, 0);
    const total = memoria.reduce((acum, p) => acum + p.precio * p.cantidad, 0);

    if (cuentaCarrito) cuentaCarrito.innerText = unidades;
    if (cuentaTotales) cuentaTotales.innerText = unidades;
    if (precioTotales) precioTotales.innerText = total;
    if (divVacio) divVacio.innerText = memoria.length === 0 ? "Ups, tu carrito está vacío" : "";
}

// Agregar producto al carrito
function agregarAlCarrito(producto) {
    const memoria = JSON.parse(localStorage.getItem("carrito")) || [];
    const index = memoria.findIndex(p => p.id === producto.id);

    if (index === -1) {
        memoria.push(nuevoPrMemo(producto));
    } else {
        memoria[index].cantidad++;
    }

    localStorage.setItem("carrito", JSON.stringify(memoria));
    actualizarCarrito();
    if (typeof crearProducto === "function") crearProducto();
}

// Restar producto o eliminar si llega a 0
function restarAlCarrito(producto) {
    const memoria = JSON.parse(localStorage.getItem("carrito")) || [];
    const index = memoria.findIndex(p => p.id === producto.id);

    if (index !== -1) {
        if (memoria[index].cantidad === 1) {
            memoria.splice(index, 1);
        } else {
            memoria[index].cantidad--;
        }

        localStorage.setItem("carrito", JSON.stringify(memoria));
        actualizarCarrito();
        if (typeof crearProducto === "function") crearProducto();
    }
}

// Vaciar carrito desde botón
if (botonVaciar) {
    botonVaciar.addEventListener('click', () => {
        localStorage.removeItem("carrito");
        actualizarCarrito();
        if (typeof crearProducto === "function") crearProducto();
    });
}

//botono de comprar//

if (comprarBoton) {
    comprarBoton.addEventListener('click', () => {
        const cel = "541162344594";
        const baseUrl = `https://wa.me/${cel}`;
        const mensajeBase = "Hola, me interesa comprar estos productos:";

        // Recuperar carrito
        const carrito = JSON.parse(localStorage.getItem("carrito")) || [];

        if (carrito.length === 0) {
            alert("Tu carrito está vacío");
            return;
        }

        // Formatear productos y calcular total
        let total = 0;
        const productosTexto = carrito.map(p => {
            const subtotal = p.precio * p.cantidad;
            total += subtotal;
            return `- ${p.nombre} x${p.cantidad} ($${subtotal})`;
        }).join("\n");

        // Agregar total
        const mensaje = encodeURIComponent(`${mensajeBase}\n${productosTexto}\n\nTOTAL: $${total}`);

        // Redireccionar a WhatsApp
        const redirectUrl = `${baseUrl}?text=${mensaje}`;
        window.location.href = redirectUrl;
    });
}





// Al cargar cualquier página, actualiza contador
document.addEventListener("DOMContentLoaded", () => {
    actualizarCarrito();
});
