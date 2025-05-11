import { GameSession } from '../types/GameSession';
import { parties } from './parties';

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
        parties[0],
        parties[1],
        parties[2],
      ]
  },
  {
    id: 101,
    title: 'Horreur',
    parties: 
      [
        parties[4],
        parties[5],
        parties[6],
      ]
  },
  {
    id: 101,
    title: 'Entre Amis',
    parties: 
      [
        parties[7],
        parties[8],
        parties[9],
      ]
  },
];

export default parties_card;