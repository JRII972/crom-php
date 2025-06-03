{{-- Composant carte de partie --}}
@props([
    'titre' => 'Titre de la partie',
    'jeu' => 'Jeu',
    'mj' => 'MJ',
    'type' => '1Sht',
    'type_tooltip' => 'OneShot',
    'joueurs_count' => '5',
    'joueurs_max' => '6',
    'lieu' => 'FSV',
    'lieu_tooltip' => 'Foyer Saint Vincent',
    'description' => 'Description de la partie',
    'image' => 'https://jrii972.github.io/data/images/donjon&dragon.jpg',
    'joueurs' => ['JOUEUR1', 'JOUEUR2', 'JOUEUR3', 'JOUEUR4', 'JOUEUR5']
])

<div class="card card-xs md:card-md bg-base-200 rounded-lg shadow-sm overflow-hidden mb-5 h-[250px] sm:w-[170px] sm:h-[300px] md:w-[230px] md:h-[350px]">
  <figure class="relative">
    <img src="{{ $image }}" class="w-full h-full object-cover" alt="{{ $titre }}" />
  </figure>

  <div class="card-body p-2 pb-3 gap-1 h-7/12 mb:h-8/12">
    <div>
      <h3 class="text">{{ $titre }}</h3>
      <div class="flex flex-row sm:flex-col justify-around text-xs sm:text-md">
        <h4 class="text">{{ $jeu }}</h4>
        <div class="font-semibold">{{ $mj }}</div>
      </div>
    </div>
    <div class="flex gap-4 justify-between">
      <span class="badge badge-xs md:badge-sm cursor-help tooltip tooltip-accent tooltip-right" data-tip="{{ $type_tooltip }}">{{ $type }}</span>
      <span class="badge sm:hidden md:flex badge-xs md:badge-sm">{{ $joueurs_count }}/{{ $joueurs_max }} joueurs</span>
      <span class="badge badge-xs hidden sm:flex md:hidden">{{ $joueurs_count }}/{{ $joueurs_max }} jrs</span>
      <span class="badge badge-xs md:badge-sm cursor-help tooltip tooltip-accent tooltip-left" data-tip="{{ $lieu_tooltip }}">{{ $lieu }}</span>
    </div>
    <p class="text text-xs">{{ $description }}</p>
    <div class="divider my-0.5"></div>
    <div class="text-xs text-center">
      @foreach($joueurs as $joueur)
        <span class="badge badge-soft badge-xs md:badge-sm">
            {{ $joueur }}
        </span>
      @endforeach
    </div>
  </div>
</div>
