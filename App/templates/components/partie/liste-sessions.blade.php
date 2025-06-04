{{-- Liste des sessions d'une partie --}}

<div id="tab-content-sessions" class="py-4 {{ $activeTab === 'sessions' ? '' : 'hidden' }}">
  <h3 class="text-lg font-bold mb-4">Prochaines sessions</h3>
  
  {{-- Liste des sessions --}}
  <div class="space-y-6">
    @if(isset($sessions) && count($sessions) > 0)
      @foreach($sessions as $session)
        @include('components.partie.session-item', ['session' => $session])
      @endforeach
    @else
      {{-- Sessions statiques si aucune session n'est fournie --}}
      @php
        $sessionExemples = [
          [
            'numero' => '1',
            'titre' => 'L\'appel du roi',
            'date' => '10 Juin 2025',
            'horaire' => '19h00 - 23h00',
            'lieu' => 'Salle Paris',
            'places_prises' => 3,
            'places_max' => 5,
            'joueurs' => [
              ['nom' => 'Marie Dupont', 'pseudo' => '@Elendil', 'avatar' => 'https://picsum.photos/seed/player1/200'],
              ['nom' => 'Jean Lefebvre', 'pseudo' => '@Gimli', 'avatar' => 'https://picsum.photos/seed/player2/200'],
              ['nom' => 'Lucie Martin', 'pseudo' => '@Galadriel', 'avatar' => 'https://picsum.photos/seed/player3/200']
            ]
          ],
          [
            'numero' => '2',
            'titre' => 'La forêt des murmures',
            'date' => '24 Juin 2025',
            'horaire' => '19h00 - 23h00',
            'lieu' => 'Salle Paris',
            'places_prises' => 2,
            'places_max' => 5,
            'joueurs' => [
              ['nom' => 'Marie Dupont', 'pseudo' => '@Elendil', 'avatar' => 'https://picsum.photos/seed/player1/200'],
              ['nom' => 'Jean Lefebvre', 'pseudo' => '@Gimli', 'avatar' => 'https://picsum.photos/seed/player2/200']
            ]
          ],
          [
            'numero' => '3',
            'titre' => 'Les mines oubliées',
            'date' => '8 Juillet 2025',
            'horaire' => '19h00 - 23h00',
            'lieu' => 'Salle Paris',
            'places_prises' => 2,
            'places_max' => 5,
            'joueurs' => [
              ['nom' => 'Marie Dupont', 'pseudo' => '@Elendil', 'avatar' => 'https://picsum.photos/seed/player1/200'],
              ['nom' => 'Lucie Martin', 'pseudo' => '@Galadriel', 'avatar' => 'https://picsum.photos/seed/player3/200']
            ]
          ],
          [
            'numero' => '4',
            'titre' => 'Le temple des anciens',
            'date' => '22 Juillet 2025',
            'horaire' => '19h00 - 23h00',
            'lieu' => 'Salle Paris',
            'places_prises' => 1,
            'places_max' => 5,
            'joueurs' => [
              ['nom' => 'Marie Dupont', 'pseudo' => '@Elendil', 'avatar' => 'https://picsum.photos/seed/player1/200']
            ]
          ],
          [
            'numero' => '5',
            'titre' => 'La citadelle des ombres',
            'date' => '5 Août 2025',
            'horaire' => '19h00 - 23h00',
            'lieu' => 'Salle Paris',
            'places_prises' => 0,
            'places_max' => 5,
            'joueurs' => []
          ]
        ];
      @endphp
      
      @foreach($sessionExemples as $session)
        @include('components.partie.session-item', ['session' => $session])
      @endforeach
    @endif
  </div>
</div>
