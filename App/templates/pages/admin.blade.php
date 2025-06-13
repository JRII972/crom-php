{{-- Page détaillée d'une activite --}}
@extends('layouts.base')

@section('head')
<!-- Grid.js JS -->
<script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>

@endsection

@section('content')
<div class="mx-auto w-full max-w-7xl">
  <div role="tablist" class="tabs tabs-lift">

    <!-- Tab principal: Utilisateurs -->
    <input type="radio" name="main_tabs" role="tab" class="tab" aria-label="Utilisateurs" id="tab-users" checked="checked" />
    @include('components.admin.tab-users')

    <!-- Tab principal: Jeux -->
    <input type="radio" name="main_tabs" role="tab" class="tab" aria-label="Jeux" id="tab-games" />
    @include('components.admin.tab-jeux')

    <!-- Tab principal: Lieux -->
    <input type="radio" name="main_tabs" role="tab" class="tab" aria-label="Lieux" id="tab-locations" />
    @include('components.admin.tab-lieux')

    <!-- Tab principal: Paramètres -->
    <input type="radio" name="main_tabs" role="tab" class="tab" aria-label="Paramètres" id="tab-settings" />
    @include('components.admin.tab-params')
  </div>
</div>

@include('components.admin.modal-jeux')
@include('components.admin.modal-lieux')

@endsection

@section('scripts')
<script>
  // Configuration et initialisation de Grid.js pour la table des jeux

  // Fonctions pour les actions des boutons
  function editGame(button) {
    const row = button.closest('tr');
    const gameName = row.querySelector('td:first-child .font-bold').textContent;
    console.log('Modifier le jeu:', gameName);
    // Ici vous pouvez ajouter la logique pour modifier le jeu
    alert(`Fonctionnalité de modification pour "${gameName}" à implémenter`);
  }

  function deleteGame(button) {
    const row = button.closest('tr');
    const gameName = row.querySelector('td:first-child .font-bold').textContent;

    if (confirm(`Êtes-vous sûr de vouloir supprimer le jeu "${gameName}" ?`)) {
      console.log('Supprimer le jeu:', gameName);
      // Ici vous pouvez ajouter la logique pour supprimer le jeu
      row.remove();
      alert(`Le jeu "${gameName}" a été supprimé`);
    }
  }
</script>
@endsection