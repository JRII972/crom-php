import Typography from '@mui/material/Typography';
import { Box, Divider } from '@mui/material';
import LockOutlineIcon from '@mui/icons-material/LockOutline';
import CardsRoll from './components/CardsRoll';
import { Session } from '../api/types/db';

// Function to group by date
const groupSessionsByDay = (sessionsList: Session[]) => {
  const grouped = {};
  sessionsList.forEach((session) => {
    const date = session.date_session;
    if (!grouped[date]) {
      grouped[date] = [];
    }
    grouped[date].push(session);
  });
  // Retourne un objet { date: sessions[] }
  return grouped;
};

// Function to group by week
const groupSessionsByWeek = (sessionsList: Session[]) => {
  const grouped = {};
  sessionsList.forEach((session) => {
    const sessionDate = new Date(session.date_session);
    // Récupère le premier jour de la semaine (lundi)
    const weekStart = new Date(sessionDate);
    weekStart.setDate(sessionDate.getDate() - sessionDate.getDay() + 1);
    const weekKey = weekStart.toISOString().split('T')[0];
    
    if (!grouped[weekKey]) {
      grouped[weekKey] = {
        weekStart: weekStart,
        sessions: []
      };
    }
    grouped[weekKey].sessions.push(session);
  });
  // Retourne un objet { weekKey: { weekStart, sessions[] } }
  return grouped;
};

// Function to group by location
const groupSessionsByLocation = (sessionsList: Session[]) => {
  const grouped = {};
  sessionsList.forEach((session) => {
    const location = session.lieu.nom;
    if (!grouped[location]) {
      grouped[location] = {
        location: location,
        sessions: []
      };
    }
    grouped[location].sessions.push(session);
  });
  // Retourne un objet { location: { location, sessions[] } }
  return grouped;
};

export function formatDateToFrench(date: Date | string | number): string {
  if (typeof(date) === 'string') {
    date = Date.parse(date);
  } 
  const options: Intl.DateTimeFormatOptions = {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
  };
  const formatted = new Intl.DateTimeFormat('fr-FR', options).format(date);
  return formatted
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}

export function formatWeekToFrench(date: Date, sessions: Session[]): string {
  const weekEnd = new Date(date);
  weekEnd.setDate(date.getDate() + 6);
  
  const monthStart = new Intl.DateTimeFormat('fr-FR', { month: 'long' }).format(date);
  const monthEnd = new Intl.DateTimeFormat('fr-FR', { month: 'long' }).format(weekEnd);
  
  return `Semaine du ${date.getDate()} ${monthStart} au ${weekEnd.getDate()} ${monthEnd}`;
}

interface PartiesCardProps {
  sessionsList: Session[];
  setSessionsList: (sessions: Session[]) => void;
}

export default function PartiesCard({ sessionsList, setSessionsList }: PartiesCardProps) {
  // Filtrer les sessions futures (à partir de maintenant)
  const today = new Date();
  const futureSessionsList = sessionsList.filter(session => 
    new Date(session.date_session) >= today
  );
  
  // Trier les sessions par date
  const sortedSessions = [...futureSessionsList].sort(
    (a, b) => new Date(a.date_session).getTime() - new Date(b.date_session).getTime()
  );
  
  // Récupérer les sessions de la semaine prochaine
  const nextWeekStart = new Date();
  nextWeekStart.setDate(nextWeekStart.getDate() + 7 - nextWeekStart.getDay() + 1);
  const nextWeekEnd = new Date(nextWeekStart);
  nextWeekEnd.setDate(nextWeekStart.getDate() + 6);
  
  const nextWeekSessions = sortedSessions.filter(session => {
    const sessionDate = new Date(session.date_session);
    return sessionDate >= nextWeekStart && sessionDate <= nextWeekEnd;
  });
  
  // Regrouper les sessions par jour pour la semaine prochaine
  const nextWeekByDay = groupSessionsByDay(nextWeekSessions);
  
  // Regrouper le reste des sessions par semaine
  const remainingSessions = sortedSessions.filter(session => 
    new Date(session.date_session) > nextWeekEnd
  );
  const sessionsByWeek = groupSessionsByWeek(remainingSessions);
  
  // Récupérer les suggestions (à adapter selon votre logique métier)
  // Pour cet exemple, j'utilise simplement les premières sessions futures comme suggestions
  const suggestedSessions = sortedSessions.slice(0, Math.min(5, sortedSessions.length));
  
  return (
    <Box sx={{ width: '100%' }}>
      {/* Section des suggestions */}
      {suggestedSessions.length > 0 && (
        <Box>
          <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom: 0, mx: 2 }}>
            Elles pourraient vous intéresser
          </Typography>

          <Box sx={{ overflow: 'auto', width: '100%', display: 'inline-flex', flexWrap: 'nowrap', gap: '1em' }}>
            <CardsRoll 
              title="Suggestions" 
              sessions={suggestedSessions} 
              displayDate={true}
            />
          </Box>
          <Divider sx={{ margin: '2em' }} orientation="horizontal" variant="middle" flexItem />
        </Box>
      )}

      {/* Section semaine prochaine */}
      {Object.keys(nextWeekByDay).length > 0 && (
        <Box>
          <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom: 0, mx: 2 }}>
            La semaine prochaine
          </Typography>

          <Box sx={{ overflow: 'auto', width: '100%', display: 'inline-flex', flexWrap: 'nowrap', gap: '1em' }}>
            {Object.entries(nextWeekByDay).map(([date, sessions]) => (
              <CardsRoll 
                key={date} 
                title={formatDateToFrench(date)} 
                sessions={sessions as Session[]}
              />
            ))}
          </Box>
          <Divider sx={{ margin: '2em' }} orientation="horizontal" variant="middle" flexItem />
        </Box>
      )}

      {/* Section des semaines suivantes */}
      {Object.keys(sessionsByWeek).length > 0 && (
        Object.entries(sessionsByWeek).map(([weekKey, weekData]) => (
          <Box key={weekKey}>
            <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom: 0, mx: 2 }}>
              {formatWeekToFrench(weekData.weekStart, weekData.sessions)}
            </Typography>

            <Box sx={{ 
              overflow: 'auto', 
              width: '100%', 
              display: 'inline-flex',  
              flexWrap: 'nowrap', 
              gap: '1em',
            }}>
              {/* Regrouper par jour dans la semaine */}
              {Object.entries(groupSessionsByDay(weekData.sessions)).map(([date, daySessions]) => (
                <CardsRoll 
                  key={date} 
                  title={formatDateToFrench(date)} 
                  sessions={daySessions as Session[]}
                />
              ))}
            </Box>
            <Divider sx={{ margin: '2em' }} orientation="horizontal" variant="middle" flexItem />
          </Box>
        ))
      )}
    </Box>
  );
}
