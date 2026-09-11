<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? $title : 'Panel de Administración' ?></title>
  <link rel="icon" href="/perunet/public/img/EMPRESA/p.png?v=2.0">

  <!-- Tailwind CSS CDN oficial con soporte dark -->
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: '#2563eb',
            darkbg: '#18181b',
            darkpanel: '#23232a',
          }
        }
      }
    }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Script para aplicar modo oscuro al cargar -->
  <script>
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  </script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- dashboardNav -->
  <script src="/perunet/public/js/dashboardNav.js"></script>
  <!-- Estilos adicionales para páginas específicas -->
  <?php if (isset($pageStyle)): ?>
    <link rel="stylesheet" href="/perunet/public/css/ventasResumen.css">
  <?php endif; ?>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>