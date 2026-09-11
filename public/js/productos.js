/* uso de jquery para filtrar modelo segun la marca elegida */
$(document).ready(function () {
    filtroMarcaModelo();
    filtroCategoriaSubcategoria();
});


/* filtro marca -> modelo */
function filtroMarcaModelo() {
    var marcaId = $("#marca");

    marcaId.on("change", function () {
        let valorSeleccionado = $(this).val();
        //console.log('marca', valorSeleccionado);
        cargarMarcaModelo(valorSeleccionado);

    })
}
function cargarMarcaModelo(valorSeleccionado) {
    var modeloId = $("#modelo");
    var isEditing = $("#isEditing").val();
    var ruta = "";



    /* correcion de problemas con la url : 
    1: 'ejemplo /perunet/admin/productos/crear' 
    2: 'ejemplo /perunet/admin/productos/editar/1' 

    la url 2 tiene un '/' mas que la 1 es decir '/1'
    */
    if (isEditing) {
        // en caso url tenga '/1' 
        ruta = "../../../"
    } else {
        // en caso url tenga '/crear'
        ruta = "../../"
    }

    $.post(ruta + "public/php/productos.php", {
        accion: "filtroMarcaModelo",
        marca_id: valorSeleccionado,
    }, function (data) {
        
        //console.log(data);

        modeloId.empty();

        /* convertir json a array de (json_encode) */
        data = JSON.parse(data);

        /* si no hay modelos */
        if (data.length === 0) {
            modeloId.append($("<option>", { value: "", text: "No hay modelos" }));
        } else {
            /* recorrer array y agregar option en el select modelo */
            modeloId.html('<option value="">-- Selecciona un modelo --</option>');

            data.forEach(function (element) {
                modeloId.append($("<option>", { value: element.id_mod, text: element.nombre }));
            })

        }


    })

}

/* filtro categoria -> subcategoria */
function filtroCategoriaSubcategoria() {
    var categoriaId = $("#categoria");

    categoriaId.on("change", function () {
        let valorSeleccionado = $(this).val();
        //console.log(valorSeleccionado);
        cargarCategoriaSubcategoria(valorSeleccionado);

    })   
}

function cargarCategoriaSubcategoria(valorSeleccionado) {
    var subcategoriaId = $("#subCategoria");
    var isEditing = $("#isEditing").val();
    var ruta = "";

    /* correcion de problemas con la url : 
    1: 'ejemplo /perunet/admin/productos/crear' 
    2: 'ejemplo /perunet/admin/productos/editar/1' 

    la url 2 tiene un '/' mas que la 1 es decir '/1'
    */
    if (isEditing) {
        // en caso url tenga '/1' 
        ruta = "../../../"
    } else {
        // en caso url tenga '/crear'
        ruta = "../../"
    }

    $.post(ruta + "public/php/productos.php", {
        accion: "filtroCategoriaSubcategoria",
        id_categoria: valorSeleccionado,
    }, function (data) {

        //console.log(data);

        subcategoriaId.empty();

        /* convertir json a array de (json_encode) */
        data = JSON.parse(data);

        /* si no hay subcategorias */
        if (data.length === 0) {
            subcategoriaId.append($("<option>", { value: "", text: "No hay subcategorias" }));
        } else {
            /* recorrer array y agregar option en el select subcategoria */
            subcategoriaId.html('<option value="">-- Selecciona una subcategoria --</option>');

            data.forEach(function (element) {
                subcategoriaId.append($("<option>", { value: element.id, text: element.nombre }));
            })

        }


    })

}
//_------------------------------------------------
