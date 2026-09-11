<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset(
                $title
            ) ? $title . ' - NewTec' : 'NewTec' ?></title>
    <link rel="icon" href="/perunet/public/img/EMPRESA/p.png">

    <!-- jQuery (debe ir antes de cualquier script que use $) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Otros meta y estilos globales aquí -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/perunet/public/assets/css/app.css?v=<?= time() ?>">
    <script src="/perunet/public/assets/js/app.js"></script>
    <script src="/perunet/public/js/filtros.js"></script>

    <?php if (isset($extraHead)) echo $extraHead; ?>
</head>

<body class="bg-gray-50 min-h-scree">
    <!-- Header global -->
    <?php include __DIR__ . '/../../components/headerNav.php'; ?>

    <!-- Contenido principal -->
    <main class="min-h-screen">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer global -->
    <?php include __DIR__ . '/../../components/footer.php'; ?>

    <!-- Atención al cliente flotante -->
    <?php include __DIR__ . '/../../components/helpSection.php'; ?>

    <!-- Botón flotante Configurador -->
    <?php include __DIR__ . '/../../components/builder_button.php'; ?>

    <!-- Scripts globales aquí -->
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
    <?php if (isset($_SESSION['usuario']['id_us'])): ?>

        

        <script>
            window.PERUNET_USER_ID = <?= (int)$_SESSION['usuario']['id_us'] ?>;
        </script>
    <?php endif; ?>

    <!-- scripts secciones (nosotros, termino y condiciones-->
    <script src="/perunet/public/js/seccion.js" defer></script>
</body>

</html>