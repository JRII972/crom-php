{{-- Page principale des activites --}}
@extends('layouts.base')

@section('content')
<div class="mx-auto w-full lg:max-w-[1200px]">
  {{-- Header avec titre et barre de recherche --}}
  @include('components.activites.header-search')

  {{-- Filtres additionnels --}}
  @include('components.activites.filtres')
</div>

{{-- Bannière héro --}}
@include('components.activites.hero-banner')

{{-- Contenu principal --}}
<div class="mx-auto lg:max-w-[1200px] w-full">
  {{-- Suggestions ("Pourrais vous intéresser") --}}
  @include('components.activites.liste-activites', [
    'titre_section' => 'Pourrais vous intérésser !',
    'sections' => $suggestion
  ])
  
  {{-- La semaine prochaine --}}
  @include('components.activites.liste-activites', [
    'titre_section' => 'La semaine prochaine',
    'sections' => $next_week
  ])
</div>

  <!-- Bouton "Voir plus" -->
  <div class="flex justify-center mt-6">
    <button class="btn btn-outline">Voir plus de activites</button>
  </div>
@endsection

@section('scripts')
  @include('components.activites.scripts')
@endsection
