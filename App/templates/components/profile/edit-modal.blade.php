{{-- Modal d'édition de profil --}}
<dialog id="edit-profile-modal" class="modal">
  <div class="modal-box w-11/12 max-w-3xl">
    <h3 class="font-bold text-lg">Modifier mon profil</h3>
    <form id="profile-edit-form" class="py-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
          <label class="label">
            <span class="label-text">Prénom</span>
          </label>
          <input type="text" id="edit-firstname" class="input input-bordered" value="{{ $user->firstname ?? 'Thomas' }}" />
        </div>
        <div class="form-control">
          <label class="label">
            <span class="label-text">Nom</span>
          </label>
          <input type="text" id="edit-lastname" class="input input-bordered" value="{{ $user->lastname ?? 'Dubois' }}" />
        </div>
        <div class="form-control">
          <label class="label">
            <span class="label-text">Pseudonyme</span>
          </label>
          <input type="text" id="edit-pseudo" class="input input-bordered" value="{{ $user->pseudo ?? 'TomD' }}" />
        </div>
        <div class="form-control">
          <label class="label">
            <span class="label-text">Nom d'utilisateur</span>
          </label>
          <input type="text" id="edit-username" class="input input-bordered" value="{{ $user->username ?? 'tomdu92' }}" />
        </div>
        <div class="form-control">
          <label class="label">
            <span class="label-text">Email</span>
          </label>
          <input type="email" id="edit-email" class="input input-bordered" value="{{ $user->email ?? 'thomas@email.com' }}" />
        </div>
        <div class="form-control">
          <label class="label">
            <span class="label-text">ID Discord</span>
          </label>
          <input type="text" id="edit-discord" class="input input-bordered" value="{{ $user->discord ?? 'TomD#1234' }}" />
        </div>
        <div class="form-control">
          <label class="label">
            <span class="label-text">Date de naissance</span>
          </label>
          <input type="date" id="edit-birthdate" class="input input-bordered" value="{{ $user->birthdate ?? '1990-04-15' }}" />
        </div>
        <div class="form-control">
          <label class="label">
            <span class="label-text">Sexe</span>
          </label>
          <select id="edit-gender" class="select select-bordered">
            <option value="M" {{ isset($user->gender) && $user->gender == 'M' ? 'selected' : '' }}>Masculin</option>
            <option value="F" {{ isset($user->gender) && $user->gender == 'F' ? 'selected' : '' }}>Féminin</option>
            <option value="Autre" {{ isset($user->gender) && $user->gender == 'Autre' ? 'selected' : '' }}>Autre</option>
          </select>
        </div>
      </div>
      
      <div class="divider my-6">Changement de mot de passe</div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
          <label class="label">
            <span class="label-text">Mot de passe actuel</span>
          </label>
          <input type="password" id="edit-current-password" class="input input-bordered" />
        </div>
        <div class="form-control md:col-span-2">
          <label class="label">
            <span class="label-text">Nouveau mot de passe</span>
          </label>
          <input type="password" id="edit-new-password" class="input input-bordered" />
        </div>
        <div class="form-control md:col-span-2">
          <label class="label">
            <span class="label-text">Confirmer le nouveau mot de passe</span>
          </label>
          <input type="password" id="edit-confirm-password" class="input input-bordered" />
        </div>
      </div>
    </form>
    <div class="modal-action">
      <button class="btn btn-error" id="cancel-edit-btn">Annuler</button>
      <button class="btn btn-primary" id="save-profile-btn">Enregistrer les modifications</button>
    </div>
  </div>
  <form method="dialog" class="modal-backdrop">
    <button>Fermer</button>
  </form>
</dialog>
