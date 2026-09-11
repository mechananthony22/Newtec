import {modalAlert} from "./modalAlert.js";

// Poner a disposición globalmente el producto eliminar
window.eliminarModelo = eliminarModelo;
window.editarModelo = editarModelo;

$(document).ready(function () {
    const modal = $('#modal');

    useModal(modal);
    handleSubmitForm(modal);
});


/* ----------------- configuracion del estado del modal ----------------- */
function useModal(modal) {
    const openModalBtn = $('#openModalBtn');
    const closeModalBtn = $('#closeModalBtn');
    const closeModalX = $('#closeModalX');

    openModalBtn.click(function () {
        $("#marca_id_form").val("");
        $("#nombre_form").val("");
        $("#marca_form").val("");
        openModal(modal);
    });

    closeModalBtn.click(function () {
        closeModal(modal);
    });

    closeModalX.click(function () {
        closeModal(modal);
    });
}

function openModal(modal) {
    modal.removeClass('hidden');
}

function closeModal(modal) {
    modal.addClass('hidden');
}
// Manejar envío del formulario

function handleSubmitForm(modal) {
    const button = $('#submitForm');

    button.click(function () {

        $.post("../../public/php/modelos.php", {
            accion: "create",
            id: $("#modelo_id_form").val(),
            nombre: $("#nombre_form").val(),
            id_marca: $("#marca_id_form").val(),
        }, function (data) {
            reloadTable();

        });

        closeModal(modal);
        modalAlert.show('success', 'Éxito', 'Modelo agregado correctamente', 2000);
    });
}


// recargar dinamicamente la tabla

function reloadTable() {

    const tableBody = $('#tableBody');

    $.post("../../public/php/modelos.php", {
        accion: "getModelos",
    }, function (data) {
        tableBody.html(data);
    });
}


// editar modelo
function editarModelo(modelo) {
    var _modal = $('#modal');
    openModal(_modal);

    // Verificamos si el rol ya es un objeto o si necesitamos parsearlo
    var modeloData = typeof modelo === 'string' ? JSON.parse(modelo) : modelo;

    $("#modelo_id_form").val(modeloData.id_mod);
    $("#nombre_form").val(modeloData.nombre);

    /* cargar marca */
    $.post("../../public/php/marcas.php", {
        accion: "getAllforModelos",
        marca_id: modeloData.id_mar,
    }, function (data) {
        $("#marca_id_form").html(data);
    });
}

// eliminar modelo
function eliminarModelo(id) {
    modalAlert.show('warning', 'Advertencia', '¿Estas seguro de eliminar este modelo?', null, true,
    (result) => {
        if (result) {
            $.post("../../public/php/modelos.php", {
                accion: "delete",
                id: id,
            }, function (data) {
                reloadTable();
            });
        }
    });
}



