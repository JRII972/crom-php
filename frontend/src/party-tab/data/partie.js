// Exemple de fichier partie.js

const partie = {
    nom: "La Malédiction de Strahd",
    image: "/data/images/EGS_Warhammer_SpaceMarine2.jpeg",
    description: "Une campagne gothique dans l'univers lugubre de Ravenloft. Oserez-vous affronter le seigneur vampire Strahd ?",
    type: "campagne", // ou "one_shot"
    mj: {
      nom: "MJ Elodie",
      avatar: "https://example.com/avatars/elodie.jpg"
    },
    joueurs: [
      { id: 1, nom: "Arthur", pseudo: 'Prètre de pacotille', avatar: "https://example.com/avatars/arthur.jpg" },
      { id: 2, nom: "Sophie", avatar: "https://example.com/avatars/sophie.jpg" },
      { id: 3, nom: "Maxime", avatar: "https://example.com/avatars/maxime.jpg" }
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
  