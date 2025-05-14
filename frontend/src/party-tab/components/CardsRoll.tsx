import * as React from 'react';
import Typography from '@mui/material/Typography';
import { Box, Stack } from '@mui/material';
import PartyCard from './PartyCard';
import { GameSession } from '../../types/GameSession';

// Function to group by date
const groupByDate = (array) => {
  return array.reduce((result, session) => {
    const date = session.date;
    if (!result[date]) {
      result[date] = [];
    }
    result[date].push(session);
    return result;
  }, {});
};

function formatDateToFrench(date: Date): string {
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


export default function CardsRoll({ title, sessions, type, displayDate }: { title: string; sessions: GameSession[]; type?: 'session' | 'game' | 'party'; displayDate?: boolean }): React.ReactElement {
  return (
    <Stack 
      direction='column'
      spacing={1} 
      sx={{ paddingBottom:'1em', paddingTop:'1em', mx: 1}}>
      <Box sx={{ display:'inline-flex', gap:'1em'}}>
        <Typography variant="subtitle1" sx={{ 
          paddingLeft:'1em',
          position:'sticky',
          left:0, 
          whiteSpace:'nowrap',
          }}>
          {title}
        </Typography>
      </Box>
      <Box sx={{ display:'inline-flex', gap:'1em' }}>
        {sessions.map((partie:GameSession) => ( 
          <PartyCard partie={partie} key={partie.id} type={type} displayDate={displayDate}/>
          ))}
      </Box>
    </Stack>
  );
}

export function PartyGameCardsRoll({ title, sessions, displayDate }: { title: string; sessions: GameSession[], displayDate?:boolean }): React.ReactElement {
  return (
    <CardsRoll title={title} sessions={sessions} type='game' displayDate={displayDate} />
  );
}
