{{-- Composant carte de partie --}}
@props([
    'session'
])

<div class="card card-xs md:card-md bg-base-200 rounded-lg shadow-sm overflow-hidden mb-5 h-[280px] w-[185px] min-w-[185px] sm:h-[300px]md:w-[230px] md:h-[350px]">
  <a href="/partie?id={{ $session->getPartie()->getId() }}">
    <figure class="relative h-1/3">
      <img src="{{ $session->getImageURL() }}" class="w-full h-full object-cover" alt="{{ $session->getImageALT() }}" />
    </figure>

    <div class="card-body p-2 pb-3 gap-1 h-7/12 mb:h-8/12">
      <div>
        <h3 class="text">{{ $session->getPartie()->getNom() }}</h3>
        <div class="flex flex-row sm:flex-col justify-around text-xs sm:text-md">
          <h4 class="text">{{ $session->getNomJeu() }}</h4>
          <div class="font-semibold">{{ $session->getMaitreJeu()->displayName() }}</div>
        </div>
      </div>
      <div class="flex gap-4 justify-between">
        <span class="badge badge-xs md:badge-sm cursor-help tooltip tooltip-accent tooltip-right" data-tip="{{ $session->getTypeFormatted() }}">{{ $session->getTypeFormattedShort() }}</span>
        @if ($session->isLocked())
          <span class="badge badge-xs md:badge-sm">
            <span class="material-symbols-outlined" style="font-size: 18px;">lock</span>
          </span>
        @else
          <span class="badge badge-xs md:badge-sm">{{ $session->getNombreJoueursInscrits() }}/{{ $session->getMaxJoueurs() }} 
            <span class="material-symbols-outlined" style="font-size: 18px;">group</span>
          </span>
        @endif
        <span class="badge badge-xs md:badge-sm cursor-help tooltip tooltip-accent tooltip-left" data-tip="{{ $session->getLieuNom() }}">{{ $session->getLieuShort() }}</span>
      </div>
      <p class="text md:text-xs">{{ $session->getPartie()->getDescription() }}</p>
      <div class="divider my-0.5"></div>
      <div class="text-xs text-center h-1/3">
        @foreach($session->getJoueurs() as $joueur)
          <span class="badge badge-soft badge-xs md:badge-sm">
              {{ $joueur->displayName() }}
          </span>
        @endforeach
      </div>
    </div>
  </a>
</div>
