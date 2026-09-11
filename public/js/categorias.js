import {modalAlert} from "./modalAlert.js";

// Poner a disposición globalmente el producto eliminar
window.eliminarCategoria = eliminarCategoria;
window.editarCategoria = editarCategoria;

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
        $("#categoria_id_form").val("");
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

        $.post("../../public/php/categorias.php", {
            accion: "create",
            id: $("#categoria_id_form").val(),
            nombre: $("#nombre_form").val(),
        }, function (data) {
            reloadTable();

        });

        closeModal(modal);
        modalAlert.show('success', 'Éxito', 'Categoria agregada correctamente', 2000);
    });


}


// recargar dinamicamente la tabla

function reloadTable() {

    const tableBody = $('#tableBody');

    $.post("../../public/php/categorias.php", {
        accion: "getCategorias",
    }, function (data) {
        tableBody.html(data);
    });
}

// editar categoria
function editarCategoria(categoria) {
    var _modal = $('#modal');
    openModal(_modal);

    // Verificamos si el rol ya es un objeto o si necesitamos parsearlo
    var categoriaData = typeof categoria === 'string' ? JSON.parse(categoria) : categoria;

    $("#categoria_id_form").val(categoriaData.id_cat);
    $("#nombre_form").val(categoriaData.nombre);
}

// eliminar categoria
function eliminarCategoria(id) {
    modalAlert.show('warning', 'Advertencia', '¿Estas seguro de eliminar esta categoria?', null, true,
    (result) => {
        if (result) {
            $.post("../../public/php/categorias.php", {
                accion: "delete",
                id: id,
            }, function (data) {
                reloadTable();
            });
        }
    });
}



