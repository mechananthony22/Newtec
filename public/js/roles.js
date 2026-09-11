/* ----- importar alertas personalizadas ----- */
import {modalAlert} from "./modalAlert.js";

// Poner a disposición globalmente el producto eliminar
window.eliminarRol = eliminarRol;
window.editarRol = editarRol;

/* ----------------------- */
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
    const modalTitle = $('#modalTitle');
    const submitBtn = $('#submitForm');

    openModalBtn.click(function () {
        $("#rol_id_form").val("");
        $("#nombre_form").val("");
        $("#estado_form").val("");
        modalTitle.text('Agregar nuevo Rol');
        submitBtn.text('Guardar');
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

        $.post("../../public/php/roles.php", {
            accion: "create",
            id: $("#rol_id_form").val(),
            nombre: $("#nombre_form").val(),
            estado: $("#estado_form").val(),
        }, function (data) {
            reloadTable();
            modalAlert.show('success', 'Éxito', 'Rol creado correctamente');
        });

        closeModal(modal);
    });


}


// recargar dinamicamente la tabla

function reloadTable() {

    const tableBody = $('#tableBody');

    $.post("../../public/php/roles.php", {
        accion: "getRoles",
    }, function (data) {
        tableBody.html(data);
    });
}

// editar rol
function editarRol(rol) {
    var _modal = $('#modal');
    openModal(_modal);

    // Verificamos si el rol ya es un objeto o si necesitamos parsearlo
    var rolData = typeof rol === 'string' ? JSON.parse(rol) : rol;

    $("#rol_id_form").val(rolData.id_rol);
    $("#nombre_form").val(rolData.nombre);
    $("#estado_form").val(rolData.estado);
    $('#modalTitle').text('Editar Rol');
    $('#submitForm').text('Actualizar');
}

// eliminar rol
function eliminarRol(id) {
    modalAlert.show('warning', 'Advertencia', '¿Estas seguro de eliminar este rol?', null, true,
    (result) => {
        if (result) {
            $.post("../../public/php/roles.php", {
                accion: "delete",
                id: id,
            }, function (data) {
                reloadTable();
            });
        }
    });
}



