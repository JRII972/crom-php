<!doctype html>
<html lang="en" data-theme="caramellatte">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mon App</title>
    <link rel="stylesheet" crossorigin href="/assets/css/index.css">
  </head>
  <body>

    <!-- Modifier la structure du drawer pour permettre un défilement indépendant -->
    <div class="drawer lg:drawer-open h-screen">
      <input id="my-drawer" type="checkbox" class="drawer-toggle" />

      <!-- Drawer sidebar - ajouter fixed pour qu'il reste en place -->
      <div class="drawer-side min-h-full fixed top-0 bottom-0 z-10"> 
        <label for="my-drawer" class="drawer-overlay"></label>
        <div class="flex flex-col h-full bg-base-200 w-3xs">
          
          <!-- Logo et nom de l'association en haut -->
          <div class="flex flex-col items-center py-6 px-4 bg-base-200 border-b border-base-300">
            <img src="/data/images/logo-bdr.png" alt="Logo LBDR" class="w-16 h-16 mb-2" />
            <div class="font-bold text-xl text-center font-ravenholm-bold">LBDR</div>
          </div>
          
          <!-- Menu navigation principal -->
          <ul class="menu p-4 w-full text-base-content space-y-2">
            <li class="menu-title"><span>Menu</span></li>
            <li><a>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>
              Acceuil
            </a></li>
            <li><a>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l7.5 5.25v9.5L12 22l-7.5-5.25v-9.5L12 2z M12 2v7M19.5 7.25l-7.5 3M12 9l-7.5-2M4.5 16.75l7.5 1.25M12 18l7.5-1.5M12 22v-4" />
              </svg>
              Mes Parties
            </a></li>
            <li><a>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" />
              </svg>
              Toutes les parties
            </a></li>
            <li><a>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
              </svg>
              A propos
            </a></li>
            <li><a>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
              </svg>
              Contact
            </a></li>
          </ul>
          
          <!-- Espace flexible pour pousser le menu utilisateur vers le bas -->
          <div class="flex-grow"></div>

          <!-- Menu utilisateur en bas -->
          <ul class="menu p-4 w-full text-base-content space-y-2 border-t border-base-300">
            <li class="menu-title"><span>Utilisateur</span></li>
            <li><a>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
              </svg>
              Profil
            </a></li>
            <li><a>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Paramètres
            </a></li>
            <li><a class="text-error">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
              </svg>
              Se déconnecter
            </a></li>
          </ul>
          
          <!-- Profil utilisateur déplacé en bas -->
          <div class="flex items-center p-4 justify-between bg-base-200 border-t border-base-300">
            <div class="flex items-center gap-3">
              <div class="avatar">
                <div class="w-10 rounded-full">
                  <img src="https://picsum.photos/200" alt="Photo de profil" />
                </div>
              </div>
              <div>
                <div class="font-medium">Riley Carter</div>
                <div class="text-xs text-base-content/70">riley@email.com</div>
              </div>
            </div>
          </div>
          
        </div>
        
      </div>

      <div class="drawer-content flex flex-col overflow-auto">
        <!-- Navbar -->
        <div class="navbar bg-base-100 px-4 shadow-sm sticky top-0 z-9">

          <div class="flex-none lg:hidden">
            <label for="my-drawer" class="btn btn-square btn-ghost">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </label>
          </div>
          
          <div class="navbar-start">
            <div class="breadcrumbs text-sm md:text-md lg:text-lg">
              <ul>
                <li><a>Home</a></li>
                <li>Dashboard</li>
              </ul>
            </div>
          </div>

          <div class="navbar-center hidden lg:block">
            <span id="" class="text-xl font-ravenholm-bold">LBDR</span>
          </div>

          <div class="navbar-end gap-2">
            <!-- Date avec icône calendrier -->
            <div class="border border-base-300 rounded-lg px-3 py-1 flex items-center gap-2 hidden md:inline-flex">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9.75v7.5" />
              </svg>
              <span id="current-date" class="text-sm"></span>
            </div>

            <!-- Bouton notifications avec indicateur -->

            
            <div class="indicator">
              <span class="indicator-item badge badge-xs badge-secondary"></span> 
              <button class="btn btn-ghost btn-circle">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
              </button>
            </div>

            <!-- Bouton changement de thème -->
            <div class="dropdown dropdown-end">
              <button tabindex="0" role="button" class="btn btn-ghost btn-circle">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
              </button>
              <ul tabindex="0" class="dropdown-content z-10 menu p-2 shadow bg-base-100 rounded-box w-52">
                <li><a data-set-theme="light">Light</a></li>
                <li><a data-set-theme="dark">Dark</a></li>
                <li><a data-set-theme="cupcake">Cupcake</a></li>
                <li><a data-set-theme="bumblebee">Bumblebee</a></li>
                <li><a data-set-theme="caramellatte">Caramellatte</a></li>
                <li><a data-set-theme="retro">retro</a></li>
                <li><a data-set-theme="sunset">sunset</a></li>
                <li><a data-set-theme="pastel">pastel</a></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Main content -->
        <main class="flex-1 bg-base-100 px-4 " id="root">
          <div class="mx-auto w-full lg:max-w-[1200px] py-6">
          </div>
            
        </main>
      </div>

    </div>

    <script>
      // Formater la date au format "29 Juil 2025"
      document.getElementById('current-date').textContent = 
        new Date().toLocaleDateString('fr-FR', {
          day: 'numeric', 
          month: 'short', 
          year: 'numeric'
        });
        
      // Fonction pour changer le thème (à ajouter)
      document.querySelectorAll('[data-set-theme]').forEach(button => {
        button.addEventListener('click', () => {
          document.documentElement.setAttribute('data-theme', button.getAttribute('data-set-theme'));
        });
      });
    </script>

    <!-- Ajoutez ce script juste avant la fermeture du body -->
    <script>
      // Sélection des éléments du dropdown
      const dropdownBtn = document.getElementById('dropdown-parties-btn');
      const dropdownBtnText = dropdownBtn.querySelector('span');
      const dropdownItems = document.querySelectorAll('#dropdown-parties .dropdown-content a');
      
      // État actuel du filtre
      let currentFilter = 'all';
      
      // Initialiser les attributs aria-selected
      dropdownItems.forEach(item => {
        if (item.getAttribute('data-value') === 'all') {
          item.classList.add('active', 'selected');
          item.setAttribute('aria-selected', 'true');
        } else {
          item.setAttribute('aria-selected', 'false');
        }
      });
      
      // Ajouter des gestionnaires d'événements aux éléments du menu dropdown
      dropdownItems.forEach(item => {
        item.addEventListener('click', function() {
          // Récupérer la valeur du filtre
          const filterValue = this.getAttribute('data-value');
          const filterText = this.textContent;
          
          // Mettre à jour uniquement le texte du span dans le bouton
          dropdownBtnText.textContent = filterText;
          
          // Mettre à jour le filtre actuel
          currentFilter = filterValue;
          
          // Supprimer la classe active et selected de tous les éléments
          // et définir aria-selected="false"
          dropdownItems.forEach(el => {
            el.classList.remove('active', 'selected');
            el.setAttribute('aria-selected', 'false');
          });
          
          // Ajouter la classe active et selected à l'élément cliqué
          // et définir aria-selected="true"
          this.classList.add('active', 'selected');
          this.setAttribute('aria-selected', 'true');
          
          // Filtrer le contenu (à implémenter selon vos besoins)
          filterContent(filterValue);
        });
      });
      
      // Fonction pour filtrer le contenu
      function filterContent(filter) {
        console.log(`Filtrage par: ${filter}`);
        // Ici, vous pouvez implémenter la logique réelle de filtrage
        // Par exemple, afficher/masquer des éléments selon le filtre sélectionné
        
        // Exemple: Si vous avez des éléments avec des classes comme "item-campaign", "item-oneshot", etc.
        /*
        const allItems = document.querySelectorAll('.item');
        allItems.forEach(item => {
          if (filter === 'all' || item.classList.contains(`item-${filter}`)) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
        */
      }
    </script>
    <script>
      // Corriger le script pour la navbar qui disparaît au défilement
      document.addEventListener('DOMContentLoaded', function() {
        // Variables pour suivre le défilement
        let lastScrollTop = 0;
        const minTop = 20; //Seuil ou le menu doit toujours être afficher
        let scrollThreshold = 30; // Seuil pour considérer un défilement "fort"
        let scrollTimer = null;
        const navbar = document.querySelector('.navbar');
        const scrollContainer = document.querySelector('.drawer-content'); // Le conteneur qui défile
        
        // Ajout des classes de transition sur la navbar
        navbar.classList.add('transition-transform', 'duration-300', 'ease-in-out');
        
        // Écoute l'événement de défilement sur le conteneur qui défile réellement
        scrollContainer.addEventListener('scroll', function() {
          // Effacer le timeout précédent
          clearTimeout(scrollTimer);
          
          // Position actuelle du défilement
          const st = scrollContainer.scrollTop;
          
          if (st < minTop){            
            navbar.classList.remove('-translate-y-full');
          }

          // Calcul de la différence de défilement
          const scrollDifference = Math.abs(st - lastScrollTop);
          console.log(st, scrollDifference)
          
          // Vérifier si le défilement est assez "fort" (rapide)
          if (scrollDifference > scrollThreshold) {
            // Défilement vers le bas = masquer la navbar
            if (st > lastScrollTop && st > navbar.offsetHeight) {
              navbar.classList.add('-translate-y-full');
            } 
            // Défilement vers le haut = afficher la navbar
            else if (st < lastScrollTop) {
              navbar.classList.remove('-translate-y-full');
            }
          }
          
          // Mettre à jour la dernière position de défilement après un court délai
          scrollTimer = setTimeout(function() {
            lastScrollTop = st <= 0 ? 0 : st;
          }, 50);
        }, { passive: true });
      });
    </script>
    
    <!-- Initialiser les tabs -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tabs .tab');
        tabs.forEach(tab => {
          tab.addEventListener('click', function() {
            // Enlever la classe active de tous les tabs
            tabs.forEach(t => t.classList.remove('tab-active'));
            // Ajouter la classe active au tab cliqué
            this.classList.add('tab-active');
            
            // Ici vous pourriez ajouter la logique pour afficher le contenu correspondant
            // Par exemple, en fonction du texte du tab
            const tabName = this.textContent.trim();
            console.log(`Tab ${tabName} clicked`);
            
            // Exemple: montrer/cacher des sections
            // document.querySelectorAll('.tab-content').forEach(content => {
            //   content.style.display = 'none';
            // });
            // document.querySelector(`.tab-content-${tabName.toLowerCase()}`).style.display = 'block';
          });
        });
      });
      
      // Gestion du bouton d'édition du profil
      document.querySelector('.btn-primary').addEventListener('click', function() {
        // Ici vous pourriez ajouter la logique pour passer en mode édition
        // Par exemple, remplacer les éléments p par des inputs
        console.log('Modification du profil');
        
        // Exemple de code pour remplacer les éléments p par des inputs
        const infoFields = document.querySelectorAll('.card-body p.bg-base-300');
        infoFields.forEach(field => {
          const value = field.textContent.trim();
          const input = document.createElement('input');
          input.type = 'text';
          input.value = value;
          input.className = 'input input-bordered w-full';
          field.parentNode.replaceChild(input, field);
        });
        
        // Changer le texte du bouton
        this.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          Sauvegarder
        `;
      });
    </script>
    
  </body>
</html>
