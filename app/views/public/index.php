<?php
$title = "Inicio - PeruNet";
// Obtener los IDs reales de las categorías destacadas
$idGamer = null;
$idVideo = null;
$idCableado = null;
if (
    isset(
    $categorias
) && is_array($categorias)
) {
    foreach ($categorias as $cat) {
        $nombre = strtolower(trim($cat['nombre']));
        if (strpos($nombre, 'gamer') !== false)
            $idGamer = $cat['id_cat'];
        if (strpos($nombre, 'videovigilancia') !== false)
            $idVideo = $cat['id_cat'];
        if (strpos($nombre, 'cableado') !== false)
            $idCableado = $cat['id_cat'];
    }
}
?>
<!-- Banner Principal (Slider) Hero -->
<div id="banner-slider"
    class="relative w-screen min-h-[80vh] max-h-[100vh] mx-auto mt-0 rounded-none overflow-hidden shadow-2xl border-b border-gray-200 flex items-center justify-center">
    <!-- Mensaje centrado sobre el banner -->
    <div
        class="absolute z-20 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center">
        <div class="bg-black/50 px-8 py-6 rounded-lg shadow-xl">
            <h1 class="text-4xl md:text-6xl font-bold text-white text-center drop-shadow-lg">Bienvenido a
                NewTec<br><span class="text-lg md:text-2xl font-light">Tecnología y Soluciones</span></h1>
        </div>
    </div>
    <div class="slider-wrapper w-full h-full">
        <img src="/perunet/public/img/EMPRESA/Banner1.jpg"
            class="slider-img w-full h-[80vh] max-h-[100vh] object-cover object-center hidden transition-all duration-700"
            alt="Banner 1">
        <img src="/perunet/public/img/EMPRESA/Banner2.jpg"
            class="slider-img w-full h-[80vh] max-h-[100vh] object-cover object-center hidden transition-all duration-700"
            alt="Banner 2">
        <img src="/perunet/public/img/EMPRESA/Banner3.jpg"
            class="slider-img w-full h-[80vh] max-h-[100vh] object-cover object-center hidden transition-all duration-700"
            alt="Banner 3">
        <img src="/perunet/public/img/EMPRESA/BannerPromocional.jpg"
            class="slider-img w-full h-[80vh] max-h-[100vh] object-cover object-center hidden transition-all duration-700"
            alt="Banner Promocional">
    </div>
    <!-- Flechas -->
    <button id="prev-banner"
        class="absolute left-8 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-4 shadow-lg z-30 border border-gray-300 text-3xl">
        &#8592;
    </button>
    <button id="next-banner"
        class="absolute right-8 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-4 shadow-lg z-30 border border-gray-300 text-3xl">
        &#8594;
    </button>
    <!-- Indicadores -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-4 z-30">
        <span
            class="dot w-5 h-5 bg-white/90 rounded-full cursor-pointer border-2 border-gray-400 transition-all"></span>
        <span
            class="dot w-5 h-5 bg-white/90 rounded-full cursor-pointer border-2 border-gray-400 transition-all"></span>
        <span
            class="dot w-5 h-5 bg-white/90 rounded-full cursor-pointer border-2 border-gray-400 transition-all"></span>
        <span
            class="dot w-5 h-5 bg-white/90 rounded-full cursor-pointer border-2 border-gray-400 transition-all"></span>
    </div>
</div>

