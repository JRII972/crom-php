import { GameSession } from '../types/GameSession';

export interface PartyCardData {
  id: number;
  title: string;
  parties: GameSession[];
}

export const parties_card: PartyCardData[] = [
  {
    id: 101,
    title: 'Fantaisie',
    parties: 
      [
        {
          "id": 101,
          "date": "2025-04-04",
          "maitre_de_jeu": "Julien",
          "jeu": "Warhammer",
          "type": "Cmp",
          "lieu": "FSV",
          "short_coment": "Plongez dans une guerre sans fin contre le Chaos. L’Empire a besoin de héros !",
          "max_player": 5,
          "players": ["Alice", "Bastien", "Camille", "Damien"],
          "number_of_players_registered": 4,
          "locked": false,
          "image": "https://cdn1.epicgames.com/offer/f640a0c1648147fea7e81565b45a3003/EGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840",
          "image_alt": "Chaos Space Marines in battle",
          "coment": "Warhammer est un jeu de rôle immersif dans un univers de dark fantasy où l’humanité lutte contre les forces du Chaos dans un monde médiéval brutal. Les joueurs incarnent des héros de l’Empire, confrontés à des cultistes, des mutants et des démons. Le système de jeu, riche et détaillé, met l’accent sur des combats tactiques et des choix aux conséquences souvent tragiques. L’ambiance est sombre, presque désespérée, mais c’est dans cette adversité que les personnages trouvent leur gloire. Parfait pour ceux qui aiment les récits épiques et les dilemmes moraux complexes."
        },
        {
          "id": 102,
          "date": "2025-04-04",
          "maitre_de_jeu": "Pierre",
          "jeu": "L’Appel de Cthulhu",
          "type": "1Sht",
          "lieu": "FSV",
          "short_coment": "Un mystère indicible vous attend. Survivrez-vous à la folie des Grands Anciens ?",
          "max_player": 6,
          "players": ["Karine", "Laurent", "Manon", "Nicolas", "Ophélie", "Pascal"],
          "number_of_players_registered": 6,
          "locked": true,
          "image": "https://cthulhu-rpg.com/artwork/great_old_one_cthulhu.jpg",
          "image_alt": "Depiction of the Great Old One Cthulhu",
          "coment": "L’Appel de Cthulhu est un monument du jeu de rôle, ancré dans l’horreur cosmique de H.P. Lovecraft. Les joueurs, souvent des investigateurs ordinaires, plongent dans des enquêtes où chaque découverte menace leur santé mentale. Le système privilégie l’ambiance et la narration, avec des mécaniques simples mais efficaces pour simuler la fragilité humaine face à l’inconnu. Ce jeu excelle dans la création de tension, où la survie est rare et la victoire toujours amère. Idéal pour les amateurs de mystères oppressants et d’histoires où l’humanité est insignifiante."
        },
        {
          "id": 103,
          "date": "2025-04-05",
          "maitre_de_jeu": "Marie",
          "jeu": "Donjons et Dragons",
          "type": "Cmp",
          "lieu": "MDA",
          "short_coment": "Une quête épique commence dans les terres oubliées. Affrontez dragons et mystères !",
          "max_player": 5,
          "players": ["François", "Gabrielle", "Hugo", "Isabelle"],
          "number_of_players_registered": 4,
          "locked": false,
          "image": "https://dnd.wizards.com/images/dragon_horde.jpg",
          "image_alt": "A horde of dragons guarding treasure",
          "coment": "Donjons et Dragons (D&D) est le jeu de rôle par excellence, définissant le genre depuis les années 1970. Dans cet univers de high fantasy, les joueurs explorent des royaumes remplis de dragons, de sorciers et de trésors cachés. La 5e édition offre un équilibre parfait entre accessibilité pour les novices et profondeur pour les vétérans, avec des mécaniques fluides pour le combat et la narration. Les campagnes, comme celle-ci, permettent des aventures épiques sur plusieurs sessions, où chaque choix forge une légende. Un incontournable pour les amateurs de fantasy héroïque."
        }
      ]
  },
  {
    id: 101,
    title: 'Horreur',
    parties: 
      [
        {
          "id": 101,
          "date": "2025-04-04",
          "maitre_de_jeu": "Julien",
          "jeu": "Warhammer",
          "type": "Cmp",
          "lieu": "FSV",
          "short_coment": "Plongez dans une guerre sans fin contre le Chaos. L’Empire a besoin de héros !",
          "max_player": 5,
          "players": ["Alice", "Bastien", "Camille", "Damien"],
          "number_of_players_registered": 4,
          "locked": false,
          "image": "https://cdn1.epicgames.com/offer/f640a0c1648147fea7e81565b45a3003/EGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840",
          "image_alt": "Chaos Space Marines in battle",
          "coment": "Warhammer est un jeu de rôle immersif dans un univers de dark fantasy où l’humanité lutte contre les forces du Chaos dans un monde médiéval brutal. Les joueurs incarnent des héros de l’Empire, confrontés à des cultistes, des mutants et des démons. Le système de jeu, riche et détaillé, met l’accent sur des combats tactiques et des choix aux conséquences souvent tragiques. L’ambiance est sombre, presque désespérée, mais c’est dans cette adversité que les personnages trouvent leur gloire. Parfait pour ceux qui aiment les récits épiques et les dilemmes moraux complexes."
        },
        {
          "id": 102,
          "date": "2025-04-04",
          "maitre_de_jeu": "Pierre",
          "jeu": "L’Appel de Cthulhu",
          "type": "1Sht",
          "lieu": "FSV",
          "short_coment": "Un mystère indicible vous attend. Survivrez-vous à la folie des Grands Anciens ?",
          "max_player": 6,
          "players": ["Karine", "Laurent", "Manon", "Nicolas", "Ophélie", "Pascal"],
          "number_of_players_registered": 6,
          "locked": true,
          "image": "https://cthulhu-rpg.com/artwork/great_old_one_cthulhu.jpg",
          "image_alt": "Depiction of the Great Old One Cthulhu",
          "coment": "L’Appel de Cthulhu est un monument du jeu de rôle, ancré dans l’horreur cosmique de H.P. Lovecraft. Les joueurs, souvent des investigateurs ordinaires, plongent dans des enquêtes où chaque découverte menace leur santé mentale. Le système privilégie l’ambiance et la narration, avec des mécaniques simples mais efficaces pour simuler la fragilité humaine face à l’inconnu. Ce jeu excelle dans la création de tension, où la survie est rare et la victoire toujours amère. Idéal pour les amateurs de mystères oppressants et d’histoires où l’humanité est insignifiante."
        },
        {
          "id": 103,
          "date": "2025-04-05",
          "maitre_de_jeu": "Marie",
          "jeu": "Donjons et Dragons",
          "type": "Cmp",
          "lieu": "MDA",
          "short_coment": "Une quête épique commence dans les terres oubliées. Affrontez dragons et mystères !",
          "max_player": 5,
          "players": ["François", "Gabrielle", "Hugo", "Isabelle"],
          "number_of_players_registered": 4,
          "locked": false,
          "image": "https://dnd.wizards.com/images/dragon_horde.jpg",
          "image_alt": "A horde of dragons guarding treasure",
          "coment": "Donjons et Dragons (D&D) est le jeu de rôle par excellence, définissant le genre depuis les années 1970. Dans cet univers de high fantasy, les joueurs explorent des royaumes remplis de dragons, de sorciers et de trésors cachés. La 5e édition offre un équilibre parfait entre accessibilité pour les novices et profondeur pour les vétérans, avec des mécaniques fluides pour le combat et la narration. Les campagnes, comme celle-ci, permettent des aventures épiques sur plusieurs sessions, où chaque choix forge une légende. Un incontournable pour les amateurs de fantasy héroïque."
        }
      ]
  },
  {
    id: 101,
    title: 'Entre Amis',
    parties: 
      [
        {
          "id": 101,
          "date": "2025-04-04",
          "maitre_de_jeu": "Julien",
          "jeu": "Warhammer",
          "type": "Cmp",
          "lieu": "FSV",
          "short_coment": "Plongez dans une guerre sans fin contre le Chaos. L’Empire a besoin de héros !",
          "max_player": 5,
          "players": ["Alice", "Bastien", "Camille", "Damien"],
          "number_of_players_registered": 4,
          "locked": false,
          "image": "https://cdn1.epicgames.com/offer/f640a0c1648147fea7e81565b45a3003/EGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840",
          "image_alt": "Chaos Space Marines in battle",
          "coment": "Warhammer est un jeu de rôle immersif dans un univers de dark fantasy où l’humanité lutte contre les forces du Chaos dans un monde médiéval brutal. Les joueurs incarnent des héros de l’Empire, confrontés à des cultistes, des mutants et des démons. Le système de jeu, riche et détaillé, met l’accent sur des combats tactiques et des choix aux conséquences souvent tragiques. L’ambiance est sombre, presque désespérée, mais c’est dans cette adversité que les personnages trouvent leur gloire. Parfait pour ceux qui aiment les récits épiques et les dilemmes moraux complexes."
        },
        {
          "id": 102,
          "date": "2025-04-04",
          "maitre_de_jeu": "Pierre",
          "jeu": "L’Appel de Cthulhu",
          "type": "1Sht",
          "lieu": "FSV",
          "short_coment": "Un mystère indicible vous attend. Survivrez-vous à la folie des Grands Anciens ?",
          "max_player": 6,
          "players": ["Karine", "Laurent", "Manon", "Nicolas", "Ophélie", "Pascal"],
          "number_of_players_registered": 6,
          "locked": true,
          "image": "https://cthulhu-rpg.com/artwork/great_old_one_cthulhu.jpg",
          "image_alt": "Depiction of the Great Old One Cthulhu",
          "coment": "L’Appel de Cthulhu est un monument du jeu de rôle, ancré dans l’horreur cosmique de H.P. Lovecraft. Les joueurs, souvent des investigateurs ordinaires, plongent dans des enquêtes où chaque découverte menace leur santé mentale. Le système privilégie l’ambiance et la narration, avec des mécaniques simples mais efficaces pour simuler la fragilité humaine face à l’inconnu. Ce jeu excelle dans la création de tension, où la survie est rare et la victoire toujours amère. Idéal pour les amateurs de mystères oppressants et d’histoires où l’humanité est insignifiante."
        },
        {
          "id": 103,
          "date": "2025-04-05",
          "maitre_de_jeu": "Marie",
          "jeu": "Donjons et Dragons",
          "type": "Cmp",
          "lieu": "MDA",
          "short_coment": "Une quête épique commence dans les terres oubliées. Affrontez dragons et mystères !",
          "max_player": 5,
          "players": ["François", "Gabrielle", "Hugo", "Isabelle"],
          "number_of_players_registered": 4,
          "locked": false,
          "image": "https://dnd.wizards.com/images/dragon_horde.jpg",
          "image_alt": "A horde of dragons guarding treasure",
          "coment": "Donjons et Dragons (D&D) est le jeu de rôle par excellence, définissant le genre depuis les années 1970. Dans cet univers de high fantasy, les joueurs explorent des royaumes remplis de dragons, de sorciers et de trésors cachés. La 5e édition offre un équilibre parfait entre accessibilité pour les novices et profondeur pour les vétérans, avec des mécaniques fluides pour le combat et la narration. Les campagnes, comme celle-ci, permettent des aventures épiques sur plusieurs sessions, où chaque choix forge une légende. Un incontournable pour les amateurs de fantasy héroïque."
        }
      ]
  },
];

export default parties_card;