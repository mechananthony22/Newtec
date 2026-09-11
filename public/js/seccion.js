document.addEventListener('DOMContentLoaded', () => {
    //console.log('Seccion.js cargado y funcionando correctamente');
    const overlay = document.getElementById('overlay');
    const overlayText = document.getElementById('overlay-text');
    const closeOverlayButton = document.getElementById('close-overlay');
    
    // Función para mostrar el overlay con animación
    function showOverlay() {
        // Primero quitamos la clase hidden
        overlay.classList.remove('hidden');
        // Luego añadimos clases de animación de Tailwind
        setTimeout(() => {
            overlay.classList.add('opacity-100');
            overlay.classList.remove('opacity-0');
        }, 10);
        document.body.classList.add('overflow-hidden'); // Evita scroll en el body con Tailwind
        
        // Asegurar que el contenedor del overlay tenga scroll
        const overlayContainer = overlay.querySelector('.bg-white');
        if (overlayContainer) {
            overlayContainer.classList.add('max-h-[80vh]', 'overflow-y-auto');
        }
    }
    
    // Función para ocultar el overlay con animación
    function hideOverlay() {
        // Primero animamos la salida
        overlay.classList.add('opacity-0');
        overlay.classList.remove('opacity-100');
        // Después de la animación, ocultamos completamente
        setTimeout(() => {
            overlay.classList.add('hidden');
        }, 300); // Tiempo de la transición
        document.body.classList.remove('overflow-hidden'); // Restaura el scroll con Tailwind
    }

    // Contenido dinámico para cada sección
const sections = {
    'nosotros-link': `

                <div class="max-w-4xl mx-auto px-4 py-8">
                    <h2 class="text-3xl font-bold text-center mb-8 text-red-700">Nosotros</h2>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h3 class="text-2xl font-bold text-red-600 mb-4">Sobre PeruNet</h3>
                        <p class="text-gray-700 leading-relaxed">Somos una empresa peruana dedicada a la venta de productos de tecnología y servicios de internet. Contamos con más de 10 años de experiencia en el mercado, brindando soluciones tecnológicas a nuestros clientes.</p>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-red-50 rounded-lg shadow-md p-6 border-l-4 border-red-600">
                            <h3 class="text-xl font-bold text-red-600 mb-3">Nuestra Visión</h3>
                            <p class="text-gray-700 leading-relaxed">Ser la empresa líder en soluciones tecnológicas en el Perú, reconocida por su excelencia en servicio y calidad de productos.</p>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg shadow-md p-6 border-l-4 border-red-600">
                            <h3 class="text-xl font-bold text-red-600 mb-3">Nuestra Misión</h3>
                            <p class="text-gray-700 leading-relaxed">Proporcionar soluciones tecnológicas innovadoras y accesibles que mejoren la calidad de vida de nuestros clientes, contribuyendo al desarrollo digital del país.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h3 class="text-xl font-bold text-red-600 mb-4">Nuestros Valores</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600 mr-3 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-gray-700"><span class="font-semibold">Innovación:</span> Buscamos constantemente nuevas formas de mejorar nuestros productos y servicios.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600 mr-3 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-gray-700"><span class="font-semibold">Compromiso:</span> Nos comprometemos a brindar un servicio de calidad y a cumplir con nuestras promesas.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600 mr-3 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-gray-700"><span class="font-semibold">Integridad:</span> Actuamos con honestidad y transparencia en todas nuestras operaciones.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600 mr-3 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-gray-700"><span class="font-semibold">Excelencia:</span> Nos esforzamos por superar las expectativas de nuestros clientes en cada interacción.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600 mr-3 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-gray-700"><span class="font-semibold">Responsabilidad Social:</span> Contribuimos al desarrollo sostenible de nuestra comunidad y el medio ambiente.</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold text-red-600 mb-4">Historia</h3>
                        <div class="space-y-3 text-gray-700">
                            <p class="leading-relaxed">PeruNet fue fundada en 2013 con la visión de democratizar el acceso a la tecnología en el Perú. Comenzamos como un pequeño negocio familiar en Lima, ofreciendo servicios básicos de internet y venta de accesorios tecnológicos.</p>
                            <p class="leading-relaxed">Con el paso de los años, hemos crecido hasta convertirnos en una empresa reconocida a nivel nacional, expandiendo nuestra oferta de productos y servicios para satisfacer las necesidades cambiantes del mercado tecnológico.</p>
                            <p class="leading-relaxed">Hoy, contamos con un equipo de más de 50 profesionales altamente capacitados y comprometidos con nuestra misión de llevar la mejor tecnología a todos los rincones del país.</p>
                        </div>
                    </div>
                </div>
    `,
    'servicios-link': `

                <div class="w-full">
                    <!-- Título principal -->
                    <h2 class="text-3xl font-bold text-center mb-8 text-red-700">Nuestros Servicios</h2>
                    
                    <!-- Te ofrecemos (Posicionado al centro arriba) -->
                    <div class="bg-red-50 p-6 rounded-lg shadow-lg mb-8 border-l-4 border-red-600 text-center mx-auto max-w-3xl">
                        <h3 class="text-2xl font-bold text-red-700 mb-4">Te ofrecemos</h3>
                        <p class="text-gray-700 mb-2 leading-relaxed">Contamos con un equipo profesional de primer nivel que te brindará asesoramiento de manera personalizada, eficiente y confiable.</p>
                        <p class="text-gray-700 mb-2 leading-relaxed">Despachos directos a tu obra, planta, oficina u hogar.</p>
                        <p class="text-gray-700 leading-relaxed">Talleres de proyectos donde podrás participar de manera gratuita.</p>
                    </div>
                    
                    <!-- Grid de servicios -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-8">
                        <!-- Políticas de envío -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <img src="/perunet/public/img/EMPRESA/servicio.jpg" alt="Envíos a todo el Perú" class="w-full h-48 object-cover rounded-lg mb-4">
                            <h3 class="text-xl font-bold text-red-600 mb-3">Políticas de envío</h3>
                            <p class="text-gray-700 mb-2">¡Realizamos envíos a todo el Perú! Contamos con distintas modalidades de envío y todas llegan a la puerta de tu casa.</p>
                            <p class="text-gray-700 mb-2"><span class="font-semibold">Lima Metropolitana:</span> Tiempo estimado de entrega es de 1 a 2 días hábiles.</p>
                            <p class="text-gray-700 mb-4"><span class="font-semibold">Provincias:</span> Tiempo estimado de entrega es de 8 días hábiles.</p>
                            
                            <p class="text-gray-700 mb-2">Si tienes alguna pregunta, nuestro equipo de atención al cliente está aquí para ayudarte:</p>
                            <p class="text-gray-700 mb-1">Email: <a href="mailto:hola@mazmorragames.com" class="text-blue-600 hover:underline">hola@mazmorragames.com</a></p>
                            <p class="text-gray-700">WhatsApp: <a href="tel:+51912492621" class="text-blue-600 hover:underline">912492621</a></p>
                        </div>
                        
                        <!-- Recojo en tienda -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <img src="/perunet/public/img/EMPRESA/tienda.png" alt="Recojo en tienda" class="w-full h-48 object-cover rounded-lg mb-4">
                            <h3 class="text-xl font-bold text-red-600 mb-3">Recojo en tienda</h3>
                            <p class="text-gray-700">Todos los pedidos que eligieron la opción de recojo en tienda recibirán un WhatsApp para confirmar que su pedido ya se encuentra listo para recojo.</p>
                        </div>

                        <!-- Política de cambios -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <img src="/perunet/public/img/EMPRESA/servicio_cambios.jpg" alt="Política de cambios" class="w-full h-48 object-cover rounded-lg mb-4">
                            <h3 class="text-xl font-bold text-red-600 mb-3">Política de cambios</h3>
                            <p class="text-gray-700 mb-2">Nuestra política de cambio es de 7 días calendario desde la fecha de recepción de tus productos.</p>
                            <ul class="list-disc pl-5 mb-2 text-gray-700">
                                <li class="mb-1">Las devoluciones deben estar sin uso ni signos de desgaste, modificaciones o daños.</li>
                                <li class="mb-1">Se deben presentar en su empaque original.</li>
                                <li class="mb-1">No aplican cambios o devoluciones para productos en descuento.</li>
                            </ul>
                            <p class="text-gray-700">El proceso de devolución es a través de la emisión de una Nota de Crédito.</p>
                        </div>

                        <!-- Fallos de editorial -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <img src="/perunet/public/img/EMPRESA/fallo_editorial.jpg" alt="Fallos de editorial" class="w-full h-48 object-cover rounded-lg mb-4">
                            <h3 class="text-xl font-bold text-red-600 mb-3">Fallos de editorial</h3>
                            <p class="text-gray-700 mb-2">Sentimos mucho si un artículo recibido no está en perfectas condiciones.</p>
                            <p class="text-gray-700 mb-2">Puedes solicitar una revisión para cambiarlo por la misma pieza en caso haya stock o para la emisión de una nota de crédito.</p>
                            <p class="text-gray-700">Contacta a: <a href="mailto:hola@mazmorragames.com" class="text-blue-600 hover:underline">hola@mazmorragames.com</a> detallando nombre, número de DNI, comprobante de compra y fotos del producto.</p>
                        </div>
                    </div>
                </div>



    `,
    'terminos-link': `

                <div class="max-w-4xl mx-auto px-4 py-8">
                    <h2 class="text-3xl font-bold text-center mb-8 text-red-700">Términos y Condiciones</h2>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <p class="text-gray-700 mb-4 leading-relaxed">El acceso y uso de este sitio web se rige por los Términos y Condiciones descritos a continuación, así como por la legislación que se aplique en la República de Perú. En consecuencia, todas las visitas y todos los contratos y transacciones que se realicen en este sitio, como asimismo sus efectos jurídicos, quedarán regidos por estas reglas y sometidas a esta legislación. Los Términos y Condiciones contenidos en este instrumento se aplicarán y se entenderá que forman parte de todos los actos y contratos que se ejecuten o celebren mediante los sistemas de oferta y comercialización comprendidos en este sitio web entre los usuarios de este sitio y PERUNET.PE, la cual se denominará en adelante también en forma indistinta como la empresa.</p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-red-600 mb-3">Requisitos para comprar</h3>
                        <p class="text-gray-700 leading-relaxed">Es requisito para comprar en la presente Tienda Virtual la aceptación de los Términos y Condiciones de ventas descritos a continuación. Cualquier persona que realice una transacción en la Tienda Virtual declara y reconoce, por el hecho de efectuar la compra, que conoce y acepta todos y cada uno de los Términos y Condiciones descritos a continuación. Se entenderán conocidos y aceptados los Términos y Condiciones por el solo hecho del registro y/o la compra de productos a través de este sitio. La empresa se reserva el derecho de actualizar y/o modificar los Términos y Condiciones que detallamos a continuación en cualquier momento, sin previo aviso. Por esta razón recomendamos revisar los Términos y Condiciones cada vez que utilice este Sitio.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-red-600 mb-3">Registro del Usuario</h3>
                        <p class="text-gray-700 leading-relaxed">Para comprar productos en el sitio es necesario estar registrado. En el proceso de registro de cada usuario se verificarán los datos obligatorios. Los datos necesarios para el registro son los siguientes: Nombre, Apellidos, Email, Clave, Ubigeo, Tipo de documento, Número de documento para facturación. Para acceder al registro del usuario, se deberán aceptar los términos y condiciones de la web.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-red-600 mb-3">Clave Secreta</h3>
                        <p class="text-gray-700 leading-relaxed">El usuario dispondrá, una vez registrado, de un email y contraseña que le permitirá el acceso personalizado, confidencial y seguro. El usuario tendrá la posibilidad de cambiar la clave de acceso, para lo cual deberá ingresar a la opción editar mi perfil.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-red-600 mb-3">Derechos del Usuario</h3>
                        <p class="text-gray-700 leading-relaxed">El usuario gozará de todos los derechos que le reconoce la legislación correspondiente vigente en el territorio de Perú, y además los que se le otorgan en estos términos y condiciones.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-red-600 mb-3">Medios de Pago</h3>
                        <p class="text-gray-700 mb-3">Una forma diferente de pago para casos particulares u ofertas de determinados bienes o servicios, podrán ser cancelados utilizando los siguientes medios de pago permitidos en este sitio:</p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-1">
                            <li>Tarjetas de crédito o débito</li>
                            <li>Abono en cuenta</li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-red-600 mb-3">Zona de Envío</h3>
                        <p class="text-gray-700 leading-relaxed">La empresa ofrece envío a Lima y Provincias.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-red-600 mb-3">Vigencia de Precios y Validez de Stock</h3>
                        <p class="text-gray-700 leading-relaxed">Los precios de los productos en este sitio se encuentran vigentes únicamente mientras aparezcan en él. La empresa podrá modificar cualquier información contenida en este sitio, incluyendo las relacionadas con mercaderías, servicios, precios, existencias y condiciones, en cualquier momento y sin previo aviso, hasta el momento de recibir una solicitud de procesamiento de compra.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-red-600 mb-3">Comprobantes de Pago</h3>
                        <p class="text-gray-700 leading-relaxed">Las facturas electrónicas, las boletas de venta electrónicas y las notas electrónicas vinculadas a estos comprobantes electrónicos serán puestas a disposición a través de la página web de la empresa, para lo cual se enviará al correo electrónico designado por el cliente, los datos necesarios para que éste pueda consultar el comprobante electrónico en la señalada web y tenga la posibilidad de descargarlo, de acuerdo al Artículo 15 de la Resolución de Superintendencia N° 097-2012/SUNAT, Resolución de Superintendencia que crea el Sistema de Emisión Electrónica desarrollado desde los sistemas del contribuyente.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold text-red-600 mb-3">Cambios y Devoluciones</h3>
                        <p class="text-gray-700 leading-relaxed">Tus compras pueden ser cambiadas o devueltas sólo por temas de garantía y se procesan en nuestra Tienda en Surco. Tienes 07 días cronológicos para cambios por garantía, siempre y cuando el producto se encuentre sin señales de uso y con empaque original.</p>
                    </div>
                </div>
    `,
};

    // Agregar event listeners a los enlaces con efectos visuales
    const nosotrosLink = document.getElementById('nosotros-link');
    const serviciosLink = document.getElementById('servicios-link');
    const terminosLink = document.getElementById('terminos-link');
    
    // Función para añadir efecto de carga
    function showLoadingEffect(element) {
        // Añadir clase de pulso o animación de carga
        element.classList.add('animate-pulse', 'opacity-70');
        setTimeout(() => {
            element.classList.remove('animate-pulse', 'opacity-70');
        }, 500);
    }

    if (nosotrosLink) {
        nosotrosLink.addEventListener('click', (e) => {
            e.preventDefault();
            showLoadingEffect(nosotrosLink);
            // Añadir clase de transición al contenido
            overlayText.classList.add('opacity-0');
            setTimeout(() => {
                overlayText.innerHTML = sections['nosotros-link'];
                // Mostrar el contenido con animación
                setTimeout(() => {
                    overlayText.classList.remove('opacity-0');
                }, 50);
                showOverlay();
            }, 300);
        });
    }

    if (serviciosLink) {
        serviciosLink.addEventListener('click', (e) => {
            e.preventDefault();
            showLoadingEffect(serviciosLink);
            // Añadir clase de transición al contenido
            overlayText.classList.add('opacity-0');
            setTimeout(() => {
                overlayText.innerHTML = sections['servicios-link'];
                // Mostrar el contenido con animación
                setTimeout(() => {
                    overlayText.classList.remove('opacity-0');
                }, 50);
                showOverlay();
            }, 300);
        });
    }

    if (terminosLink) {
        terminosLink.addEventListener('click', (e) => {
            e.preventDefault();
            showLoadingEffect(terminosLink);
            // Añadir clase de transición al contenido
            overlayText.classList.add('opacity-0');
            setTimeout(() => {
                overlayText.innerHTML = sections['terminos-link'];
                // Mostrar el contenido con animación
                setTimeout(() => {
                    overlayText.classList.remove('opacity-0');
                }, 50);
                showOverlay();
            }, 300);
        });
    }

    // Mejorar el botón de cierre con efectos
    if (closeOverlayButton) {
        // Añadir clases de Tailwind para mejorar la apariencia del botón
        closeOverlayButton.classList.add('transition-transform', 'duration-300', 'hover:scale-110', 'focus:outline-none');
        
        closeOverlayButton.addEventListener('click', () => {
            // Añadir efecto al hacer clic
            closeOverlayButton.classList.add('rotate-90');
            setTimeout(() => {
                closeOverlayButton.classList.remove('rotate-90');
                hideOverlay();
            }, 200);
        });
        
        // También cerrar al hacer clic fuera del contenido
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                hideOverlay();
            }
        });
        
        // Cerrar con la tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !overlay.classList.contains('hidden')) {
                hideOverlay();
            }
        });
    }
    
    // Cerrar overlay al hacer clic fuera del contenido
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            hideOverlay();
        }
    });

    // Aseguramos que el overlay no sea visible al cargar la página
    overlay.classList.add('hidden'); // Forzamos la clase "hidden"

    //console.log('Eventos de seccion.js registrados correctamente');
});
