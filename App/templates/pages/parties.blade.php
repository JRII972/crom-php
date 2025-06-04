{{-- Page principale des parties --}}
@extends('layouts.base')

@section('content')
<div class="mx-auto w-full lg:max-w-[1200px]">
  {{-- Header avec titre et barre de recherche --}}
  @include('components.parties.header-search')

  {{-- Filtres additionnels --}}
  @include('components.parties.filtres')
</div>

{{-- Bannière héro --}}
@include('components.parties.hero-banner')

{{-- Contenu principal --}}
<div class="mx-auto lg:max-w-[1200px] w-full">
  {{-- Suggestions ("Pourrais vous intéresser") --}}
  @include('components.parties.liste-parties', [
    'titre_section' => 'Pourrais vous intérésser !',
    'sections' => $suggestion
  ])
  
  {{-- La semaine prochaine --}}
  @include('components.parties.liste-parties', [
    'titre_section' => 'La semaine prochaine',
    'sections' => $next_week
  ])
</div>

  <!-- Bouton "Voir plus" -->
  <div class="flex justify-center mt-6">
    <button class="btn btn-outline">Voir plus de parties</button>
  </div>
@endsection

@section('scripts')
  @include('components.parties.scripts')
@endsection
