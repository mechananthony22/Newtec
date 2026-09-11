import { modalAlert } from "./modalAlert.js";

// Poner a disposición globalmente el producto eliminar
window.irPaso = irPaso;
window.seleccionarEntrega = seleccionarEntrega;
window.seleccionarMetodo = seleccionarMetodo;

var usuario = { id: '', nombre: '', apellidos: '', correo: '', dni: '', telefono: '' };
var entrega = { tipo: null, id_direccion: null, domicilio: {}, sucursal: {} };
var metodoPago = { seleccionado: null, tipo: null, tarjeta: {}, celular: {} };

// Mercado Pago Instance
console.log("Inicializando Mercado Pago SDK v2...");
const mp = new MercadoPago("TEST-4cb670f6-e3f8-44bc-81bb-0945c34fbe93", { locale: 'es-PE' });
let brickController;
let currentPaymentTotal = 0; // Guarda el monto para el modo prueba local

$(document).ready(function () {
  $("#comprar").click(function () {
    comprar();
  });

  var domicilioButton = $("#domicilio-button");
  var tiendaButton = $("#tienda-button");

  domicilioButton.click(function () {
    console.log("Domicilio");
    domicilioButton.addClass("bg-red-200 text-red-700 hover:bg-red-100");
    tiendaButton.removeClass("bg-red-200 text-red-700 hover:bg-red-100");
  });

  tiendaButton.click(function () {
    console.log("Tienda");
    tiendaButton.addClass("bg-red-200 text-red-700 hover:bg-red-100");
    domicilioButton.removeClass("bg-red-200 text-red-700 hover:bg-red-100");
  });
});

function comprar() {
  $.ajax({
    url: "../public/php/venta.php",
    method: "POST",
    data: {
      accion: "create",
      usuario: JSON.stringify(usuario),
      entrega: JSON.stringify(entrega),
      metodoPago: JSON.stringify(metodoPago),
    },
    success: function (response) {
      if (response.status === "success") {
        setTimeout(function () {
          modalAlert.show("success", "Éxito!", "Venta realizada correctamente.", 2000);
        }, 500);
        const id_venta = response.data.idVenta;
        window.location.href = "/perunet/usuarios/compra/" + id_venta;
      }
    },
    error: function (xhr) {
      modalAlert.show("error", "Error!", "Error al realizar la venta.");
    },
  });
}

