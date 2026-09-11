<!DOCTYPE html>
<html lang="es">

<!-- components/head.php -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'NewTec - Tecnología a tu alcance') ?></title>

    <!-- Meta tags -->
    <meta name="description" content="<?= htmlspecialchars($description ?? 'NewTec - Tu tienda de confianza para tecnología y computación. Productos de calidad con garantía y soporte técnico.') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($keywords ?? 'tecnología, computadoras, periféricos, gaming, cámaras de seguridad, Perú, Chiclayo') ?>">
    <meta name="author" content="NewTec">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($ogUrl ?? 'https://perunet.pe') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($title ?? 'NewTec - Tecnología a tu alcance') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description ?? 'Tu tienda de confianza para tecnología y computación') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage ?? '/perunet/public/img/EMPRESA/newtec_logo.png') ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= htmlspecialchars($ogUrl ?? 'https://perunet.pe') ?>">
    <meta property="twitter:title" content="<?= htmlspecialchars($title ?? 'NewTec - Tecnología a tu alcance') ?>">
    <meta property="twitter:description" content="<?= htmlspecialchars($description ?? 'Tu tienda de confianza para tecnología y computación') ?>">
    <meta property="twitter:image" content="<?= htmlspecialchars($ogImage ?? '/perunet/public/img/EMPRESA/newtec_logo.png') ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/perunet/public/img/EMPRESA/p.png">
    <link rel="apple-touch-icon" href="/perunet/public/img/EMPRESA/p.png">

    <!-- Estilos globales -->
    <link rel="stylesheet" href="/perunet/public/css/index.css">

    <!-- Estilos específicos de página -->
    <?php if (!empty($style)): ?>
        <link rel="stylesheet" href="/perunet/public/css/<?= htmlspecialchars($style) ?>.css">
    <?php endif; ?>

    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-pO/aPn+EyC2RgXqPhBzHxcSDY4Dx2I4Nzh75QFIF17BglN1AuJ0W++OxDKX09/7z" crossorigin="anonymous"> -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Preconnect para mejor rendimiento -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>