const contenedorTarjetas = document.getElementById('carrito-contenedor')

function crearProducto(){
    contenedorTarjetas.innerHTML = "";
    const localProductos = JSON.parse(localStorage.getItem("carrito"));
    console.log(localProductos);
    if(localProductos && localProductos.length > 0){
        localProductos.forEach(producto => {
            const nuevoProduto = document.createElement('div');
            nuevoProduto.classList = 'carrito-bandeja';
            nuevoProduto.innerHTML = `  
            <img src=../php/uploads/${producto.img}> 
            <h3>${producto.nombre}</h3>
            <p class="precio">$${producto.precio}</p>
            <div>
                <button class="boton">-</button>
                <span class="cantidad">${producto.cantidad}</span>
                <button class="boton">+</button>
            </div>`  
            contenedorTarjetas.appendChild(nuevoProduto);
            nuevoProduto
            .getElementsByTagName('button')[1]
            .addEventListener('click', () => { 
                agregarAlCarrito(producto);
            });

            nuevoProduto
            .getElementsByTagName('button')[0]
            .addEventListener('click', () => { 
                restarAlCarrito(producto);
            });
        });
    }
}

crearProducto();
