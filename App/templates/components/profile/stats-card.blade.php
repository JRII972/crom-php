{{-- Statistiques de l'utilisateur --}}
<div class="card bg-base-200 shadow-xl mt-6">
  <div class="card-body">
    <h3 class="card-title text-xl mb-4">Statistiques</h3>
    
    <div class="stats stats-vertical shadow w-full">
      <div class="stat">
        <div class="stat-title">Membre depuis</div>
        <div class="stat-value text-xl text-primary" id="profile-member-since">
          @if(isset($user->created_at))
            {{ \Carbon\Carbon::parse($user->created_at)->diffForHumans(['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
          @else
            3 ans
          @endif
        </div>
        <div class="stat-desc" id="profile-member-date">
          @if(isset($user->created_at))
            Inscription le {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}
          @else
            Inscription le 15/06/2022
          @endif
        </div>
      </div>
      
      <div class="stat">
        <div class="stat-title">Activites jouées</div>
        <div class="stat-value text-xl text-secondary">{{ $stats->activites_jouees ?? 42 }}</div>
        <div class="stat-desc">
          @if(isset($stats->pourcentage_activites))
            {{ $stats->pourcentage_activites > 0 ? '+' : '' }}{{ $stats->pourcentage_activites }}% par rapport à l'année dernière
          @else
            +8% par rapport à l'année dernière
          @endif
        </div>
      </div>
      
      <div class="stat">
        <div class="stat-title">Activites créées</div>
        <div class="stat-value text-xl text-accent">{{ $stats->activites_creees ?? 7 }}</div>
        <div class="stat-desc">En tant que maître de jeu</div>
      </div>
    </div>
  </div>
</div>
