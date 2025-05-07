import { GameSession } from '../../types/GameSession'; // Assumons que GameSession est défini dans un fichier types.ts

import { parties } from './parties';

// Interface pour la structure des données regroupées
export interface DaySessions {
  date: string;
  sessions: GameSession[];
}

export interface WeekSessions {
  titre: string;
  jours: DaySessions[];
}

// Fonction pour obtenir les dates de début et fin d'une semaine à partir d'une date
const getWeekRange = (date: Date): { start: string; end: string } => {
  const startOfWeek = new Date(date);
  startOfWeek.setDate(date.getDate() - date.getDay() + (date.getDay() === 0 ? -6 : 1)); // Lundi
  const endOfWeek = new Date(startOfWeek);
  endOfWeek.setDate(startOfWeek.getDate() + 6); // Dimanche

  const formatDate = (d: Date): string => 
    d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });

  return {
    start: formatDate(startOfWeek),
    end: formatDate(endOfWeek),
  };
};

// Fonction pour obtenir une clé unique pour une semaine
const getWeekKey = (date: Date): string => {
  const startOfWeek = new Date(date);
  startOfWeek.setDate(date.getDate() - date.getDay() + (date.getDay() === 0 ? -6 : 1));
  return startOfWeek.toISOString().split('T')[0];
};

// Transformation des données
const groupedByDay: { [key: string]: DaySessions } = parties.reduce((acc, session) => {
  if (!acc[session.date]) {
    acc[session.date] = { date: session.date, sessions: [] };
  }
  acc[session.date].sessions.push(session);
  return acc;
}, {} as { [key: string]: DaySessions });

const groupedByWeek: WeekSessions[] = Object.values(groupedByDay).reduce((acc, day) => {
  const date = new Date(day.date);
  const weekKey = getWeekKey(date);
  const weekRange = getWeekRange(date);

  let week = acc.find(w => w.titre.includes(weekRange.start));
  if (!week) {
    week = { titre: `Semaine du ${weekRange.start} au ${weekRange.end}`, jours: [] };
    acc.push(week);
  }

  week.jours.push(day);
  return acc;
}, [] as WeekSessions[]);

// Tri des semaines par date de début et des jours par date
groupedByWeek.sort((a, b) => {
  const dateA = new Date(a.jours[0].date);
  const dateB = new Date(b.jours[0].date);
  return dateA.getTime() - dateB.getTime();
});

groupedByWeek.forEach(week => {
  week.jours.sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
});

export const groupedParties: WeekSessions[] = groupedByWeek;

export default groupedParties;