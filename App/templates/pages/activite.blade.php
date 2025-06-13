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
        <div class="tabs">
          <input type="radio" name="main_tabs" class="tab  md:hidden" aria-label="Détail" />
          <div id="tab-content-detail" class="tab-content md:hidden">
            @include('components.activite.info-activite', ['activite' => $activite ?? []])

            {{-- Liste des joueurs inscrits --}}
            @include('components.activite.liste-joueurs', ['joueurs' => $joueurs ?? []])
          </div>


          {{-- Description de la activite --}}
          <input type="radio" name="main_tabs" class="tab" aria-label="Description" {{ $activeTab === 'description' ? '' : 'checked="checked"' }} />
          @include('components.activite.description-activite', ['activite' => $activite ?? []])

          {{-- Liste des sessions --}}
          <input type="radio" name="main_tabs" class="tab" aria-label="Sessions @if (count($nextSessions) > 0) ({{ count($nextSessions) }}) @endif " {{ $activeTab === 'sessions' ? '' : 'checked="checked"' }} />
          @include('components.activite.liste-sessions', ['sessions' => $sessions ?? [], 'activeTab' => $activeTab])

          <input type="radio" name="main_tabs" class="tab" aria-label="Gestion des joueurs" {{ $activeTab === 'joueurs' ? '' : 'checked="checked"' }} />
          @include('components.activite.gestion-joueurs', ['activeTab' => $activeTab])

          <input type="radio" name="main_tabs" class="tab" aria-label="Gestion des sessions" {{ $activeTab === 'sessions-gestion' ? '' : 'checked="checked"' }} />
          @include('components.activite.gestion-session', ['sessions' => $sessions ?? [], 'activeTab' => $activeTab])
        </div>

      </div>
    </div>
  </div>
</div>

@include('components.activite.modal-session')

@endsection

@section('scripts')
@include('components.activite.scripts')
@endsection