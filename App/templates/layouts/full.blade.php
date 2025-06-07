<!doctype html>
<html lang="en" data-theme="caramellatte">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="./vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $page_title ?? 'CROM | LBDR'}}</title>
    <link rel="stylesheet" crossorigin href="./assets/css/index.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" crossorigin href="./assets/css/module.css">
    @yield('head')
  </head>
  <body>
    {{-- Structure principale du drawer --}}
    <div class="drawer lg:drawer-open h-screen">
      <input id="my-drawer" type="checkbox" class="drawer-toggle" />

      {{-- Drawer sidebar - Composant --}}
      @include('components.navigation.sidebar')

      <div class="drawer-content flex flex-col overflow-auto">
        {{-- Navbar - Composant --}}
        @include('components.navigation.navbar')

        {{-- Main content --}}
        <main class="flex-1 bg-base-100 " id="root">
          <div class="mx-auto w-full pb-6">
            @yield('content')
          </div>            
        </main>
      </div>
    </div>

    {{-- Scripts - Composant --}}
    @include('components.scripts')
  </body>
</html>
