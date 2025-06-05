{{-- Composant carte de activite --}}
@props([
    'session'
])
@if( isSessionDisplay($session))
<a href="{{ route('activite.show', [$session->getActivite()->getId()]) }}">
  <div class="card card-xs md:card-md bg-base-200 rounded-lg shadow-sm overflow-hidden mb-5 h-[280px] w-[185px] min-w-[185px] sm:h-[300px] md:w-[230px] md:h-[350px]">
    <div class="card card-sm rounded-none image-full shadow-sm h-[38%] md:h-[45%]">
      <figure>
        <img src="{{ $session->getImageURL() }}" class="w-full h-full object-cover" alt="{{ $session->getNom() }}" />
      </figure>
      <div class="card-body items-center text-center gap-0.5 justify-end">
        <span class="cursor-help tooltip tooltip-accent tooltip-bottom" data-tip="{{ $session->getTypeFormatted() }}">{{ $session->getTypeFormattedShort() }}</span>
        <h2 class="card-title">{{ $session->getNom() }}</h2>
      </div>
    </div>
    
    <div class="card-body p-2 pb-3 gap-0.5 justify-between">
      
      <div class="flex gap-0.5 flex-col items-center">
        <span class="">{{ $session->getNomJeu() }}</span>
        <span class="font-semibold">{{ $session->getMaitreJeu()->displayName() }}</span>
        <div class="flex">
          <div class="flex items-end flex-col">                            
            <span class="cursor-help tooltip tooltip-accent tooltip-bottom" data-tip="{{ $session->getLieuNom() }}">{{ $session->getLieuNom() }}</span>
            </div>
            <div class="divider divider-horizontal mx-0"></div>
            <div class="flex items-start flex-col">                            
              @if ($session->isLocked())
              <div style="display: flex; align-items: anchor-center; gap: 0.5em;">
                <!-- <span>Complet</span> -->
                <span class="material-symbols-outlined" style="font-size: 18px;">lock</span>
              </div>
              @else
              <div style="display: flex; align-items: anchor-center; gap: 0.5em;">
                <span class="">{{ $session->getNombreJoueursInscrits() }}/{{ $session->getMaxJoueurs() }}</span>
                <span class="material-symbols-outlined" style="font-size: 18px;">group</span>
              </div>
              @endif
            </div>
          </div>
        </div>

      <div class="pt-0.5 ">
        <p class="md:text-xs line-clamp-3 md:line-clamp-4">{{ $session->getActivite()->getDescription() }}</p>
      </div>
      <div class="text-xs text-center h-fit">
        @foreach($session->getJoueurs() as $joueur)
          <span class="badge badge-soft badge-xs md:badge-sm">
              {{ $joueur->displayName() }}
          </span>
        @endforeach
      </div>
    </div>
  </div>
</a>
@else
<a href="{{ route('activite.show', [$session->getId()]) }}">
  <div class="card card-xs md:card-md bg-base-200 rounded-lg shadow-sm overflow-hidden mb-5 h-[280px] w-[185px] min-w-[185px] sm:h-[300px] md:w-[230px] md:h-[350px]">
    <div class="card card-sm rounded-none image-full shadow-sm h-[38%] md:h-[45%]">
      <figure>
        <img src="{{ $session->getImageURL() }}" class="w-full h-full object-cover" alt="{{ $session->getNom() }}" />
      </figure>
      <div class="card-body items-center text-center gap-0.5 justify-end">
        <span class="cursor-help tooltip tooltip-accent tooltip-bottom" data-tip="{{ $session->getTypeFormatted() }}">{{ $session->getTypeFormattedShort() }}</span>
        <h2 class="card-title">{{ $session->getNom() }}</h2>
      </div>
    </div>
    
    <div class="card-body p-2 pb-3 gap-0.5 justify-between">
      
      <div class="flex gap-0.5 flex-col items-center">
        <span class="">{{ $session->getJeu()->getNom() }}</span>
        <span class="font-semibold">{{ $session->getMaitreJeu()->displayName() }}</span>
        <div class="flex">
          <div class="flex items-end flex-col">                            
            <!-- <span class="cursor-help tooltip tooltip-accent tooltip-bottom" data-tip=" "> </span> -->
            </div>
            <div class="divider divider-horizontal mx-0"></div>
            <div class="flex items-start flex-col">                            
              @if ($session->isLocked())
              <div style="display: flex; align-items: anchor-center; gap: 0.5em;">
                <span class="material-symbols-outlined" style="font-size: 18px;">lock</span>
              </div>
              @else
              <div style="display: flex; align-items: anchor-center; gap: 0.5em;">
                <span class="">{{ $session->getNombreJoueursInscrits() }}/{{ $session->getMaxJoueurs() }}</span>
                <span class="material-symbols-outlined" style="font-size: 18px;">group</span>
              </div>
              @endif
            </div>
          </div>
        </div>

      <div class="pt-0.5 ">
        <p class="md:text-xs line-clamp-3 md:line-clamp-4">{{ $session->getDescription() }}</p>
      </div>
      <div class="text-xs text-center h-fit">
       {{--  @forelse($session->getJoueursInscrits() as $joueur)
          <span class="badge badge-soft badge-xs md:badge-sm">
              {{ $joueur->displayName() }}
          </span>
        @empty
          <div></div>
        @endforelse --}}
      </div>
    </div>
  </div>
</a>
@endif
