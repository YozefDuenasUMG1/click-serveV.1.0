<table border="1">
    <thead>
        <tr>
            <th>Mesa</th>
            <th>Pedido</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody id="pedidos"></tbody>
</table>

<script>
// Función para cargar los pedidos desde el servidor
function cargarPedidos() {
    fetch("obtener_pedidos.php")
    .then(response => response.json())
    .then(data => {
        console.log(data);  // Verifica si los pedidos están llegando correctamente
        let tabla = document.getElementById("pedidos");
        tabla.innerHTML = "";  // Limpiar la tabla antes de agregar nuevos pedidos
        data.forEach(pedido => {
            tabla.innerHTML += `
                <tr>
                    <td>${pedido.mesa}</td>
                    <td>${pedido.detalle}</td>
                    <td>${pedido.estado}</td>
                    <td><button onclick="marcarListo(${pedido.id})">Marcar como Listo</button></td>
                </tr>
            `;
        });
    })
    .catch(error => console.error('Error cargando los pedidos:', error));
}

// Función para marcar un pedido como listo
function marcarListo(id) {
    fetch(`actualizar_pedido.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                cargarPedidos();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error al actualizar el pedido:', error));
}

// Recargar la lista de pedidos cada 5 segundos
setInterval(cargarPedidos, 5000);

// Cargar los pedidos cuando se carga la página
cargarPedidos();
</script>
