import {modalAlert} from "./modalAlert.js";

// Poner a disposición globalmente el producto eliminar
window.eliminarMarca = eliminarMarca;
window.editarMarca = editarMarca;

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

        $.post("../../public/php/marcas.php", {
            accion: "create",
            id: $("#marca_id_form").val(),
            nombre: $("#nombre_form").val(),
        }, function (data) {
            reloadTable();

        });

        closeModal(modal);
        modalAlert.show('success', 'Éxito', 'Marca agregada correctamente', 2000);
    });


}


// recargar dinamicamente la tabla

function reloadTable() {

    const tableBody = $('#tableBody');

    $.post("../../public/php/marcas.php", {
        accion: "getMarcas",
    }, function (data) {
        tableBody.html(data);
    });
}

// editar marca
function editarMarca(marca) {
    var _modal = $('#modal');
    openModal(_modal);

    // Verificamos si el rol ya es un objeto o si necesitamos parsearlo
    var marcaData = typeof marca === 'string' ? JSON.parse(marca) : marca;

    $("#marca_id_form").val(marcaData.id_mar);
    $("#nombre_form").val(marcaData.nombre);
}

// eliminar marca
function eliminarMarca(id) {
    modalAlert.show('warning', 'Advertencia', '¿Estas seguro de eliminar esta marca?', null, true,
    (result) => {
        if (result) {
            $.post("../../public/php/marcas.php", {
                accion: "delete",
                id: id,
            }, function (data) {
                reloadTable();
            });
        }
    });
}



