/**
 * Scripts pour la page d'administration
 * Utilise les fonctions utilitaires de utils.js et main.js
 */

document.addEventListener('DOMContentLoaded', function () {
    // Chargement des thèmes (fonction de main.js)
    if (localStorage.getItem('theme-mode')) {
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme-mode'))
    }

    // Configuration des onglets principaux
    const mainTabsConfig = {
        'main-tab-users': 'content-users',
        'main-tab-games': 'content-games',
        'main-tab-locations': 'content-locations',
        'main-tab-settings': 'content-settings'
    };

    // Configuration des sous-onglets utilisateurs
    const userTabsConfig = {
        'users-tab-list': 'users-content-list',
        'users-tab-add': 'users-content-add',
        'users-tab-admins': 'users-content-admins'
    };

    // Configuration des sous-onglets jeux
    const gameTabsConfig = {
        'games-tab-list': 'games-content-list',
        'games-tab-genres': 'games-content-genres'
    };

    // Initialisation des onglets avec la fonction utils.js
    initTabs(mainTabsConfig);
    initTabs(userTabsConfig);
    initTabs(gameTabsConfig);

    // Gestion du changement de thème (fonction de main.js)
    document.querySelectorAll('.theme-controller').forEach(input => {
        input.addEventListener('change', () => {
            if (input.checked) {
                document.documentElement.setAttribute('data-theme', input.value);
                localStorage.setItem('theme-mode', input.value);
            }
        });
    });  // Initialisation des fonctionnalités
    initUserManagement();
    initGameManagement();

    initLocationManagement();
    initSettingsManagement();
    initFormValidation();
    initTableActions();
    initSearchFunctionality();
});

/**
 * Gestion des utilisateurs
 */
function initUserManagement() {
    // Formulaire d'ajout d'utilisateur
    const addUserForm = document.querySelector('#users-content-add form');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const email = formData.get('email');

            if (email && !validateEmail(email)) {
                showNotification('Adresse email invalide', 'error');
                return;
            }

            // Simulation d'ajout d'utilisateur
            showNotification('Utilisateur ajouté avec succès!', 'success');
            this.reset();
        });
    }

    // Gestion de la promotion d'utilisateurs
    const promoteButton = document.querySelector('#users-content-admins .btn-primary');
    if (promoteButton) {
        promoteButton.addEventListener('click', function () {
            const userInput = document.querySelector('#users-content-admins input[type="text"]');
            const levelSelect = document.querySelector('#users-content-admins select');

            if (userInput && levelSelect && userInput.value.trim() && levelSelect.value) {
                const userName = userInput.value;
                const level = levelSelect.options[levelSelect.selectedIndex].text;
                showNotification(`${userName} promu au niveau ${level}`, 'success');
                userInput.value = '';
                levelSelect.selectedIndex = 0;
            } else {
                showNotification('Veuillez remplir tous les champs', 'warning');
            }
        });
    }
}

/**
 * Gestion des jeux
 */
function initGameManagement() {
    grid = initGamesGrid()
    // Formulaire d'ajout de jeu
    const addGameModal = document.getElementById('modal-add-game');
    const addGameForm = addGameModal?.querySelector('form');

    if (addGameForm) {
        addGameForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Simulation d'ajout de jeu
            showNotification('Jeu ajouté avec succès!', 'success');
            this.reset();
            addGameModal.close();
        });
    }

    // Gestion des genres
    const addGenreButton = document.querySelector('#games-content-genres .card:last-child .btn-primary');
    if (addGenreButton) {
        addGenreButton.addEventListener('click', function () {
            const genreCard = this.closest('.card');
            const genreInput = genreCard.querySelector('input[type="text"]');
            const colorSelect = genreCard.querySelector('select');

            if (genreInput && colorSelect && genreInput.value.trim() && colorSelect.value) {
                showNotification(`Genre "${genreInput.value}" ajouté avec succès!`, 'success');
                genreInput.value = '';
                colorSelect.selectedIndex = 0;
            } else {
                showNotification('Veuillez remplir tous les champs', 'warning');
            }
        });
    }
}

