$(document).ready(function() {
    $(".stock").each(function() {
        const stock = $(this).text();
        if (stock === 'Agotado') {
            $(this).css('color', 'red');
            $(this).css('font-weight', 'bold');
        }else{
            $(this).css('color', 'green');
            $(this).css('font-weight', 'bold');
        }
    });
    // Update price range display
    $('input[type="range"]').on('input', function() {
        const value = $(this).val();
        if ($(this).attr('id') === 'precio-min') {
            $('#valor-min').text('S/ ' + value);
        } else {
            $('#valor-max').text('S/ ' + value);
        }
    });

    // Apply filters
    $('#aplicar-filtros').on('click', function() {
        const selectedSubcategorias = [];
        $('.subcategoria-checkbox:checked').each(function() {
            selectedSubcategorias.push($(this).val().toLowerCase());
        });

        const selectedMarcas = [];
        $('.marca-checkbox:checked').each(function() {
            selectedMarcas.push($(this).val().toLowerCase());
        });

        const minPrice = parseFloat($('#precio-min').val());
        const maxPrice = parseFloat($('#precio-max').val());

        $('.producto').each(function() {
            const $producto = $(this);
            const productoPrecio = parseFloat($producto.data('precio') || 0);
            const productoSubcategoria = $producto.data('subcategoria') ? $producto.data('subcategoria').toLowerCase() : '';
            const productoMarca = $producto.data('marca') ? $producto.data('marca').toLowerCase() : '';

            // Check price range
            const precioEnRango = productoPrecio >= minPrice && productoPrecio <= maxPrice;
            
            // Check subcategorias
            const subcategoriaMatch = selectedSubcategorias.length === 0 || 
                                    selectedSubcategorias.some(sc => productoSubcategoria.includes(sc));
            
            // Check marcas
            const marcaMatch = selectedMarcas.length === 0 || 
                             selectedMarcas.some(m => productoMarca.includes(m));

            // Show/hide based on filters
            if (precioEnRango && subcategoriaMatch && marcaMatch) {
                $producto.show();
            } else {
                $producto.hide();
            }
        });
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        // Uncheck all checkboxes
        $('input[type="checkbox"]').prop('checked', false);
        
        // Reset price range
        $('#precio-min').val(0).trigger('input');
        $('#precio-max').val(1000).trigger('input');
        
        // Show all products
        $('.producto').show();
    });
});
