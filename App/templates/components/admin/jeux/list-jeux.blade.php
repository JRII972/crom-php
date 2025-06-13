<div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Liste des jeux</h3>
        <div class="flex gap-2">
            <label class="input input-bordered input-sm flex items-center gap-2">
                <input type="text" id="game_search" placeholder="Rechercher un jeu..." />
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4 opacity-70">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </label>
            <button type="button" class="btn btn-primary btn-sm" onclick="modal_add_game.showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Ajouter un jeu
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <div id="games-grid-table"></div>
    </div>
</div>