/**
 * Gestionnaire pour la création et édition de parties
 */
class PartieFormManager {
    constructor() {
        this.isEditing = false;
        this.partieId = null;
        this.quillEditor = null;
        this.selectizeJeu = null;
        this.gameImageCache = new Map();
        
        this.init();
    }

    /**
     * Initialise la page
     */
    init() {
        this.checkEditMode();
        this.initializeComponents();
        this.setupEventListeners();
        this.loadGameData();
        
        if (this.isEditing) {
            this.loadPartieData();
        }
    }

    /**
     * Vérifie si nous sommes en mode édition
     */
    checkEditMode() {
        const urlParams = new URLSearchParams(window.location.search);
        this.partieId = urlParams.get('id');
        this.isEditing = !!this.partieId;
        
        if (this.isEditing) {
            document.getElementById('page-title').textContent = 'Modifier la partie';
            document.getElementById('submit-text').textContent = 'Mettre à jour';
        }
    }

    /**
     * Initialise les composants (Quill Editor, Selectize)
     */
    initializeComponents() {
        // Initialiser Quill Editor
        this.quillEditor = new Quill('#description-editor', {
            theme: 'snow',
            placeholder: 'Décrivez votre partie en détail...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['link', 'blockquote', 'code-block'],
                    ['clean']
                ]
            }
        });

        // Synchroniser avec le champ caché
        this.quillEditor.on('text-change', () => {
            document.getElementById('description').value = this.quillEditor.root.innerHTML;
        });

        // Initialiser Selectize pour les jeux
        $('#jeu').selectize({
            valueField: 'id',
            labelField: 'nom',
            searchField: ['nom', 'description'],
            placeholder: 'Tapez pour rechercher un jeu...',
            loadThrottle: 300,
            create: function(input) {
                return {
                    id: 'new_' + Date.now(),
                    nom: input,
                    description: '',
                    image: null,
                    isNew: true
                };
            },
            createFilter: function(input) {
                return input.length >= 2;
            },
            render: {
                option: (item, escape) => {
                    return `<div class="option">
                        <div class="font-semibold">${escape(item.nom)}</div>
                        ${item.description ? `<div class="text-sm text-base-content/70">${escape(item.description.substring(0, 100))}...</div>` : ''}
                        ${item.isNew ? '<div class="text-xs text-primary">Nouveau jeu</div>' : ''}
                    </div>`;
                },
                item: (item, escape) => {
                    return `<div>${escape(item.nom)}</div>`;
                }
            },
            onChange: (value) => {
                this.handleGameSelection(value);
            }
        });

        this.selectizeJeu = $('#jeu')[0].selectize;
    }

    /**
     * Configure les écouteurs d'événements
     */
    setupEventListeners() {
        // Gestion du compteur de caractères pour description courte
        const descCourte = document.getElementById('description_courte');
        const descCount = document.getElementById('desc-courte-count');
        
        descCourte.addEventListener('input', () => {
            const count = descCourte.value.length;
            descCount.textContent = `${count}/140`;
            descCount.classList.toggle('text-warning', count > 120);
            descCount.classList.toggle('text-error', count >= 140);
        });

        // Affichage conditionnel du type de campagne
        document.getElementById('type_partie').addEventListener('change', (e) => {
            const container = document.getElementById('type-campagne-container');
            container.style.display = e.target.value === 'CAMPAGNE' ? 'block' : 'none';
            
            if (e.target.value !== 'CAMPAGNE') {
                document.getElementById('type_campagne').value = '';
            }
        });

        // Validation des nombres de joueurs
        const maxJoueurs = document.getElementById('nombre_max_joueurs');
        const maxSession = document.getElementById('max_joueurs_session');
        
        const validatePlayerNumbers = () => {
            const maxJoueursVal = parseInt(maxJoueurs.value) || 0;
            const maxSessionVal = parseInt(maxSession.value) || 0;
            
            const errorElement = document.getElementById('max-session-error');
            
            if (maxJoueursVal > 0 && maxSessionVal > maxJoueursVal) {
                errorElement.textContent = 'Ne peut pas dépasser le nombre maximum total';
                errorElement.style.display = 'block';
                maxSession.classList.add('input-error');
                return false;
            } else {
                errorElement.style.display = 'none';
                maxSession.classList.remove('input-error');
                return true;
            }
        };

        maxJoueurs.addEventListener('input', validatePlayerNumbers);
        maxSession.addEventListener('input', validatePlayerNumbers);

        // Gestion des onglets d'image
        const imageSourceRadios = document.querySelectorAll('input[name="image_source"]');
        imageSourceRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                this.handleImageSourceChange();
            });
        });

        // Gestion des changements d'image
        document.getElementById('image_url').addEventListener('input', () => {
            this.updateImagePreview();
        });

        document.getElementById('image_file').addEventListener('change', () => {
            this.updateImagePreview();
        });

        // Boutons d'action
        document.getElementById('save-btn').addEventListener('click', () => {
            this.savePartie();
        });

        document.getElementById('preview-btn').addEventListener('click', () => {
            this.showPreview();
        });

        document.getElementById('save-draft-btn').addEventListener('click', () => {
            this.saveDraft();
        });

        // Soumission du formulaire
        document.getElementById('partie-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.savePartie();
        });
    }

    /**
     * Charge les données des jeux depuis l'API
     */
    async loadGameData() {
        try {
            // Simuler un appel API - remplacer par votre endpoint
            const response = await fetch('/api/jeux');
            const jeux = await response.json();
            
            // Ajouter les jeux à Selectize
            this.selectizeJeu.clearOptions();
            jeux.forEach(jeu => {
                this.selectizeJeu.addOption(jeu);
                if (jeu.image) {
                    this.gameImageCache.set(jeu.id.toString(), jeu.image);
                }
            });
            
        } catch (error) {
            console.error('Erreur lors du chargement des jeux:', error);
            
            // Données de démonstration
            const demoGames = [
                { id: 1, nom: 'Dungeons & Dragons 5e', description: 'Le célèbre jeu de rôle fantasy', image: 'public/data/images/donjon&dragon.jpg' },
                { id: 2, nom: 'Pathfinder', description: 'Jeu de rôle fantasy avancé', image: 'public/data/images/Pathfinder.webp' },
                { id: 3, nom: 'Cyberpunk RED', description: 'Jeu de rôle cyberpunk futuriste', image: 'public/data/images/Cyberpunk RED.png' },
                { id: 4, nom: 'Chroniques Oubliées', description: 'JDR français médiéval-fantastique', image: 'public/data/images/Chroniques_Oubliees.jpg' }
            ];
            
            demoGames.forEach(jeu => {
                this.selectizeJeu.addOption(jeu);
                this.gameImageCache.set(jeu.id.toString(), jeu.image);
            });
        }
    }

    /**
     * Charge les données de la partie en mode édition
     */
    async loadPartieData() {
        try {
            const response = await fetch(`/api/parties/${this.partieId}`);
            const partie = await response.json();
            
            this.populateForm(partie);
            
        } catch (error) {
            console.error('Erreur lors du chargement de la partie:', error);
            // Afficher un message d'erreur et rediriger
            alert('Erreur lors du chargement de la partie');
            window.location.href = 'parties.html';
        }
    }

    /**
     * Remplit le formulaire avec les données de la partie
     */
    populateForm(partie) {
        document.getElementById('nom').value = partie.nom || '';
        document.getElementById('type_partie').value = partie.type_partie || '';
        document.getElementById('type_campagne').value = partie.type_campagne || '';
        document.getElementById('description_courte').value = partie.description_courte || '';
        document.getElementById('nombre_max_joueurs').value = partie.nombre_max_joueurs || 0;
        document.getElementById('max_joueurs_session').value = partie.max_joueurs_session || 5;
        document.getElementById('verrouille').checked = partie.verrouille || false;
        document.getElementById('texte_alt_image').value = partie.texte_alt_image || '';

        // Sélectionner le jeu
        if (partie.id_jeu) {
            this.selectizeJeu.setValue(partie.id_jeu);
        }

        // Gérer l'image
        if (partie.image) {
            document.getElementById('image_url').value = partie.image;
            document.querySelector('input[value="url"]').checked = true;
            this.handleImageSourceChange();
        }

        // Description longue
        if (partie.description) {
            this.quillEditor.root.innerHTML = partie.description;
            document.getElementById('description').value = partie.description;
        }

        // Déclencher les événements pour mettre à jour l'UI
        document.getElementById('type_partie').dispatchEvent(new Event('change'));
        document.getElementById('description_courte').dispatchEvent(new Event('input'));
        this.updateImagePreview();
    }

    /**
     * Gère la sélection d'un jeu
     */
    handleGameSelection(gameId) {
        if (!gameId) return;

        // Si l'image source est "game", mettre à jour l'aperçu
        const gameSource = document.querySelector('input[value="game"]');
        if (gameSource.checked) {
            this.updateImagePreview();
        }
    }

    /**
     * Gère le changement de source d'image
     */
    handleImageSourceChange() {
        const selectedSource = document.querySelector('input[name="image_source"]:checked').value;
        
        // Cacher tous les conteneurs
        document.getElementById('url-input-container').style.display = 'none';
        document.getElementById('upload-input-container').style.display = 'none';
        
        // Afficher le conteneur approprié
        if (selectedSource === 'url') {
            document.getElementById('url-input-container').style.display = 'block';
        } else if (selectedSource === 'upload') {
            document.getElementById('upload-input-container').style.display = 'block';
        }

        this.updateImagePreview();
    }

    /**
     * Met à jour l'aperçu de l'image
     */
    updateImagePreview() {
        const previewContainer = document.getElementById('image-preview');
        const selectedSource = document.querySelector('input[name="image_source"]:checked').value;
        
        let imageUrl = null;

        if (selectedSource === 'game') {
            const selectedGameId = this.selectizeJeu?.getValue();
            if (selectedGameId && this.gameImageCache.has(selectedGameId)) {
                imageUrl = this.gameImageCache.get(selectedGameId);
            }
        } else if (selectedSource === 'url') {
            imageUrl = document.getElementById('image_url').value;
        } else if (selectedSource === 'upload') {
            const fileInput = document.getElementById('image_file');
            if (fileInput.files && fileInput.files[0]) {
                imageUrl = URL.createObjectURL(fileInput.files[0]);
            }
        }

        if (imageUrl) {
            previewContainer.innerHTML = `
                <img src="${imageUrl}" alt="Aperçu" class="max-w-full max-h-64 rounded-lg object-cover">
            `;
        } else {
            previewContainer.innerHTML = `
                <div class="text-base-content/60 text-center">
                    <i class="fas fa-image text-4xl"></i>
                    <p class="mt-2">Aucune image sélectionnée</p>
                </div>
            `;
        }
    }

    /**
     * Valide le formulaire
     */
    validateForm() {
        const errors = [];

        // Nom requis
        const nom = document.getElementById('nom').value.trim();
        if (!nom) {
            errors.push('Le nom de la partie est requis');
        }

        // Jeu requis
        const jeu = this.selectizeJeu?.getValue();
        if (!jeu) {
            errors.push('Le jeu est requis');
        }

        // Type de partie requis
        const typePartie = document.getElementById('type_partie').value;
        if (!typePartie) {
            errors.push('Le type de partie est requis');
        }

        // Validation des nombres de joueurs
        const maxJoueurs = parseInt(document.getElementById('nombre_max_joueurs').value) || 0;
        const maxSession = parseInt(document.getElementById('max_joueurs_session').value) || 0;
        
        if (maxSession <= 0) {
            errors.push('Le nombre maximum de joueurs par session doit être supérieur à 0');
        }
        
        if (maxJoueurs > 0 && maxSession > maxJoueurs) {
            errors.push('Le nombre de joueurs par session ne peut pas dépasser le nombre total maximum');
        }

        return errors;
    }

    /**
     * Collecte les données du formulaire
     */
    collectFormData() {
        const formData = new FormData();
        
        formData.append('nom', document.getElementById('nom').value.trim());
        formData.append('id_jeu', this.selectizeJeu?.getValue() || '');
        formData.append('type_partie', document.getElementById('type_partie').value);
        formData.append('type_campagne', document.getElementById('type_campagne').value || null);
        formData.append('description_courte', document.getElementById('description_courte').value.trim());
        formData.append('description', this.quillEditor.root.innerHTML);
        formData.append('nombre_max_joueurs', document.getElementById('nombre_max_joueurs').value || 0);
        formData.append('max_joueurs_session', document.getElementById('max_joueurs_session').value);
        formData.append('verrouille', document.getElementById('verrouille').checked);
        formData.append('texte_alt_image', document.getElementById('texte_alt_image').value.trim());

        // Gestion de l'image
        const imageSource = document.querySelector('input[name="image_source"]:checked').value;
        
        if (imageSource === 'url') {
            formData.append('image', document.getElementById('image_url').value);
        } else if (imageSource === 'upload') {
            const fileInput = document.getElementById('image_file');
            if (fileInput.files && fileInput.files[0]) {
                formData.append('image_file', fileInput.files[0]);
            }
        } else if (imageSource === 'game') {
            const selectedGameId = this.selectizeJeu?.getValue();
            if (selectedGameId && this.gameImageCache.has(selectedGameId)) {
                formData.append('image', this.gameImageCache.get(selectedGameId));
            }
        }

        return formData;
    }

    /**
     * Sauvegarde la partie
     */
    async savePartie() {
        const errors = this.validateForm();
        
        if (errors.length > 0) {
            alert('Erreurs de validation:\n' + errors.join('\n'));
            return;
        }

        const formData = this.collectFormData();
        const saveBtn = document.getElementById('save-btn');
        
        try {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';

            const url = this.isEditing ? `/api/parties/${this.partieId}` : '/api/parties';
            const method = this.isEditing ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                body: formData
            });

            if (response.ok) {
                const result = await response.json();
                
                // Afficher un message de succès
                const message = this.isEditing ? 'Partie mise à jour avec succès!' : 'Partie créée avec succès!';
                alert(message);
                
                // Rediriger vers la liste des parties ou la page de détail
                window.location.href = `partie-detail.html?id=${result.id}`;
                
            } else {
                throw new Error('Erreur lors de la sauvegarde');
            }
            
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la sauvegarde de la partie');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
        }
    }

    /**
     * Sauvegarde comme brouillon
     */
    async saveDraft() {
        const formData = this.collectFormData();
        formData.append('is_draft', 'true');
        
        try {
            const response = await fetch('/api/parties/draft', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                alert('Brouillon sauvegardé!');
            } else {
                throw new Error('Erreur lors de la sauvegarde du brouillon');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la sauvegarde du brouillon');
        }
    }

    /**
     * Affiche l'aperçu de la partie
     */
    showPreview() {
        const formData = this.collectFormData();
        const modal = document.getElementById('preview-modal');
        const content = document.getElementById('preview-content');
        
        // Générer l'aperçu HTML
        const previewHtml = this.generatePreviewHtml(formData);
        content.innerHTML = previewHtml;
        
        modal.showModal();
    }

    /**
     * Génère le HTML pour l'aperçu
     */
    generatePreviewHtml(formData) {
        const selectedGame = this.selectizeJeu?.getOption(this.selectizeJeu.getValue());
        const imageSource = document.querySelector('input[name="image_source"]:checked').value;
        
        let imageUrl = null;
        if (imageSource === 'game' && selectedGame) {
            imageUrl = this.gameImageCache.get(this.selectizeJeu.getValue());
        } else if (imageSource === 'url') {
            imageUrl = formData.get('image');
        }

        return `
            <div class="card bg-base-100 shadow-lg">
                ${imageUrl ? `
                    <figure class="h-48">
                        <img src="${imageUrl}" alt="${formData.get('texte_alt_image') || 'Image de la partie'}" 
                             class="w-full h-full object-cover">
                    </figure>
                ` : ''}
                <div class="card-body">
                    <h2 class="card-title">${formData.get('nom')}</h2>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <div class="badge badge-primary">${selectedGame?.nom || 'Jeu non sélectionné'}</div>
                        <div class="badge badge-secondary">${formData.get('type_partie')}</div>
                        ${formData.get('type_campagne') ? `<div class="badge badge-accent">${formData.get('type_campagne')}</div>` : ''}
                        ${formData.get('verrouille') === 'true' ? '<div class="badge badge-warning">Verrouillée</div>' : ''}
                    </div>
                      ${formData.get('description_courte') ? `
                        <p class="text-base-content/80 mb-4">${formData.get('description_courte')}</p>
                    ` : ''}
                    
                    <div class="grid grid-cols-2 gap-4 text-sm text-base-content/70">
                        <div>
                            <strong>Max joueurs total:</strong> ${formData.get('nombre_max_joueurs') || 'Illimité'}
                        </div>
                        <div>
                            <strong>Max par session:</strong> ${formData.get('max_joueurs_session')}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// Initialiser la page quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new PartieFormManager();
});
