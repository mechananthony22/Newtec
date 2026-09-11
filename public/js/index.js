// obtener categorias
function getCategorias(id_categoria) {
    $.post("public/php/index.php", {
        accion: "getCategorias",
        id_categoria: id_categoria,
    }, function (data) {

        // actualiza la url sin recargar la pagina
        history.pushState(null, '', "?categoria=" + id_categoria);

        // actualiza la lista de productos
        $("#productos").html(data);
    });
}