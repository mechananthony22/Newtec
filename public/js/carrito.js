// importar modalAlert para usar
import { modalAlert } from "./modalAlert.js";

/* ----------------------------------------------------------------------------- */

// se ejecuta al cargar la pagina -> jquery
$(document).ready(function () {
  // calcular precio de producto de acuerdo a stock disponible
  const precio_unitario = $("#precio").text().replace("S/", "");
  cantidadProducto(precio_unitario);

  // crear carrito
  crearCarrito();
});

/* ----------------------------------------------------------------------------- */
function crearCarrito() {
  // Obtener el id_usuario de la sesión PHP si está disponible
  let id_usuario = "";
  if ($("#id_usuario").length) {
    id_usuario = $("#id_usuario").val();
  }

  const id_producto = $("#id_producto").val();
  const agregar = $("#btn-agregar-carrito");
  const precio = $("#precio").text().replace("S/", ""); // solucion

  // se ejecuta al hacer click en agregar al carrito
  agregar.click(function () {
    const cantidad = $("#cantidad").val();

    // animacion al hacer click en el boton
    agregar.addClass("scale-105");

    setTimeout(function () {
      agregar.removeClass("scale-105");
    }, 100);

    // alerta si el stock es menor a 1
    if (parseInt(cantidad) == 0) {
      modalAlert.show("warning", "Advertencia!", "Stock insuficiente.");
      return;
    }

    // alerta si los datos no estan completos
    if (
      id_usuario === "" ||
      id_producto === "" ||
      cantidad === "" ||
      precio === ""
    ) {
      modalAlert.show("error", "Error!", "Inicia sesión primero.");
      return;
    }

    $.post(
      "../../../public/php/carrito.php",
      {
        accion: "create",
        id_usuario: id_usuario,
        id_producto: id_producto,
        cantidad: cantidad,
        precio_unitario: precio,
      },
      function (data) {
        // actualizar contador de carrito
        actualizarCarrito(id_producto, id_usuario);
        if (window.actualizarContadorCarrito)
          window.actualizarContadorCarrito();
        modalAlert.show(
          "success",
          "Exito!",
          "Producto agregado al carrito.",
          2000
        );
      }
    ).fail(function (jqXHR) {
      let msg = "Error desconocido";
      try {
        const resp = JSON.parse(jqXHR.responseText);
        msg = resp.message || msg;
      } catch(e) {
        msg = jqXHR.responseText || msg;
      }
      console.error("❌ Error carrito:", msg);
      modalAlert.show("error", "Error!", "Error al agregar al carrito: " + msg);
    });
  });
}

// cantidad
function cantidadProducto(precio_unitario) {
  var cantidad = $("#cantidad");
  var stock = $("#stock");
  var precio = $("#precio");

  // se activa al hacer click en restar cantidad
  $("#restar-cantidad").click(function () {
    restarCantidad(cantidad, precio, precio_unitario);
  });
  // se activa al hacer click en sumar cantidad
  $("#sumar-cantidad").click(function () {
    sumarCantidad(cantidad, stock, precio, precio_unitario);
  });
}

// se activa al hacer click en restar cantidad
function restarCantidad(cantidad, precio, precio_unitario) {
  if (parseInt(cantidad.val()) > 1) {
    cantidad.val(parseInt(cantidad.val()) - 1);

    var total = parseInt(cantidad.val()) * parseInt(precio_unitario);
    precio.text("$" + total);
  }
}

/* se activa al hacer click en sumar cantidad */
function sumarCantidad(cantidad, stock, precio, precio_unitario) {
  if (parseInt(stock.text()) > parseInt(cantidad.val())) {
    cantidad.val(parseInt(cantidad.val()) + 1);

    var total = parseInt(cantidad.val()) * parseInt(precio_unitario);
    precio.text("$" + total);
  } else {
    modalAlert.show("warning", "Advertencia!", "Stock insuficiente.");
  }
}
/* -------------------------- */
// actualizar producto seleccionado
function actualizarCarrito(id_producto, id_usuario) {
  var carritoCount = $("#carrito-count");
  var cantidad = $("#cantidad");
  var stock = $("#stock");

  $.post(
    "../../../public/php/carrito.php",
    {
      accion: "update_product",
      id_producto: id_producto,
      id_usuario: id_usuario,
    },
    function (data) {
      // actualizar contador de carrito, cantidad y stock
      data = JSON.parse(data);

      if (data.producto.stock_disponible != 0) {
        var cantidad_value = 1;
        var stock_value = data.producto.stock_disponible + " Stock";
      } else {

        // si el stock es 0, deshabilitar el boton agregar al carrito
        var cantidad_value = 0;
        var stock_value = "Stock agotado";
        stock.removeClass("bg-green-100 text-green-700");
        stock.addClass("bg-red-100 text-red-700");

        // deshabilitar boton agregar al carrito
        $("#btn-agregar-carrito").addClass("opacity-50 cursor-not-allowed");
        $("#btn-agregar-carrito").attr("disabled", true);
      }

      // actualizar contador de carrito, cantidad y stock
      carritoCount.text(data.carrito);
      cantidad.val(cantidad_value);
      stock.text(stock_value);
    }
  );
}
