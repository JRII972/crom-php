{{-- Page détaillée d'une activite --}}
@extends('layouts.base')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
  {{-- Colonne de gauche - Image et info de base --}}
  <div class="col-span-1 hidden md:block">
    {{-- Informations de base de la activite --}}
    @include('components.activite.info-activite', ['activite' => $activite ?? []])
    
    {{-- Liste des joueurs inscrits --}}
    @include('components.activite.liste-joueurs', ['joueurs' => $joueurs ?? []])
  </div>
  
  {{-- Colonne droite - Description et sessions --}}
  <div class="col-span-1 lg:col-span-2">
    <div class="card bg-base-200 shadow-xl h-full">
      <div class="card-body">
        {{-- Onglets --}}
        @php $activeTab = $activeTab ?? 'description'; @endphp
        @include('components.activite.onglets', ['activeTab' => $activeTab, 'sessions' => $sessions ?? []])
        
        {{-- Description de la activite --}}
        @include('components.activite.description-activite', ['activite' => $activite ?? []])
        
        {{-- Liste des sessions --}}
        @include('components.activite.liste-sessions', ['sessions' => $sessions ?? [], 'activeTab' => $activeTab])

        @include('components.activite.gestion-jouers', ['activeTab' => $activeTab])

        @include('components.activite.gestion-session', ['sessions' => $sessions ?? [], 'activeTab' => $activeTab])
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  @include('components.activite.scripts')
@endsection