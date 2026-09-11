# CHANGELOG
Cambios hechos

## v3.0.0 (Jr)
- Se corrigio el error al listar usuarios en la tabla de usuarios `$usuarios['id_us']` en la vista `usuarios_admin.php`
- se corrigio al no mostrar modelos al seleccionar una marca
- [] falta proteger las rutas admin con autenticacion


## v3.0.1 (Jr) - 08/07/2025
!!! nota: El query de la base de datos esta -> config/query.sql


- Se corrigio el error al eliminar productos del carrito (views/carrito.php)
- Se corrigio el error al vaciar el carrito (views/carrito.php)
- Se agrego mensajes de estado personalizables (success, warning, error...) (public/js/modalAlert.js)
- Se agrego la carga dinamica y comprobacion de stock al momento de agregar productos al carrito (views/productoSeleccionado.php)
- Se agrego la funcionalidad de que si usuario1 compra un producto de otro usuario2 que lo tiene en carrito, al recargar la pagina el ususario2 no podra ver el producto en su carrito (se eliminara del carrito automaticamente) (views/carrito.php)
- El carrito es independiente a cada usuario, ejemplo : usuario1 añade un producto que cuenta con un stock de 2 unidades, al añadir el producto eligiendo las 2 unidades disponibles al carrito, se resta el stock disponible del producto al usuario1, es decir le saldra producto agotado. (los demas usuario podran ver disponibilidad del stock de 2 unidades del producto hasta que el usuario1 realice la compra)
- [x] carrito.php pasar informacion todo los productos.
- [x] actualizar producto de carrito si otro usuario compra el producto y no cuenta con stock disponible.
- [x] personalizar productos que se presente al inicio de la pagina (views/index.php) (se puede mejorar)
- [x] validacion numero de telefono falta.
- [x] mesage de confirmacion al eliminar producto del carrito, etc.
- [x] mejora en la interfaz den  la categoria y subcategorias a eligir (views/productos_publicos.php) (controla todas las rutas)
- [x] Se añadio interfaz (nav(te ayudamos), footer(nosotros, servicios, contacto, terminos y condiciones y sedes(no funcional el mapa)))
- [x] se arreglo la barra de navegacion (componentes/headerNav.php) para que sea responsive en tamaño de celular.
- [x] Se corrigio las imagenes de los productos y la subida con comprobacion de extensiones permitidas.
- [ ] quitar tailwindcss en dashboard admin para ser remplazado por css puro (tal vez)
- [ ] Añadir middleware de autenticacion para rutas admin y otros (se añadira directo al archivo index.php o router.php)
- [ ] Seguridad para scraping o bots
- [ ] mejorar al realizar la compra de productos (views/venta.php) si se quiere añadir sisitema de pagos.. etc..
- [ ] etc...