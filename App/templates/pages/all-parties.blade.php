{{-- Page principale des parties --}}
@extends('layouts.base')

@section('content')
<div class="mx-auto w-full lg:max-w-[1200px]">
  {{-- Header avec titre et barre de recherche --}}
  @include('components.parties.header-search')

  {{-- Filtres additionnels --}}
</div>

@include('components.parties.filtres')

{{-- Contenu principal --}}
<div class="mx-auto lg:max-w-[1200px] w-full">
  @include('components.parties.liste-parties', [
    'titre_section' => 'Pourrais vous intérésser !',
    'sections' => $suggestion
  ])
  
  @foreach ($sections as $title => $sectionData )
    @include('components.parties.liste-parties', [
      'titre_section' => $title,
      'sections' => $sectionData
    ])
  @endforeach
</div>

  <!-- Bouton "Voir plus" -->
  <div class="flex justify-center mt-6">
    <button class="btn btn-outline">Voir plus de parties</button>
  </div>
@endsection

@section('scripts')
  @include('components.parties.scripts')
@endsection