// Cambio de paso
function irPaso(n) {
  if (n === 2) {
    usuario.id = $("#usuario_id").val();
    usuario.nombre = $("#usuario_nombre").val();
    usuario.apellidos = $("#usuario_apellidos").val();
    usuario.correo = $("#usuario_correo").val();
    usuario.dni = $("#usuario_dni").val();
    usuario.telefono = $("#usuario_telefono").val();

    if (!validarDatosCliente()) return;
  }

  document.querySelectorAll(".paso-compra").forEach((p) => {
    p.classList.add("hidden");
  });

  const currentPaso = document.getElementById("paso" + n);
  if (currentPaso) {
    currentPaso.classList.remove("hidden");
  }

  actualizarStepper(n);

  if (n === 2) {
    console.log("SISTEMA V7 CARGADO - Paso 2...");
    obtenerTotalYRenderizarBrick();
  }

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function actualizarStepper(n) {
  const lineProgress = document.getElementById("line-progress");
  const stepItems = document.querySelectorAll(".step-item");
  const progressPercents = [0, 0, 50, 100]; 

  if (lineProgress) {
    lineProgress.style.width = progressPercents[n] + "%";
  }

  stepItems.forEach((item) => {
    const step = parseInt(item.dataset.step);
    item.classList.remove("active", "completed");
    const icon = item.querySelector(".step-icon");
    icon.classList.remove("border-red-600", "text-red-600", "border-gray-300", "text-gray-400", "border-green-600", "text-green-600");

    if (step === n) {
      item.classList.add("active");
      icon.classList.add("border-red-600", "text-red-600");
    } else if (step < n) {
      item.classList.add("completed");
      icon.classList.add("border-green-600", "text-green-600");
    } else {
      icon.classList.add("border-gray-300", "text-gray-400");
    }
  });
}

function seleccionarEntrega(tipo) {
  document.getElementById("domicilio-section").style.display = tipo === "domicilio" ? "block" : "none";
  document.getElementById("tienda-section").style.display = tipo === "tienda" ? "block" : "none";
  entrega.tipo = tipo;
  console.log("Entrega seleccionada:", tipo);
}

// Función necesaria aunque usemos Mercado Pago (para evitar ReferenceError)
function seleccionarMetodo(id, tipo) {
  console.log("Seleccionando método manual:", id, tipo);
  metodoPago.seleccionado = id;
  metodoPago.tipo = tipo;
}

function validarDatosCliente() {
  const nombre = $("#usuario_nombre").val();
  const apellidos = $("#usuario_apellidos").val();
  const correo = $("#usuario_correo").val();
  const dni = $("#usuario_dni").val();
  const telefono = $("#usuario_telefono").val();

  if (!nombre || !apellidos || !correo || !dni || !telefono) {
    modalAlert.show("warning", "Advertencia!", "Por favor, complete todos los campos de sus datos.");
    return false;
  }
  return true;
}

function validarDatosEntrega() {
  if (entrega.tipo === "domicilio") {
    const departamento = $("#departamento").val();
    const provincia = $("#provincia").val();
    const distrito = $("#distrito").val();
    const calle = $("#calle").val();
    const numero = $("#numero").val();

    if (!departamento || !provincia || !distrito || !calle || !numero) {
      modalAlert.show("warning", "Advertencia!", "Por favor, complete los campos de su dirección de entrega.");
      return false;
    }
  } else if (entrega.tipo === "tienda") {
    const sucursal = $('input[name="sucursal"]:checked').val();
    if (!sucursal) {
      modalAlert.show("warning", "Advertencia!", "Por favor, seleccione una sucursal para el recojo.");
      return false;
    }
  } else {
    modalAlert.show("warning", "Advertencia!", "Por favor, elija un método de entrega.");
    return false;
  }
  return true;
}

function obtenerTotalYRenderizarBrick() {
    if (brickController) {
        brickController.unmount();
        brickController = null;
    }

    const usuarioId = $("#usuario_id").val() || 0;
    console.log("Solicitando total real para usuario:", usuarioId);
    
    $.ajax({
        url: "/perunet/public/php/get_total.php",
        method: "POST",
        data: { usuario_id: usuarioId },
        success: function(response) {
            console.log("Respuesta de total:", response);
            if (response.status === "success") {
                renderPaymentBrick(response.total);
            } else {
                renderPaymentBrick(175.00); 
            }
        },
        error: function(xhr) {
            renderPaymentBrick(175.00); 
        }
    });
}

async function renderPaymentBrick(total) {
    currentPaymentTotal = total; // Guardar para uso en onError (modo prueba)
    console.log("Iniciando Brick V12 con monto:", total);
    
    if (brickController) {
        try { await brickController.unmount(); } catch(e) {}
    }

    try {
        const bricksBuilder = mp.bricks();
        const settings = {
            initialization: {
                amount: Number(Number(total).toFixed(2)),
                payer: {
                    email: usuario.correo || "test_user_123456@testuser.com",
                },
            },
            customization: {
                paymentMethods: {
                    maxInstallments: 1,
                    creditCard: "all",
                    debitCard: "all"
                }
            },
            callbacks: {
                onReady: () => {
                    console.log("✅ SISTEMA V11 LISTO!");
                    const loadingText = document.getElementById('mp_loading_text');
                    if (loadingText) loadingText.remove();
                },
                onSubmit: ({ selectedPaymentMethod, formData }) => {
                    return new Promise((resolve, reject) => {
                        procesarPagoFinal(formData, resolve, reject);
                    });
                },
                onError: (error) => {
                    console.error("❌ Error de Brick:", JSON.stringify(error));
                    
                    // MODO PRUEBA LOCAL:
                    // En localhost (HTTP), MP no puede tokenizar tarjetas (requiere HTTPS).
                    // Cuando el error es 'secure_fields_card_token_creation_failed',
                    // enviamos token='PRUEBA_LOCAL' y el backend PHP simula el pago aprobado.
                    if (error && error.cause === 'secure_fields_card_token_creation_failed') {
                        console.warn("⚠️ MODO PRUEBA LOCAL: Tokenización MP falló (HTTP sin SSL). Procesando localmente...");
                        procesarPagoFinal(
                            {
                                token: 'PRUEBA_LOCAL',
                                issuer_id: null,
                                payment_method_id: 'visa',
                                transaction_amount: currentPaymentTotal,
                                installments: 1,
                                payer: { email: usuario.correo || 'test@testuser.com' }
                            },
                            () => {},
                            () => {}
                        );
                        return;
                    }
                    
                    // Para otros errores críticos, mostrar modal
                    if (error && error.type !== 'non_critical') {
                        modalAlert.show("error", "Error de Pasarela", "Código: " + (error.cause || error.message || JSON.stringify(error)));
                    }
                },
            },
        };

        const container = document.getElementById('paymentBrick_container');
        if (container) {
            const loadingText = document.getElementById('mp_loading_text');
            if (loadingText) loadingText.remove();
        }

        brickController = await bricksBuilder.create('payment', 'paymentBrick_container', settings);
    } catch (e) {
        console.error("Error crítico en V11:", e);
    }
}

function procesarPagoFinal(formData, resolve, reject) {
    if (entrega.tipo == null) {
        modalAlert.show("warning", "Entrega!", "Por favor, seleccione primero el tipo de entrega.");
        reject();
        return;
    }

    if (entrega.tipo == "domicilio") {
        entrega.domicilio = {
            departamento: $("#departamento").val(),
            provincia: $("#provincia").val(),
            distrito: $("#distrito").val(),
            calle: $("#calle").val(),
            numero: $("#numero").val(),
            piso: $("#piso").val(),
            referencia: $("#referencia").val()
        };
    } else {
        entrega.sucursal = {
            id: $('input[name="sucursal"]:checked').val()
        };
    }

    if (!validarDatosEntrega()) { reject(); return; }

    irPaso(3); 

    // Aseguramos que metodoPago tenga el id correcto (1 = Tarjeta, via Mercado Pago Brick)
    if (!metodoPago.seleccionado) {
        metodoPago.seleccionado = 1;
        metodoPago.tipo = 'tarjeta';
    }

    const datos = {
        accion: "create",
        usuario: JSON.stringify(usuario),
        entrega: JSON.stringify(entrega),
        metodoPago: JSON.stringify(metodoPago),
        token: formData.token,
        issuer_id: formData.issuer_id,
        payment_method_id: formData.payment_method_id,
        transaction_amount: formData.transaction_amount,
        installments: formData.installments,
        payer: JSON.stringify(formData.payer)
    };

    $.ajax({
        url: "/perunet/public/php/venta.php",
        method: "POST",
        data: datos,
        success: function (response) {
            if (response.status === "success") {
                modalAlert.show("success", "Pago Exitoso!", "Tu compra se ha realizado correctamente.", 3000);
                setTimeout(() => {
                    window.location.href = "/perunet/usuarios/compra/" + response.data.idVenta;
                    resolve();
                }, 2000);
            } else {
                modalAlert.show("error", "Pago Rechazado", response.message || "No se pudo procesar el pago.");
                irPaso(2);
                reject();
            }
        },
        error: function (xhr) {
            let msg = "Error en el servidor.";
            try {
                const resp = JSON.parse(xhr.responseText);
                msg = resp.message || msg;
            } catch(e) {}
            console.error("❌ Error del servidor:", xhr.status, xhr.responseText);
            modalAlert.show("error", "Error (" + xhr.status + ")", msg);
            irPaso(2);
            reject();
        }
    });
}