/**
 * Gestion des lieux
 */
function initLocationManagement() {
    // Formulaire d'ajout de lieu
    const addLocationModal = document.getElementById('modal-add-location');
    const addLocationForm = addLocationModal?.querySelector('form');

    if (addLocationForm) {
        addLocationForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Simulation d'ajout de lieu
            showNotification('Lieu ajouté avec succès!', 'success');
            this.reset();
            addLocationModal.close();
        });
    }
}

/**
 * Gestion des paramètres
 */
function initSettingsManagement() {
    // Sauvegarde des paramètres
    const saveButton = document.querySelector('#content-settings .btn-primary');
    if (saveButton && saveButton.textContent.includes('Sauvegarder')) {
        saveButton.addEventListener('click', function () {
            showNotification('Paramètres sauvegardés avec succès!', 'success');
        });
    }

    // Boutons de maintenance
    const exportButton = document.querySelector('#content-settings .btn-info');
    const cacheButton = document.querySelector('#content-settings .btn-warning');
    const resetButton = document.querySelector('#content-settings .btn-error');

    if (exportButton) {
        exportButton.addEventListener('click', function () {
            showNotification('Export des données en cours...', 'info');
            setTimeout(() => {
                showNotification('Données exportées avec succès!', 'success');
            }, 2000);
        });
    }

    if (cacheButton) {
        cacheButton.addEventListener('click', function () {
            showNotification('Cache vidé avec succès!', 'success');
        });
    }

    if (resetButton) {
        resetButton.addEventListener('click', function () {
            if (confirm('ATTENTION: Cette action va supprimer toutes les données. Êtes-vous absolument sûr ?')) {
                showNotification('Base de données réinitialisée', 'warning');
            }
        });
    }
}

/**
 * Validation des formulaires
 */
function initFormValidation() {
    // Validation des emails en temps réel
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (this.value && !validateEmail(this.value)) {
                this.classList.add('input-error');
                showNotification('Adresse email invalide', 'error');
            } else {
                this.classList.remove('input-error');
            }
        });
    });

    // Validation des mots de passe
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (this.value && this.value.length < 6) {
                this.classList.add('input-error');
                showNotification('Le mot de passe doit contenir au moins 6 caractères', 'warning');
            } else {
                this.classList.remove('input-error');
            }
        });
    });

    // Gestion de l'upload d'images
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    showNotification('Veuillez sélectionner une image valide', 'error');
                    this.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    showNotification('L\'image ne doit pas dépasser 5MB', 'error');
                    this.value = '';
                    return;
                }

                showNotification('Image sélectionnée avec succès', 'success');
            }
        });
    });
}

/**
 * Actions sur les tables
 */
function initTableActions() {
    // Boutons de modification
    document.querySelectorAll('.btn-info').forEach(button => {
        if (button.textContent.includes('Modifier')) {
            button.addEventListener('click', function () {
                showNotification('Fonctionnalité de modification à implémenter', 'info');
            });
        }
    });

    // Boutons de suppression
    document.querySelectorAll('.btn-error').forEach(button => {
        if (button.textContent.includes('Supprimer')) {
            button.addEventListener('click', function () {
                if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                    // Animation de suppression
                    const row = this.closest('tr') || this.closest('.card');
                    if (row) {
                        row.style.opacity = '0.5';
                        setTimeout(() => {
                            row.remove();
                            showNotification('Élément supprimé', 'success');
                        }, 300);
                    }
                }
            });
        }
    });

    // Boutons de suspension
    document.querySelectorAll('.btn-warning').forEach(button => {
        if (button.textContent.includes('Suspendre')) {
            button.addEventListener('click', function () {
                if (confirm('Êtes-vous sûr de vouloir suspendre cet utilisateur ?')) {
                    this.textContent = 'Réactiver';
                    this.classList.remove('btn-warning');
                    this.classList.add('btn-success');
                    showNotification('Utilisateur suspendu', 'warning');
                }
            });
        } else if (button.textContent.includes('Réactiver')) {
            button.addEventListener('click', function () {
                this.textContent = 'Suspendre';
                this.classList.remove('btn-success');
                this.classList.add('btn-warning');
                showNotification('Utilisateur réactivé', 'success');
            });
        }
    });
}

