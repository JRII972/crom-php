export default interface GameSession {
  id: number;              // "101" is a string, not a number
  date: Date;            // "04/04/2025" is a string (you could use Date if parsed)
  maitre_de_jeu: string;   // "Julien"
  jeu: string;             // "Warhammer"
  type: string;            // "Cmp"
  lieu: string;            // "FSV"
  coment: string;     // Long text description
  number_of_players_registered: number;
  max_player: number;             // "5" is a string (could be number if parsed)
  players: string[];       // Array of player names
  locked: boolean;         // true or false
  image: string;
  image_alt: string;
  short_coment: string;
  party_name: string; // "La bande à Julien"
}