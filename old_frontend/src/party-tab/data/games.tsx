import React from 'react';

interface Game {
  nom: string;
  description: string;
  short_coment?: string;
  image: string;
  categories?: string[];
  image_alt?: string;
  icon?: string;
  displayName?: React.ReactNode; 
}

const games: Game[] = [
  {
    "nom": "Warhammer",
    "description": "Warhammer est un jeu de rôle immersif dans un univers de dark fantasy où l’humanité lutte contre les forces du Chaos dans un monde médiéval brutal. Les joueurs incarnent des héros de l’Empire, confrontés à des cultistes, des mutants et des démons. Le système de jeu, riche et détaillé, met l’accent sur des combats tactiques et des choix aux conséquences souvent tragiques. L’ambiance est sombre, presque désespérée, mais c’est dans cette adversité que les personnages trouvent leur gloire. Parfait pour ceux qui aiment les récits épiques et les dilemmes moraux complexes.",
    "image": "https://cdn1.epicgames.com/offer/f640a0c1648147fea7e81565b45a3003/EGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840",
    "icon": "/data/images/icon/Warhammer40k.png",
    "categories" : ['Guerre', 'Espace', 'Alien'],
  },
  {
    "nom": "L’Appel de Cthulhu",
    "description": "L’Appel de Cthulhu est un monument du jeu de rôle, ancré dans l’horreur cosmique de H.P. Lovecraft. Les joueurs, souvent des investigateurs ordinaires, plongent dans des enquêtes où chaque découverte menace leur santé mentale. Le système privilégie l’ambiance et la narration, avec des mécaniques simples mais efficaces pour simuler la fragilité humaine face à l’inconnu. Ce jeu excelle dans la création de tension, où la survie est rare et la victoire toujours amère. Idéal pour les amateurs de mystères oppressants et d’histoires où l’humanité est insignifiante.",
    "image": "/data/images/creature-cthulhu-grand-ancien.webp",
    // "icon": "https://cthulhu-rpg.com/icons/elder_sign.png"
  },
  {
    "nom": "Donjons et Dragons",
    "description": "Donjons et Dragons (D&D) est le jeu de rôle par excellence, définissant le genre depuis les années 1970. Dans cet univers de high fantasy, les joueurs explorent des royaumes remplis de dragons, de sorciers et de trésors cachés. La 5e édition offre un équilibre parfait entre accessibilité pour les novices et profondeur pour les vétérans, avec des mécaniques fluides pour le combat et la narration. Les campagnes, comme celle-ci, permettent des aventures épiques sur plusieurs sessions, où chaque choix forge une légende. Un incontournable pour les amateurs de fantasy héroïque.",
    "image": "/data/images/donjon&dragon.jpg",
    "icon": "/data/images/icon/dnd_logo_big.webp"
  },
  {
    "nom": "Vampire: La Mascarade",
    "description": "Vampire: La Mascarade plonge les joueurs dans un monde gothique-punk où les vampires manipulent les mortels tout en luttant pour leur humanité. Chaque personnage appartient à un clan avec ses propres pouvoirs et intrigues, rendant les interactions sociales aussi cruciales que les affrontements. Le système narratif met l’accent sur les dilemmes moraux et les jeux de pouvoir, avec une ambiance de trahison constante. Cette session courte promet une immersion rapide dans un univers de complots nocturnes. Parfait pour ceux qui aiment les histoires de pouvoir et de séduction sombre.",
    "image": "/data/images/vampire_mascarde.avif",
    // "icon": "https://worldofdarkness.com/icons/ankh_vampire.png"
  },
  {
    "nom": "Chroniques Oubliées",
    "description": "Chroniques Oubliées est un jeu de rôle français accessible, parfait pour des aventures médiévales fantastiques. Son système simplifié, basé sur des profils de personnages modulables, permet de plonger rapidement dans l’action. Les joueurs incarnent des héros classiques – chevaliers, mages ou voleurs – dans des quêtes héroïques remplies de combats et de magie. Cette session courte est idéale pour découvrir le jeu ou vivre une aventure sans engagement à long terme. Un excellent choix pour les débutants comme pour les vétérans en quête de fun immédiat.",
    "image": "/data/images/Chroniques_Oubliees.jpg",
    // "icon": "https://chroniquesoubliees.fr/icons/sword_shield.png"
  },
  {
    "nom": "Shadowrun",
    "description": "Shadowrun est un jeu de rôle unique mêlant cyberpunk et fantasy dans un futur dystopique où mégacorporations dominent le monde. Les joueurs incarnent des 'runners', des mercenaires équipés de cyber-implants, de magie ou de compétences technologiques, effectuant des missions illégales. Le système combine des mécaniques de dés complexes pour le combat, le piratage et la sorcellerie, offrant une grande liberté dans la résolution des problèmes. L’ambiance néon, mêlée de dragons et de chamanisme, est parfaite pour les amateurs d’histoires audacieuses et de mondes hybrides.",
    "image": "/data/images/Shadowrun",
    // "icon": "https://shadowrunrpg.com/icons/matrix_logo.png"
  },
  {
    "nom": "Numenera",
    "description": "Numenera est un jeu de rôle de science-fantasy se déroulant sur une Terre vieille de plusieurs milliards d’années, où les civilisations avancées ont laissé des reliques mystérieuses. Les joueurs explorent ce monde médiéval-futuriste, découvrant des artefacts technologiques appelés 'numenera'. Le système Cypher privilégie la narration et l’exploration, avec des mécaniques simples mais profondes pour gérer les découvertes et les conflits. L’ambiance, mêlant émerveillement et danger, est idéale pour ceux qui aiment les récits d’aventure et de mystère cosmique.",
    "image": "/data/images/Numenera.jpg",
    // "icon": "https://numenera.com/icons/numenera_symbol.png"
  },
  {
    "nom": "Blades in the Dark",
    "description": "Blades in the Dark est un jeu de rôle indépendant où les joueurs incarnent une bande de criminels dans la ville hantée de Doskvol, un univers victorien sombre teinté de surnaturel. Le système met l’accent sur la planification de casses audacieux et les conséquences imprévues, avec des mécaniques fluides pour gérer le stress et les rivalités. L’ambiance gritty, où chaque décision peut bouleverser l’équilibre des factions, plaît aux amateurs d’histoires collaboratives et de drames intenses.",
    "image": "/data/images/Blades in the Dark.jpg",
    // "icon": "https://bladesinthedark.com/icons/dagger_icon.png"
  },
  {
    "nom": "Cyberpunk RED",
    "description": "Cyberpunk RED est un jeu de rôle dans l’univers de Cyberpunk 2077, se déroulant dans un futur où la technologie et la violence dominent. Les joueurs incarnent des mercenaires, hackers ou nomades dans Night City, affrontant mégacorporations et gangs. Le système, basé sur des dés et des compétences personnalisables, offre des combats dynamiques et des intrigues complexes. L’ambiance néon, teintée de désespoir et de rébellion, est parfaite pour les fans de récits cyberpunk modernes et immersifs.",
    "image": "/data/images/Cyberpunk RED.png",
    // "icon": "https://cyberpunk.net/icons/chip_cyberware.png"
  },
  {
    "nom": "Pathfinder",
    "description": "Pathfinder est un jeu de rôle high fantasy, souvent considéré comme un successeur spirituel de Donjons et Dragons 3.5. Dans l’univers de Golarion, les joueurs créent des aventuriers explorant des donjons, affrontant des monstres et dévoilant des intrigues épiques. Le système, riche en options de personnalisation, permet de créer des personnages uniques grâce à des classes, des dons et des sorts variés. Idéal pour les vétérans cherchant une expérience tactique et profonde, ainsi que pour les fans de récits héroïques.",
    "image": "/data/images/Pathfinder.jpg",
    // "icon": "https://paizo.com/icons/pathfinder_logo.png"
  }
];

function findGameByName(gameName: string): Game | null {
  if (typeof gameName !== 'string' || gameName.trim() === '') {
    return null;
  }
  const normalizedName = gameName.trim().toLowerCase();
  const game = games.find((game) => game.nom.toLowerCase() === normalizedName) || null;

  if (game === null) {
    return null;
  }

  // Créer une copie de l'objet game pour éviter de modifier l'original
  const gameWithDisplayName: Game = {
    ...game,
    displayName: game.icon ? (
      <img
        src={game.icon}
        alt={game.nom}
        style={{
          // width: '1em', // Taille équivalente à la hauteur du texte
          height: '1.5em',
          verticalAlign: 'middle', // Alignement avec le texte
        }}
      />
    ) : (
      game.nom
    ),
  };

  return gameWithDisplayName;
}

export { games, findGameByName };