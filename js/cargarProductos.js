
fetch('https://nunapastel.marketingjosa.site/php/obtener_productos.php')
  .then(res => res.json())
  .then(productos => {
    const contenedor = document.getElementById('ultimos-productos');
    productos.forEach(p => {
      const div = document.createElement('div');
      div.className = 'card-producto';
      div.innerHTML = `
        <img src="../php/uploads/${p.img}" alt="torta">
        <h3>${p.nombre}</h3>
        <p class="precio">$${p.precio}</p>
        <button>Agregar al carrito</button>
      `;
      contenedor.appendChild(div);
      div.querySelector('button').addEventListener('click', () => agregarAlCarrito(p));
    });
  });
