// Exemple de fichier partie.js

const partie = {
    "id": 101,
    "date": "2025-04-04",
    "maitre_de_jeu": "Rêveur en Chef",
    "jeu": "Warhammer",
    "type": "Cmp",
    "lieu": "FSV",
    "short_coment": "Plongez dans une guerre sans fin contre le Chaos. L’Empire a besoin de héros !",
    "max_player": 5,
    "players": ["Alice", "Bastien", "Camille", "Damien"],
    "number_of_players_registered": 4,
    "locked": false,
    "image": "/data/images/vampire_mascarde.avif",
    "image_alt": "Chaos Space Marines in battle",
    "party_name": "Les Ombres du Chaos",
    "coment": "Warhammer est un jeu de rôle immersif dans un univers de dark fantasy où l’humanité lutte contre les forces du Chaos dans un monde médiéval brutal. Les joueurs incarnent des héros de l’Empire, confrontés à des cultistes, des mutants et des démons. Le système de jeu, riche et détaillé, met l’accent sur des combats tactiques et des choix aux conséquences souvent tragiques. L’ambiance est sombre, presque désespérée, mais c’est dans cette adversité que les personnages trouvent leur gloire. Parfait pour ceux qui aiment les récits épiques et les dilemmes moraux complexes.",
    mj: {
      nom: "MJ Elodie",
      avatar: "https://example.com/avatars/elodie.jpg"
    },
    joueurs: [
      { id: 1, nom: "Arthur", pseudo: 'Prètre de pacotille', avatar: "https://example.com/avatars/arthur.jpg" },
      { id: 4, nom: "B", pseudo: 'Prètre de pacotille', avatar: "https://example.com/avatars/arthur.jpg" },
      { id: 5, nom: "C", pseudo: 'Prètre de pacotille', avatar: "https://example.com/avatars/arthur.jpg" },
      { id: 6, nom: "D", pseudo: 'Prètre de pacotille', avatar: "https://example.com/avatars/arthur.jpg" },
      { id: 7, nom: "E", avatar: "https://example.com/avatars/sophie.jpg" },
      { id: 8, nom: "F", avatar: "https://example.com/avatars/sophie.jpg" },
      { id: 9, nom: "G", avatar: "https://example.com/avatars/sophie.jpg" },
      { id: 10, nom: "H", avatar: "https://example.com/avatars/sophie.jpg" },
      { id: 11, nom: "I", avatar: "https://example.com/avatars/sophie.jpg" },
      { id: 12, nom: "J", avatar: "https://example.com/avatars/sophie.jpg" },
      { id: 13, nom: "K", avatar: "https://example.com/avatars/maxime.jpg" }
    ],
    prochainesSessions: [
      {
        id: 201,
        date: "2025-05-05T19:30:00Z",
        lieu: "FSV",
        joueurs: [
          { id: 1, nom: "Arthur G", pseudo: 'Prètre de pacotille', avatar: "https://example.com/avatars/arthur.jpg" },
          { id: 2, nom: "Arthur", avatar: "https://example.com/avatars/arthur.jpg" },
          { id: 3, nom: "Arthur", avatar: "https://example.com/avatars/arthur.jpg" },
          { id: 4, nom: "Arthur", avatar: "https://example.com/avatars/arthur.jpg" },
          { id: 5, nom: "Sophie", avatar: "https://example.com/avatars/sophie.jpg" }
        ]
      },
      {
        id: 202,
        date: "2025-05-12T19:30:00Z",
        lieu: "Discord",
        joueurs: [
          { id: 1, nom: "Arthur", avatar: "https://example.com/avatars/arthur.jpg" },
          { id: 3, nom: "Maxime", avatar: "https://example.com/avatars/maxime.jpg" }
        ]
      }
    ]
  };
  
  export default partie;
  