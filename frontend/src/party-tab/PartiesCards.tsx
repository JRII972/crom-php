import Typography from '@mui/material/Typography';
import { Box, Divider } from '@mui/material';
import LockOutlineIcon from '@mui/icons-material/LockOutline';
import CardsRoll from './components/CardsRoll';

import { parties_card } from './data/parties_cards';
import GameSession from '../types/GameSession';
import groupedParties, { DaySessions, WeekSessions } from './data/grouped_parties';

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

export function formatDateToFrench(date: Date | string | number): string {
  if (typeof(date) == 'string') {
    date = Date.parse(date)
  } 
  const options: Intl.DateTimeFormatOptions = {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
  };
  const formatted = new Intl.DateTimeFormat('fr-FR', options).format(date);
  return formatted
    .split(' ')
    .map(word => word.charAt(0) + word.slice(1))
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

export default function PartiesCard(parties:GameSession[]) {
  const sessions = groupByDate(Object.values(parties));
  
  return (
    <Box sx={{ width: '100%'}}>

      {/* Section dédier au proposition */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom:0, mx:2 }}>
          Elles pourraient vous intérrésser
        </Typography>

        <Box sx={{overflow:'auto', width: '100%',  display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
        {
          parties_card.map((data) => (
            <CardsRoll key={data.id} title={data.title} sessions={data.parties as GameSession[]} displayDate />
          ))
        }
        </Box>
        <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem  />
      </Box>

      {/* Section semaine prochaine */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom:0, mx: 2 }}>
          La semaine prochaine
        </Typography>

        <Box sx={{ overflow:'auto', width: '100%', display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
          <CardsRoll title='Vendredi au FSV de 14h à 22h' sessions={sessions['2025-05-16']}/>
          <CardsRoll title='Samedi au FSV de 14h à 22h' sessions={sessions['2025-05-17']}/>
        </Box>
        <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem  />
      </Box>


      {groupedParties.map((week:WeekSessions) => (
        <Box key={week.titre}>
          <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom:0, mx: 2 }}>
            {week.titre}
          </Typography>
          {/* <Typography variant="subtitle1" sx={{ mb: 2, marginTop:0, paddingLeft:'1em' }}>
              au FSV de 14h à 22h
          </Typography> */}

          <Box sx={{ 
            overflow:'auto', width: '100%', 
            display:'inline-flex',  
            flexWrap:'nowrap', 
            gap:'1em',
            alignItems:'left',
            }} className={'className'}>
            {week.jours.map((jour:DaySessions[]) => ( 
              <CardsRoll key={jour.date} title={formatDateToFrench(Date.parse(jour.date))} sessions={jour.sessions}/>
            ))}
          </Box>
          <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem  />
        </Box>
      ))}


    </Box>
  ); 
  
}
