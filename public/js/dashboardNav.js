/* usar jQuery */
$(document).ready(function () {
    /* leer el documento .html */
    menu();
})


function menu() {

    /* poder manipular el menu */

    var menu_icon = $("#menu-icon");
    var menu = $("#menu");
    var content = $("#content");

    menu_icon.click(function () {
        menu_icon.addClass("transform scale-110 transition ease-in-out duration-300");

        /* esperar 300ms para quitar la animacion */
        setTimeout(() => {
            menu_icon.removeClass("transform scale-110 transition ease-in-out duration-300");
        }, 300);

        /* si el menu esta oculto */
        if (menu.hasClass("max-md:hidden")) {
            menu.removeClass("max-md:hidden");
            content.addClass("ml-35");
        } else {
            menu.addClass("max-md:hidden");
            content.removeClass("ml-35");
        }

        /* si el contenido esta con margin-left */
        if (content.hasClass("md:ml-35")) {
            content.removeClass("md:ml-35");
            menu.removeClass("max-md:hidden");
            content.addClass("md:ml-0");
            menu.addClass("md:hidden");
        } else {
            content.addClass("md:ml-35");
            content.removeClass("md:ml-0");
            menu.removeClass("md:hidden");
        }
    })

}