<!-- ================= CATEGORÍAS DESTACADAS ================= -->
<section id="categorias-destacadas" class="w-full max-w-7xl mx-auto py-8 px-2 md:px-0">
    <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">NUESTRAS CATEGORÍAS DESTACADAS</h2>
    <div class="flex flex-wrap justify-center gap-4 md:gap-8 mb-8">
        <!-- Gamer -->
        <button
            class="categoria-btn group flex flex-col items-center bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105 focus:outline-none"
            data-categoria="<?= $idGamer ?>" style="width:200px;">
            <img src="/perunet/public/img/EMPRESA/Categoria2.jpg" alt="Gamer"
                class="w-full h-[180px] max-h-[200px] object-cover rounded-t-lg transition-transform duration-200 group-hover:scale-105 group-focus:scale-105 md:h-[140px] md:max-h-[160px] sm:h-[100px] sm:max-h-[120px]"
                />
            <span class="py-3 text-lg font-semibold group-hover:text-red-600">GAMER</span>
        </button>
        <!-- Videovigilancia -->
        <button
            class="categoria-btn group flex flex-col items-center bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105 focus:outline-none"
            data-categoria="<?= $idVideo ?>" style="width:200px;">
            <img src="/perunet/public/img/EMPRESA/Categoria1.jpg" alt="Videovigilancia"
                class="w-full h-[180px] max-h-[200px] object-cover rounded-t-lg transition-transform duration-200 group-hover:scale-105 group-focus:scale-105 md:h-[140px] md:max-h-[160px] sm:h-[100px] sm:max-h-[120px]"
                />
            <span class="py-3 text-lg font-semibold group-hover:text-red-600">VIDEOVIGILANCIA</span>
        </button>
        <!-- Cableado Estructurado -->
        <button
            class="categoria-btn group flex flex-col items-center bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105 focus:outline-none"
            data-categoria="<?= $idCableado ?>" style="width:200px;">
            <img src="/perunet/public/img/EMPRESA/Categoria3.jpg" alt="Cableado Estructurado"
                class="w-full h-[180px] max-h-[200px] object-cover rounded-t-lg transition-transform duration-200 group-hover:scale-105 group-focus:scale-105 md:h-[140px] md:max-h-[160px] sm:h-[100px] sm:max-h-[120px]"
                />
            <span class="py-3 text-lg font-semibold group-hover:text-red-600">CABLEADO ESTRUCTURADO</span>
        </button>
    </div>
</section>

<!-- ================= CONTENEDOR DE PRODUCTOS DINÁMICOS ================= -->
<section id="productos-section" class="w-full max-w-7xl mx-auto pb-12 px-2 md:px-0">
    <div id="productos" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <!-- Aquí se cargarán los productos por AJAX -->
    </div>
</section>
<!-- ================= SECCIÓN DE AYUDA Y CONTACTO ================= -->
<div
    class="w-full max-w-7xl mx-auto my-8 flex flex-col md:flex-row items-center justify-between bg-white rounded-lg shadow p-6">
    <div>
        <h3 class="text-lg font-semibold mb-1">¿Necesitas ayuda?</h3>
        <p class="text-gray-600">Contáctate con un especialista</p>
    </div>
    <a href="/perunet/contacto"
        class="mt-4 md:mt-0 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-full flex items-center gap-2 transition-colors">
        <span class="fa fa-life-ring"></span> CONTACTO
    </a>
</div>

