import Typography from '@mui/material/Typography';
import { Box, Divider, Stack } from '@mui/material';
import LockOutlineIcon from '@mui/icons-material/LockOutline';
import CardsRoll, { PartyGameCardsRoll } from './components/CardsRoll';

import { vos_partie } from './data/parties_cards';
import GameSession from '../types/GameSession';
import groupedParties from './data/grouped_parties';

import { parties } from './data/parties';

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

function formatDateToFrench(date: Date|number): string {
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

export default function PlayerParties() {
  const sessions = groupByDate(Object.values(parties));

  const allDays = groupedParties.flatMap((week) => week.jours);
  
  return (
    <Stack
      spacing={2}
      sx={{
      mx: 3,
      pb: 5,
      mt: { xs: 8, md: 0, width: '90%' },
      }}
  >

      {/* Section semaine prochaine */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom:0 }}>
          Vos prochaines sessions
        </Typography>

        <Box sx={{ overflow:'auto', width: '100%', display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
          <CardsRoll title='Vendredi au FSV de 14h à 22h' sessions={sessions['2025-05-16']}/>
          <CardsRoll title='Samedi au FSV de 14h à 22h' sessions={sessions['2025-05-17']}/>
        </Box>
        <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem  />
      </Box>

      {/* Section dédier au proposition */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom:0 }}>
          Vos parties
        </Typography>

        <Box sx={{overflow:'auto', width: '100%',  display:'flex', flexDirection: 'column',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
        {
          vos_partie.map((data) => (
            <PartyGameCardsRoll key={data.id} title={data.title} sessions={data.parties as GameSession[]} />
          ))
        }
        </Box>
        <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem  />
      </Box>



      <Box >
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom:0 }}>
          Toutes vos prochaines partie
        </Typography>
        {/* TODO: améliorer cette affichage */}
        <Box sx={{ 
          overflow:'auto', width: '100%', 
          display:'flex',  
          flexWrap:'wrap', 
          gap:'1em',
          alignItems:'left',
          }} className={'className'}>
          {allDays.map((jour) => (
            <>
            <CardsRoll
              key={jour.date}
              title={formatDateToFrench(Date.parse(jour.date))}
              sessions={jour.sessions}
            />
            <Divider orientation="vertical" flexItem />
            </>
          ))}
        </Box>
      </Box>


    </Stack>
  );
}
