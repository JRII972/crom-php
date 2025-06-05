<!DOCTYPE html>
<html lang="fr" data-theme="caramellatte">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $pageTitle ?? 'LBDR' }} - LBDR</title>
  <link rel="stylesheet" crossorigin href="/assets/css/module.css">
  <style>
    @font-face {
      font-family: 'Ravenholm-Bold';
      src: url('/fonts/Ravenholm-Bold.ttf') format('truetype');
      font-weight: bold;
      font-style: normal;
    }
    .font-ravenholm-bold {
      font-family: 'Ravenholm-Bold', sans-serif;
    }
  </style>
  <script src="assets/js/main.js"></script>
</head>
<body class="bg-base-200">
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full h-screen flex max-w-4xl">
      <div class="card bg-base-100 shadow-2xl h-fit m-auto" style="height: fit-content; margin: auto;">
        <div class="card-body">            
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts JavaScript -->
  <script src="assets/js/utils.js"></script>
  @yield('scripts')
</body>
</html>
