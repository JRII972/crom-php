{{-- Page détaillée d'une partie --}}
@extends('layouts.base')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
  {{-- Colonne de gauche - Image et info de base --}}
  <div class="col-span-1">
    {{-- Informations de base de la partie --}}
    @include('components.partie.info-partie', ['partie' => $partie ?? []])
    
    {{-- Liste des joueurs inscrits --}}
    @include('components.partie.liste-joueurs', ['joueurs' => $joueurs ?? []])
  </div>
  
  {{-- Colonne droite - Description et sessions --}}
  <div class="col-span-1 lg:col-span-2">
    <div class="card bg-base-200 shadow-xl h-full">
      <div class="card-body">
        {{-- Onglets --}}
        @php $activeTab = $activeTab ?? 'description'; @endphp
        @include('components.partie.onglets', ['activeTab' => $activeTab, 'sessions' => $sessions ?? []])
        
        {{-- Description de la partie --}}
        @include('components.partie.description-partie', ['partie' => $partie ?? []])
        
        {{-- Liste des sessions --}}
        @include('components.partie.liste-sessions', ['sessions' => $sessions ?? [], 'activeTab' => $activeTab])
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  @include('components.partie.scripts')
@endsection