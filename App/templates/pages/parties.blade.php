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
    'categories' => [
      [
        'titre' => 'Fantaisie',
        'parties' => $parties_suggestions_fantasy ?? []
      ],
      [
        'titre' => 'Nouvelle campagne',
        'parties' => $parties_suggestions_campaign ?? []
      ]
    ]
  ])
  
  {{-- La semaine prochaine --}}
  @include('components.parties.liste-parties', [
    'titre_section' => 'La semaine prochaine',
    'categories' => [
      [
        'titre' => 'Vendredi de 20h à 0h',
        'parties' => $parties_vendredi ?? []
      ],
      [
        'titre' => 'Samedi de 14h à 20H',
        'parties' => $parties_samedi ?? []
      ]
    ]
  ])
</div>
@endsection

@section('scripts')
  @include('components.parties.scripts')
@endsection
