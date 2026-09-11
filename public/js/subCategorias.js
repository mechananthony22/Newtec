import {modalAlert} from "./modalAlert.js";

// Poner a disposición globalmente el producto eliminar
window.eliminarSubCategoria = eliminarSubCategoria;
window.editarSubCategoria = editarSubCategoria;

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
        $("#subCategoria_id_form").val("");
        $("#nombre_form").val("");
        $("#categoria_id_form").val("");
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

        $.post("../../public/php/subCategorias.php", {
            accion: "create",
            id: $("#subCategoria_id_form").val(),
            nombre: $("#nombre_form").val(),
            id_categoria: $("#categoria_id_form").val(),
        }, function (data) {
            reloadTable();

        });

        closeModal(modal);
        modalAlert.show('success', 'Éxito', 'Subcategoria agregada correctamente', 2000);
    });
}


// recargar dinamicamente la tabla

function reloadTable() {

    const tableBody = $('#tableBody');

    $.post("../../public/php/subCategorias.php", {
        accion: "getSubCategorias",
    }, function (data) {
        tableBody.html(data);
    });
}


// editar subcategoria
function editarSubCategoria(subCategoria) {
    var _modal = $('#modal');
    openModal(_modal);

    // Verificamos si el rol ya es un objeto o si necesitamos parsearlo
    var subCategoriaData = typeof subCategoria === 'string' ? JSON.parse(subCategoria) : subCategoria;

    $("#subCategoria_id_form").val(subCategoriaData.id);
    $("#nombre_form").val(subCategoriaData.nombre);

    /* cargar categoria */
    $.post("../../public/php/categorias.php", {
        accion: "getAllforSubCategorias",
        id_categoria: subCategoriaData.id_cat,
    }, function (data) {
        // Reemplazar el contenedor completo para evitar duplicados
        $("#categoria_select_container").html(data);
    });
}

// eliminar subcategoria
function eliminarSubCategoria(id) {
    modalAlert.show('warning', 'Advertencia', '¿Estas seguro de eliminar esta subcategoria?', null, true,
    (result) => {
        if (result) {
            $.post("../../public/php/subCategorias.php", {
                accion: "delete",
                id: id
            }, function (data) {
                if (data.success) {
                    reloadTable();
                    modalAlert.show('success', 'Éxito', 'Subcategoria eliminada correctamente', 2000);
                }
            });
        }
    });
}



