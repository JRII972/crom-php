{{-- Page principale des activites --}}
@extends('layouts.base')

@section('content')
<div class="mx-auto w-full lg:max-w-[1200px]">
  {{-- Header avec titre et barre de recherche --}}
  @include('components.activites.header-search')

  {{-- Filtres additionnels --}}
</div>

@include('components.activites.filtres')

{{-- Contenu principal --}}
<div class="mx-auto lg:max-w-[1200px] w-full">
  @include('components.activites.liste-activites', [
    'titre_section' => 'Pourrais vous intérésser !',
    'sections' => $suggestion
  ])
  
  @foreach ($sections as $title => $sectionData )
    @include('components.activites.liste-activites', [
      'titre_section' => $title,
      'sections' => $sectionData
    ])
  @endforeach
</div>

  <!-- Bouton "Voir plus" -->
  <div class="flex justify-center mt-6">
    <button class="btn btn-outline">Voir plus de activites</button>
  </div>
@endsection

@section('scripts')
  @include('components.activites.scripts')
@endsection
