// importar modalAlert para usar
import { modalAlert } from "./modalAlert.js";

// Poner a disposición globalmente el producto eliminar
window.eliminarProducto = eliminarProducto;

/* ------------------------------- En la ruta: http://localhost/perunet/carrito ------------------------------- */
$(document).ready(function () {
    // vaciar carrito
    $('#vaciar-carrito').click(function () {
        vaciarCarrito();
    });

    // mensaje si los productos ya no existen
    var mensaje = $('#mensaje').length ? $('#mensaje').val() : false;
    if (mensaje) {
        modalAlert.show('warning', 'Advertencia!', mensaje);
    }
});

function vaciarCarrito() {
    // Mostrar confirmación al usuario
    modalAlert.show('warning', 'Advertencia!', '¿Estás seguro de vaciar el carrito?', null, true, function (confirmado) {
        if (!confirmado) {
            return;
        }

        var id_usuario = $('#id_usuario').val();

        $.post("public/php/carrito.php", {
            accion: "empty_cart",
            id_usuario: id_usuario,
        }, function (data) {
            // Actualizar contador de carrito
            setTimeout(function () {
                modalAlert.show('success', 'Éxito!', 'Carrito vaciado correctamente.');
            }, 500);
            // Actualizar contador de carrito
            actualizarCarrito();
            recargarCarrito();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            modalAlert.show('error', 'Error!', 'Error al vaciar el carrito: ' + textStatus + ' ' + errorThrown);
        });
    });
}

function eliminarProducto(id_producto) {
    // Mostrar confirmación al usuario
    modalAlert.show('warning', 'Advertencia!', '¿Estás seguro de eliminar el producto?', null, true, function (confirmado) {
        if (!confirmado) {
            return;
        }

        $.post("public/php/carrito.php", {
            accion: "delete_product",
            id_producto: id_producto,
        }, function (data) {
            // Mostrar confirmación al usuario
            setTimeout(function () {
                modalAlert.show('success', 'Éxito!', 'Producto eliminado correctamente.');
            }, 500);
            // Actualizar contador de carrito
            actualizarCarrito();
            recargarCarrito();

        }).fail(function (jqXHR, textStatus, errorThrown) {
            modalAlert.show('error', 'Error!', 'Error al eliminar el producto: ' + textStatus + ' ' + errorThrown);
        });
    });
}

// recargar la lista de productos en el carrito
function recargarCarrito() {
    var id_usuario = $('#id_usuario').val();

    $.post("public/php/carrito.php", {
        accion: "get_products",
        id_usuario: id_usuario,
    }, function (data) {
        // actualizar contador de carrito
        $('#carrito-productos').html(data);
    }).fail(function (jqXHR, textStatus, errorThrown) {
        modalAlert.show('error', 'Error!', 'Error al obtener los productos del carrito: ' + textStatus + ' ' + errorThrown);
    });
}

// actualizar contador de carrito barra de navegacion
function actualizarCarrito() {
    var carritoCount = $('#carrito-count');
    var id_usuario = $('#id_usuario').val();

    $.post("public/php/carrito.php", {
        accion: "count",
        id_usuario: id_usuario,
    }, function (data) {
        // actualizar contador de carrito
        carritoCount.text(data);
    }).fail(function (jqXHR, textStatus, errorThrown) {
        modalAlert.show('error', 'Error!', 'Error al obtener el contador del carrito: ' + textStatus + ' ' + errorThrown);
    });
}