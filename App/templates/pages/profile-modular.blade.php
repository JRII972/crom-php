{{-- Page de profil modulaire --}}
@extends('layouts.base')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    {{-- Colonne de gauche - Infos de base et photo --}}
    <div class="col-span-1">
        {{-- Carte de profil utilisateur --}}
        @include('components.profile.user-card', ['user' => $user ?? null])

        {{-- Statistiques --}}
        @include('components.profile.stats-card', ['user' => $user ?? null, 'stats' => $stats ?? null])
    </div>
    
    {{-- Colonne centrale et droite - Tabs et contenus --}}
    <div class="col-span-1 lg:col-span-2">
        <div class="card bg-base-200 shadow-xl h-full">
            <div class="card-body">
                {{-- Navigation par onglets --}}
                @include('components.profile.tabs-navigation', ['activeTab' => $activeTab ?? 'activites'])
                
                {{-- Contenu des onglets --}}
                @include('components.profile.tab-activites', ['activites' => $activites ?? null, 'activeTab' => $activeTab ?? 'activites'])
                @include('components.profile.tab-disponibilites', ['disponibilites' => $disponibilites ?? null, 'calendar' => $calendar ?? null, 'activeTab' => $activeTab ?? 'activites'])
                @include('components.profile.tab-historique', ['historique' => $historique ?? null, 'activeTab' => $activeTab ?? 'activites'])
                @include('components.profile.tab-preferences', ['preferences' => $preferences ?? null, 'calendarUrl' => $calendarUrl ?? null, 'activeTab' => $activeTab ?? 'activites'])
                @include('components.profile.tab-paiements', ['paiements' => $paiements ?? null, 'adhesion' => $adhesion ?? null, 'activeTab' => $activeTab ?? 'activites'])
            </div>
        </div>
    </div>
</div>

{{-- Modal d'édition de profil --}}
@include('components.profile.edit-modal', ['user' => $user ?? null])
@endsection

@section('scripts')
    @parent
    @include('components.profile.scripts')
@endsection
