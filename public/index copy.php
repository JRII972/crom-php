<!DOCTYPE html>
<html lang="fr" data-theme="caramellatte">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - LBDR</title>
  <link rel="stylesheet" href="http://localhost:4000/src/index.css">
  <style>
    @font-face {
      font-family: 'Ravenholm-Bold';
      src: url('/fonts/Ravenholm-Bold.ttf') format('truetype');
      font-weight: bold;
      font-style: normal;    }
    .font-ravenholm-bold {
      font-family: 'Ravenholm-Bold', sans-serif;
    }
  </style>
  <script src="src/assets/js/main.js"></script>
</head>
<body class="bg-base-200">
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full h-screen flex max-w-4xl">
      <div class="card bg-base-100 shadow-2xl h-fit m-auto" style="height: fit-content; margin: auto;">
        <div class="card-body">            
            <div class="flex flex-col lg:flex-row gap-8 w-min md:w-auto">
            <!-- Partie gauche - Image et texte d'introduction -->
            <div class="lg:min-w-[300px] lg:max-w-xl flex flex-col justify-center items-center p-6 bg-base-200 rounded-box">
                <div class="hidden lg:block">
                    <img id="login-illustration" src="https://picsum.photos/400/300" alt="Illustration connexion" class="max-w-full h-auto rounded-lg mb-6" />
                </div>
                <div class="text-center">
                    <h2 class="font-ravenholm-bold text-2xl mb-4">Bienvenue sur LBDR</h2>
                    <p class="mb-4">Connectez-vous pour accéder à toutes nos fonctionnalités et participer aux événements de la communauté.</p>
                    <p class="text-sm opacity-70">Pas encore membre ? <a href="#" class="link link-primary">Inscrivez-vous ici</a>.</p>
                </div>
            </div>

            <!-- Partie droite - Formulaire de connexion -->
            <div class="p-6 md:w-md md:min-w-[460px] flex flex-col items-center">
                <h2 class="text-2xl font-bold mb-6">Connexion</h2>
                
                <form id="login-form" class="space-y-4 min-w-[320px] w-min">
                <!-- Email -->
                <div class="form-control flex flex-col">
                    <label class="label">
                    <span class="label-text">Email</span>
                    </label>
                    <input type="text" id="email" placeholder="votre.email@exemple.com" class="input input-bordered" required />
                </div>
                
                <!-- Mot de passe -->
                <div class="form-control flex flex-col">
                    <label class="label">
                        <span class="label-text">Mot de passe</span>
                    </label>
                    <input type="password" id="password" placeholder="••••••••" class="input input-bordered" required />
                    <a href="#" class="label-text-alt link link-hover">Mot de passe oublié ?</a>
                </div>
                
                <!-- Remember me -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" id="remember-me" class="checkbox checkbox-primary" />
                    <span class="label-text">Se souvenir de moi</span>
                    </label>
                </div>
                
                <!-- Bouton de connexion -->
                <div class="form-control mt-6 w-full flex">
                    <button type="submit" class="btn btn-primary m-auto">Se connecter</button>
                </div>
                </form>
                
                <!-- Séparateur -->
                <div class="divider">OU</div>
                
                <!-- Connexion avec réseaux sociaux -->
                <div class="space-y-3">
                <button class="btn btn-outline w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 488 512">
                    <path fill="currentColor" d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/>
                    </svg>
                    Continuer avec Google
                </button>
                <button class="btn btn-outline w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 320 512">
                    <path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/>
                    </svg>
                    Continuer avec Facebook
                </button>
                </div>
            </div>            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts JavaScript -->
  <script src="src/assets/js/utils.js"></script>
  
  <script src="src/assets/js/login.js"></script>
  <script>
    // Redirection automatique si déjà connecté (géré côté JS)
    // Rien à faire ici, tout est dans login.js
  </script>
  <script>
// Images disponibles pour l'illustration de connexion
const loginImages = [
  '/data/images/Blades in the Dark.jpg',
  '/data/images/Chroniques_Oubliees.jpg',
  '/data/images/creature-cthulhu-grand-ancien.webp',
  '/data/images/Cyberpunk RED.png',
  '/data/images/donjon&dragon.jpg',
  '/data/images/EGS_Warhammer_SpaceMarine2.jpeg',
  '/data/images/logo-bdr.png',
  '/data/images/malediction-strahd.webp',
  '/data/images/Numenera.jpg',
  '/data/images/Pathfinder.webp',
  '/data/images/Shadowrun.jpg',
];

// Sélection aléatoire et application à l'image
window.addEventListener('DOMContentLoaded', () => {
  const img = document.getElementById('login-illustration');
  if (img) {
    function setRandomImage() {
      const random = Math.floor(Math.random() * loginImages.length);
      img.src = loginImages[random];
    }
    setRandomImage();
    setInterval(setRandomImage, 5000);
  }
});
</script>
</body>
</html>
