{{-- /var/www/html/App/templates/components/navigation/sidebar.blade.php --}}
<div class="drawer-side min-h-full fixed top-0 bottom-0 z-10"> 
  <label for="my-drawer" class="drawer-overlay"></label>
  <div class="flex flex-col h-full bg-base-200 w-3xs">
    
    {{-- Logo et nom de l'association en haut --}}
    @include('components.navigation.logo')
    
    {{-- Menu navigation principal --}}
    @include('components.navigation.main-menu')
    
    {{-- Espace flexible pour pousser le menu utilisateur vers le bas --}}
    <div class="flex-grow"></div>

    {{-- Menu utilisateur en bas --}}
    @include('components.user.user-menu')
    
    {{-- Profil utilisateur déplacé en bas --}}
    @include('components.user.user-profile')
    
  </div>
</div>
