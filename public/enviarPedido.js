function enviarPedido() {
    let mesa = document.getElementById("mesa").value;
    let pedido = document.getElementById("pedido").value;

    // Verifica si los campos no están vacíos
    if (!mesa || !pedido) {
        alert("Por favor, completa ambos campos.");
        return;
    }

    // Usar fetch para enviar los datos al servidor
    fetch("src/controllers/guardar_pedido.php", {
        method: "POST",
        headers: { 
            "Content-Type": "application/x-www-form-urlencoded" 
        },
        body: `mesa=${encodeURIComponent(mesa)}&pedido=${encodeURIComponent(pedido)}`
    })
    .then(response => {
        console.log("Response Status: ", response.status);  // Verifica el código de estado
        return response.text(); // Asegúrate de obtener el cuerpo de la respuesta
    })
    .then(data => {
        console.log("Server Response:", data);  // Ver lo que devuelve el servidor
        alert("Pedido enviado a la cocina!");
        cargarPedidos();  // Recarga los pedidos automáticamente
    })
    .catch(error => {
        console.error("Error al enviar el pedido:", error);
        alert("Hubo un problema al enviar el pedido.");
    });
}