/**
 * Fonctionnalité de recherche
 */
function initSearchFunctionality() {
    const searchInput = document.querySelector('#users-content-list input[type="text"]');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#users-content-list tbody tr');

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

/**
 * Initialisation de Grid.js pour la table des jeux
 */
function initGamesGrid() {
    console.log('Initialisation de Grid.js...');

    // Vérifier que Grid.js est disponible
    if (typeof gridjs === 'undefined') {
        console.error('Grid.js n\'est pas chargé');
        return;
    }

    // Vérifier que l'élément container existe
    const container = document.getElementById('games-grid-table');
    if (!container) {
        console.error('Container games-grid-table non trouvé');
        return;
    }    console.log('Grid.js et container trouvés, création de la grille...');

    // Données d'exemple pour les jeux
    const gameData = [
        {
            "id": 31,
            "nom": "Donjons et Dragons",
            "image": null,
            "icon": {
                "url": "https://img.icons8.com/?size=512&id=104704&format=png",
                "imageAlt": "EGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840",
                "name": "EGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840",
                "format": "jpeg"
            },
            "description": "Le jeu de rôle fantasy le plus populaire au monde",
            "type_jeu": "JDR",
            "genres": [
                {
                    "id": 41,
                    "nom": "Coopératif"
                },
                {
                    "id": 34,
                    "nom": "Heroic-Fantasy"
                },
                {
                    "id": 37,
                    "nom": "Western"
                }
            ]
        },
        {
            "id": 33,
            "nom": "Warhammer Fantasy",
            "image": {
                "url": "/data/images/uploadsEGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840_75.jpeg",
                "imageAlt": "EGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840",
                "name": "EGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840",
                "format": "jpeg"
            },
            "icon": null,
            "description": "Un monde de dark fantasy médiévale",
            "type_jeu": "JDR",
            "genres": [
                {
                    "id": 41,
                    "nom": "Coopératif"
                },
                {
                    "id": 32,
                    "nom": "Steampunk"
                }
            ]
        },
        {
            "id": 34,
            "nom": "Pathfinder",
            "image": null,
            "icon": null,
            "description": "Un jeu de rôle d'heroic fantasy",
            "type_jeu": "JDR",
            "genres": [
                {
                    "id": 38,
                    "nom": "Historique"
                },
                {
                    "id": 30,
                    "nom": "Médiéval"
                },
                {
                    "id": 35,
                    "nom": "Space Opera"
                }
            ]
        },
        {
            "id": 36,
            "nom": "Cyberpunk RED",
            "image": null,
            "icon": null,
            "description": "Un jeu de rôle futuriste dans un monde dystopique",
            "type_jeu": "JDR",
            "genres": [
                {
                    "id": 31,
                    "nom": "Contemporain"
                },
                {
                    "id": 30,
                    "nom": "Médiéval"
                },
                {
                    "id": 35,
                    "nom": "Space Opera"
                }
            ]
        },
        {
            "id": 43,
            "nom": "Le Seigneur des Anneaux JdR",
            "image": null,
            "icon": null,
            "description": "Aventures dans la Terre du Milieu",
            "type_jeu": "JDR",
            "genres": [
                {
                    "id": 43,
                    "nom": "Gestion"
                },
                {
                    "id": 34,
                    "nom": "Heroic-Fantasy"
                }
            ]
        },
        {
            "id": 51,
            "nom": "Dixit",
            "image": null,
            "icon": null,
            "description": "Jeu d'imagination et d'interprétation d'images",
            "type_jeu": "JEU_DE_SOCIETE",
            "genres": [
                {
                    "id": 34,
                    "nom": "Heroic-Fantasy"
                },
                {
                    "id": 27,
                    "nom": "Horreur"
                },
                {
                    "id": 29,
                    "nom": "Post-apocalyptique"
                }
            ]
        },
        {
            "id": 58,
            "nom": "Scythe",
            "image": null,
            "icon": null,
            "description": "Conquête de territoires dans un monde uchronique",
            "type_jeu": "JEU_DE_SOCIETE",
            "genres": [
                {
                    "id": 35,
                    "nom": "Space Opera"
                },
                {
                    "id": 37,
                    "nom": "Western"
                }
            ]
        },
        {
            "id": 60,
            "nom": "Wingspan",
            "image": null,
            "icon": null,
            "description": "Collection d'oiseaux et création d'habitat",
            "type_jeu": "JEU_DE_SOCIETE",
            "genres": [
                {
                    "id": 41,
                    "nom": "Coopératif"
                },
                {
                    "id": 36,
                    "nom": "Dark Fantasy"
                },
                {
                    "id": 34,
                    "nom": "Heroic-Fantasy"
                }
            ]        }
    ];

    // Stocker les données globalement pour les actions
    window.currentGameData = gameData;

    // Configuration de Grid.js
    const grid = new gridjs.Grid({
        columns: [
            {
                name: 'Jeu',
                formatter: (_, row) => {
                    const gameData = row.cells[4].data; // Données du jeu depuis la colonne cachée
                    const imageUrl = gameData.image ? gameData.image.url : 
                                   gameData.icon ? gameData.icon.url : 
                                   'https://picsum.photos/50/50?random=' + gameData.id;
                    
                    return gridjs.html(`
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="mask mask-squircle h-12 w-12">
                                    <img src="${imageUrl}" alt="${gameData.nom}" />
                                </div>
                            </div>
                            <div>
                                <div class="font-bold">${gameData.nom}</div>
                                <div class="text-sm opacity-50">${gameData.type_jeu}</div>
                            </div>
                        </div>
                    `);
                }
            },
            {
                name: 'Genres',
                formatter: (_, row) => {
                    const gameData = row.cells[4].data;
                    const genres = gameData.genres.map(genre => 
                        `<span class="badge badge-outline badge-sm mr-1 whitespace-nowrap">${genre.nom}</span>`
                    ).join('');
                    
                    return gridjs.html(genres);
                }
            },
            {
                name: 'Description',
                formatter: (_, row) => {
                    const gameData = row.cells[4].data;
                    return gridjs.html(`
                        <div class="text-sm p-[0.75rem]">
                            ${gameData.description}
                        </div>
                    `);
                }
            },
            {
                name: 'Actions',
                formatter: (_, row) => {
                    const gameData = row.cells[4].data;
                    return gridjs.html(`
                        <div class="flex gap-1">
                            <button class="btn btn-ghost btn-xs" onclick="editGame(${gameData.id})" title="Modifier">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>
                            <button class="btn btn-ghost btn-xs text-error" onclick="deleteGame(${gameData.id})" title="Supprimer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    `);
                }
            },
            // Colonne cachée pour les données complètes du jeu
            { name: 'gameData', hidden: true }
        ],
        data: gameData.map(game => [
            game.nom,        // Nom du jeu
            '',              // Genres (géré par formatter)
            '',              // Description (géré par formatter) 
            '',              // Actions (géré par formatter)
            game             // Données complètes du jeu (cachées)
        ]),        search: false, // Désactiver la recherche intégrée de Grid.js
        pagination: {
            limit: 10,
            summary: true
        },
        sort: false,
        search: true,
        resizable: true,
        language: {
            'search': {
                'placeholder': 'Rechercher...'
            },
            'pagination': {
                'previous': 'Précédent',
                'next': 'Suivant',
                'navigate': (page, pages) => `Page ${page} sur ${pages}`,
                'page': (page) => `Page ${page}`,
                'showing': 'Affichage de',
                'of': 'sur',
                'to': 'à',
                'results': 'résultats'
            },
            'loading': 'Chargement...',
            'noRecordsFound': 'Aucun résultat trouvé',
            'error': 'Une erreur est survenue lors du chargement des données'
        },
        className: {
            table: 'table table-zebra w-full',
            thead: '',
            tbody: '',
            tr: '',
            th: '',
            td: ''
        }
    });    // Rendu de la grille
    grid.render(document.getElementById('games-grid-table'));

    // Connecter l'input de recherche existant à Grid.js
    const searchInput = document.getElementById('game_search');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            // Debounce pour éviter trop de recherches
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Filtrer les données manuellement
                const filteredData = gameData.filter(game => {
                    const searchInName = game.nom.toLowerCase().includes(searchTerm);
                    const searchInType = game.type_jeu.toLowerCase().includes(searchTerm);
                    const searchInDescription = game.description.toLowerCase().includes(searchTerm);
                    const searchInGenres = game.genres.some(genre => 
                        genre.nom.toLowerCase().includes(searchTerm)
                    );
                    
                    return searchInName || searchInType || searchInDescription || searchInGenres;
                });
                
                // Recréer la grille avec les données filtrées
                grid.updateConfig({
                    data: filteredData.map(game => [
                        game.nom,
                        '',
                        '',
                        '',
                        game
                    ])
                });
                
                // Attendre que la grille soit prête avant de forcer le rendu
                setTimeout(() => {
                    try {
                        grid.forceRender();
                    } catch (error) {
                        console.warn('Erreur lors du rendu forcé:', error);
                    }
                }, 50);
            }, 300);
        });
    }

    return grid;
}

