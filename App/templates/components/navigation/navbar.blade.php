{{-- /var/www/html/App/templates/components/navigation/navbar.blade.php --}}
<div class="navbar bg-base-100 px-4 shadow-sm sticky top-0 z-9">
  <div class="flex-none lg:hidden">
    <label for="my-drawer" class="btn btn-square btn-ghost">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </label>
  </div>
  
  <div class="navbar-start">
    @include('components.navigation.breadcrumbs')
  </div>

  <div class="navbar-center hidden lg:block">
    <span class="text-xl font-ravenholm-bold">LBDR</span>
  </div>

  <div class="navbar-end gap-2">
    {{-- Date avec icône calendrier --}}
    <div class="border border-base-300 rounded-lg px-3 py-1 flex items-center gap-2 hidden md:inline-flex">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9.75v7.5" />
      </svg>
     
      <span class="text-sm capitalize">
        {{ $currentDateTime->translatedFormat('D j M Y') }}
      </span>
    </div>

    {{-- Bouton notifications avec indicateur --}}
    <div class="indicator">
      <span class="indicator-item badge badge-xs badge-secondary"></span> 
      <button class="btn btn-ghost btn-circle">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
      </button>
    </div>

    {{-- Bouton changement de thème --}}
    <div class="dropdown dropdown-end">
      <button tabindex="0" role="button" class="btn btn-ghost btn-circle">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
        </svg>
      </button>
      <ul tabindex="0" class="dropdown-content z-10 menu p-2 shadow bg-base-100 rounded-box w-52">
        <li><a data-set-theme="light">Light</a></li>
        <li><a data-set-theme="dark">Dark</a></li>
        <li><a data-set-theme="cupcake">Cupcake</a></li>
        <li><a data-set-theme="bumblebee">Bumblebee</a></li>
        <li><a data-set-theme="caramellatte">Caramellatte</a></li>
        <li><a data-set-theme="retro">retro</a></li>
        <li><a data-set-theme="sunset">sunset</a></li>
        <li><a data-set-theme="pastel">pastel</a></li>
      </ul>
    </div>
  </div>
</div>