<!-- ================= ESTILOS RESPONSIVOS ================= -->
<style>
    #categorias-destacadas .categoria-btn {
        min-width: 160px;
        max-width: 220px;
    }

    #categorias-destacadas .categoria-btn img {
        width: 100%;
        height: 140px;
        max-height: 180px;
        object-fit: cover;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        transition: transform 0.18s;
    }

    #categorias-destacadas .categoria-btn:hover img,
    #categorias-destacadas .categoria-btn:focus img {
        transform: scale(1.07);
    }

    @media (max-width: 1024px) {
        #categorias-destacadas .categoria-btn img {
            height: 120px;
            max-height: 140px;
        }
    }

    @media (max-width: 768px) {
        #categorias-destacadas .categoria-btn img {
            height: 100px;
            max-height: 120px;
        }

        #categorias-destacadas .categoria-btn {
            min-width: 90vw;
            max-width: 100vw;
        }
    }

    #productos .producto {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.10);
        padding: 2rem 1.2rem 1.5rem 1.2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: box-shadow 0.25s, transform 0.18s;
        min-height: 390px;
        max-width: 320px;
        margin: 0 auto;
        border: 1.5px solid #f3f4f6;
    }

    #productos .producto:hover {
        box-shadow: 0 8px 32px rgba(239, 68, 68, 0.18);
        transform: translateY(-6px) scale(1.035);
        border-color: #fecaca;
    }

    #productos .img-producto {
        width: 120px;
        height: 120px;
        object-fit: contain;
        background: #f9fafb;
        border-radius: 12px;
        margin-bottom: 1.2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e7eb;
    }

    #productos .producto h3 {
        font-size: 1.13rem;
        font-weight: 700;
        color: #22223b;
        margin-bottom: 0.2rem;
        text-align: center;
    }

    #productos .producto p {
        font-size: 1rem;
        color: #6b7280;
        margin-bottom: 0.2rem;
        text-align: center;
    }

    #productos .producto h4 {
        font-size: 0.98rem;
        color: #374151;
        font-weight: 400;
        margin-bottom: 0.7rem;
        text-align: center;
        min-height: 38px;
    }

    #productos .producto .marca {
        color: #ef4444;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.1rem;
    }

    #productos .producto .precio {
        color: #16a34a;
        font-weight: 700;
        font-size: 1.15rem;
        margin-bottom: 0.2rem;
    }

    #productos .btn-ver-mas {
        margin-top: auto;
        background: #ef4444;
        color: #fff;
        padding: 0.65rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.08rem;
        text-decoration: none;
        transition: background 0.18s, transform 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.5em;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.08);
        border: none;
    }

    #productos .btn-ver-mas:hover {
        background: #b91c1c;
        transform: scale(1.07);
    }

    #productos .btn-ver-mas .icon-search {
        display: inline-block;
        width: 1.1em;
        height: 1.1em;
        margin-right: 0.2em;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        #productos .producto {
            max-width: 100%;
        }
    }
</style>

<script>
    // Slider JS
    const images = document.querySelectorAll('#banner-slider .slider-img');
    const dots = document.querySelectorAll('#banner-slider .dot');
    let current = 0;
    let interval;

    function showSlide(idx) {
        images.forEach((img, i) => {
            img.classList.toggle('hidden', i !== idx);
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-blue-500', i === idx);
            dot.classList.toggle('border-blue-500', i === idx);
            dot.classList.toggle('scale-125', i === idx);
        });
        current = idx;
    }

    function nextSlide() {
        showSlide((current + 1) % images.length);
    }

    function prevSlide() {
        showSlide((current - 1 + images.length) % images.length);
    }

    function startAutoSlide() {
        interval = setInterval(nextSlide, 4000);
    }

    function stopAutoSlide() {
        clearInterval(interval);
    }

    document.getElementById('next-banner').onclick = () => {
        stopAutoSlide();
        nextSlide();
        startAutoSlide();
    };
    document.getElementById('prev-banner').onclick = () => {
        stopAutoSlide();
        prevSlide();
        startAutoSlide();
    };
    dots.forEach((dot, i) => {
        dot.onclick = () => {
            stopAutoSlide();
            showSlide(i);
            startAutoSlide();
        };
    });

    // Inicializar
    showSlide(0);
    startAutoSlide();
</script>

<!-- ================= JS PARA CATEGORÍAS DESTACADAS Y AJAX ================= -->
<script>
    // Función AJAX para cargar productos por categoría
    function getCategorias(id_categoria) {
        fetch('/perunet/public/php/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'accion=getCategorias&id_categoria=' + encodeURIComponent(id_categoria)
        })
            .then(response => response.text())
            .then(data => {
                // Actualiza la URL sin recargar la página
                if (history.pushState) {
                    history.pushState(null, '', '/perunet/index');
                }
                // Actualiza la lista de productos
                document.getElementById('productos').innerHTML = data;
            });
    }

    // ================= CATEGORÍAS DESTACADAS =================
    document.addEventListener('DOMContentLoaded', function () {
        const categoriaBtns = document.querySelectorAll('.categoria-btn');
        categoriaBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const id_categoria = btn.getAttribute('data-categoria');
                getCategorias(id_categoria);
            });
        });
        // Cargar productos de la primera categoría por defecto (ejemplo: gamer)
        getCategorias('<?= $idGamer ?>');
    });
</script>