/**
 * Actions pour les jeux
 */
window.editGame = function (gameId) {
    const gameData = window.currentGameData?.find(game => game.id === gameId);
    if (gameData) {
        showNotification(`Modification du jeu: ${gameData.nom}`, 'info');
        // Ici vous pouvez ouvrir un modal de modification avec les données du jeu
    }
};

window.deleteGame = function (gameId) {
    const gameData = window.currentGameData?.find(game => game.id === gameId);
    if (gameData && confirm(`Êtes-vous sûr de vouloir supprimer le jeu "${gameData.nom}" ?`)) {
        showNotification(`Jeu "${gameData.nom}" supprimé`, 'success');
        // Ici vous pouvez supprimer le jeu de la base de données et mettre à jour la grille
    }
};

/**
 * Fonction d'affichage des notifications (reprise de utils.js)
 * @param {string} message - Message à afficher
 * @param {string} type - Type de notification (success, warning, error, info)
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `toast toast-top toast-end`;

    const alertClass = type === 'success' ? 'alert-success' :
        type === 'warning' ? 'alert-warning' :
            type === 'error' ? 'alert-error' : 'alert-info';

    notification.innerHTML = `
    <div class="alert ${alertClass}">
      <span>${message}</span>
    </div>
  `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

/**
 * Validation d'email (reprise de utils.js)
 * @param {string} email - Email à valider
 * @returns {boolean} - True si email valide
 */
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Formatage de date (reprise de utils.js)
 * @param {Date|string} date - Date à formater
 * @returns {string} - Date formatée
 */
function formatDate(date) {
    return new Intl.DateTimeFormat('fr-FR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).format(new Date(date));
}

/**
 * Initialise les onglets (reprise de la fonction utils.js)
 * @param {Object} tabsConfig - Configuration des onglets {tabId: contentId}
 */
function initTabs(tabsConfig) {
    // Parcourt chaque onglet dans la configuration
    for (const [tabId, contentId] of Object.entries(tabsConfig)) {
        const tabElement = document.getElementById(tabId);
        const contentElement = document.getElementById(contentId);
        
        if (tabElement && contentElement) {
            tabElement.addEventListener('change', function() {
                if (this.checked) {
                    // Masquer tous les contenus de ce groupe d'onglets
                    Object.values(tabsConfig).forEach(id => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.style.display = 'none';
                        }
                    });
                    
                    // Afficher le contenu de l'onglet sélectionné
                    contentElement.style.display = 'block';
                }
            });
        }
    }
}
