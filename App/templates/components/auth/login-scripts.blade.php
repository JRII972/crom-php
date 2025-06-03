{{-- Scripts spécifiques à la page de connexion --}}
<script src="assets/js/login.js"></script>
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
      img.style.backgroundImage = `url('${loginImages[random]}')`;
    }
    setRandomImage();
    setInterval(setRandomImage, 5000);
  }
});
</script>
