const contenedorTarjetas = document.getElementById('ultimos-productos')

function crearProducto(productos){
    productos.forEach(producto => {
        const nuevoProduto = document.createElement('div');
        nuevoProduto.classList = 'card-producto';
        nuevoProduto.innerHTML = `  
        <img src=${producto.img}> 
        <h3>${producto.nombre}</h3>
        <p class="precio">$${producto.precio}</p>
        <button>Agregar al carrito</button>`  
        contenedorTarjetas.appendChild(nuevoProduto);
        nuevoProduto.getElementsByTagName('button')[0].addEventListener('click', ()=> agregarAlCarrito(producto));
        
    });
}

crearProducto(stock);
