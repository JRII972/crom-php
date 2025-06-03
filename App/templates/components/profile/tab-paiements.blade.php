{{-- Contenu de l'onglet "Paiements" --}}
<div id="tab-content-paiements" class="py-4 {{ $activeTab != 'paiements' ? 'hidden' : '' }}">
  <h3 class="text-lg font-bold mb-4">Mes paiements</h3>
  
  {{-- Statut d'adhésion --}}
  <div class="alert {{ isset($adhesion->status) && $adhesion->status == 'active' ? 'alert-success' : 'alert-warning' }} mb-6">
    @if(isset($adhesion->status) && $adhesion->status == 'active')
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      <span>Votre adhésion est à jour pour l'année {{ $adhesion->year ?? date('Y') }}.</span>
    @else
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
      <span>Votre adhésion n'est pas à jour. Veuillez renouveler votre adhésion pour accéder à toutes les fonctionnalités.</span>
    @endif
  </div>
  
  {{-- Liste des paiements --}}
  <div class="overflow-x-auto">
    <table class="table table-zebra">
      <thead>
        <tr>
          <th>Date</th>
          <th>Description</th>
          <th>Montant</th>
          <th>Statut</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @if(isset($paiements) && count($paiements) > 0)
          @foreach($paiements as $paiement)
          <tr>
            <td>
              @if(isset($paiement->date))
                @include('components.date-formatter', ['date' => $paiement->date, 'format' => 'j F Y'])
              @else
                -
              @endif
            </td>
            <td>{{ $paiement->description }}</td>
            <td>{{ number_format($paiement->montant, 2, ',', ' ') }} €</td>
            <td>
              <div class="badge badge-{{ $paiement->statut == 'Payé' ? 'success' : 'warning' }}">{{ $paiement->statut }}</div>
            </td>
            <td>
              @if($paiement->statut == 'Payé')
                <a href="{{ $route('receipts.show', [$paiement->id]) ?? '#' }}" class="btn btn-xs btn-outline">Reçu</a>
              @else
                <a href="{{ $route('payments.pay', [$paiement->id]) ?? '#' }}" class="btn btn-xs btn-outline btn-primary">Payer</a>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          {{-- Exemples statiques pour démonstration --}}
          <tr>
            <td>15 janvier 2025</td>
            <td>Adhésion Annuelle 2025</td>
            <td>25,00 €</td>
            <td>
              <div class="badge badge-success">Payé</div>
            </td>
            <td>
              <button class="btn btn-xs btn-outline">Reçu</button>
            </td>
          </tr>
          <tr>
            <td>10 janvier 2024</td>
            <td>Adhésion Annuelle 2024</td>
            <td>25,00 €</td>
            <td>
              <div class="badge badge-success">Payé</div>
            </td>
            <td>
              <button class="btn btn-xs btn-outline">Reçu</button>
            </td>
          </tr>
          <tr>
            <td>5 janvier 2023</td>
            <td>Adhésion Annuelle 2023</td>
            <td>20,00 €</td>
            <td>
              <div class="badge badge-success">Payé</div>
            </td>
            <td>
              <button class="btn btn-xs btn-outline">Reçu</button>
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
  
  {{-- Bouton pour renouveler l'adhésion --}}
  <div class="mt-6 flex justify-end">
    <a href="{{ $route('payments.renew') ?? '#' }}" class="btn btn-success">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
      </svg>
      Renouveler mon adhésion
    </a>
  </div>
</div>
