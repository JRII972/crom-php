import Typography from '@mui/material/Typography';
import { Box, Divider, Stack } from '@mui/material';
import LockOutlineIcon from '@mui/icons-material/LockOutline';

import GameSession from '../types/GameSession';
import BigRecommandationCard from './components/BigRecommandationCard';

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



function playerNumber(partie:GameSession) {
  if (partie.locked) {
    return(
      <LockOutlineIcon fontSize="small"/>
    )
  } else {
    return(
      <Typography gutterBottom variant="subtitle2" component="div">
        {partie.number_of_players_registered}/{partie.max_player} joueurs
      </Typography>
    )
  }
}

export default function PartiesRecomendations(parties:GameSession[]) {
  const sessions = groupByDate(Object.values(parties));
  
  return (
    <Box sx={{ width: '100%'}}>

      {/* Section dédier au proposition */}

      {/* Section semaine prochaine */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom:0 }}>
          La semaine prochaine
        </Typography>

        <Stack gap={2}>
          <BigRecommandationCard partie={sessions['2025-04-04'][0]} />
        </Stack>
        <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem  />
      </Box>


      

    </Box>
  );
}
