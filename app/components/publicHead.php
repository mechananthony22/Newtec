<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'PeruNet - Tu tienda de tecnología' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/perunet/public/assets/img/favicon.ico">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fix: Forzar clases de opacidad para slider -->
    <style>
        .opacity-0 { opacity: 0 !important; }
        .opacity-50 { opacity: 0.5 !important; }
        .opacity-100 { opacity: 1 !important; }
    </style>
    
    <!-- Font Awesome (comentado temporalmente) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Meta tags -->
    <meta name="description" content="<?= isset($description) ? $description : 'PeruNet - Tu tienda de confianza para tecnología y computación' ?>">
    <meta name="keywords" content="<?= isset($keywords) ? $keywords : 'tecnología, computadoras, periféricos, gaming, cámaras de seguridad, Perú, Chiclayo' ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= isset($title) ? $title : 'PeruNet' ?>">
    <meta property="og:description" content="<?= isset($description) ? $description : 'PeruNet - Tu tienda de tecnología' ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= isset($ogUrl) ? $ogUrl : '/perunet' ?>">
    <?php if (isset($ogImage)): ?>
        <meta property="og:image" content="<?= $ogImage ?>">
    <?php endif; ?>
    
    <!-- Preconnect para mejor rendimiento -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <!-- Funciones generales -->
    <script src="/perunet/public/js/funciones.js?v=<?= time() ?>"></script>
    
    <!-- Scripts específicos de página -->
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Script de verificación inmediata -->
    <script>
        console.log('=== VERIFICACIÓN INMEDIATA ===');
        console.log('Archivo funciones.js cargado con timestamp:', <?= time() ?>);
        console.log('jQuery disponible:', typeof $ !== 'undefined');
        
        // Verificar que no hay errores al cargar
        window.addEventListener('error', function(e) {
            console.error('Error detectado:', e.error);
            console.error('Archivo:', e.filename);
            console.error('Línea:', e.lineno);
        });
    </script>
    
    <!-- Estilos específicos de página -->
    <!-- <?php if (isset($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link rel="stylesheet" href="<?= $style ?>">
        <?php endforeach; ?>
    <?php endif; ?> -->
    
    <!-- Estilos para Font Awesome -->
    <style>
        /* Asegurar que Font Awesome se muestre correctamente */
        .fas, .fab, .far {
            display: inline-block !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            -webkit-font-smoothing: antialiased !important;
        }
        
        /* Asegurar que las imágenes se muestren correctamente */
        /* img {
            max-width: 100%;
            height: auto;
        } */
    </style>
</head> 