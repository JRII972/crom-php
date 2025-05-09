import * as React from 'react';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CardMedia from '@mui/material/CardMedia';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import CardActionArea from '@mui/material/CardActionArea';
import CardActions from '@mui/material/CardActions';
import { Box, Divider, Grid, Skeleton } from '@mui/material';
import { esES } from '@mui/material/locale';
import LockOutlineIcon from '@mui/icons-material/LockOutline';
import PartyCard from './components/PartyCard';
import CardsRolls from './components/CardsRolls';

import {parties_card} from './data/parties_cards';
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

export default function PartiesCard(parties:GameSession[]) {
  const sessions = groupByDate(Object.values(parties));
  
  return (
    <Box sx={{ width: '100%'}}>

      {/* Section dédier au proposition */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom:0, mx:2 }}>
          Qui pourrais vous intérrésser
        </Typography>

        <Box sx={{overflow:'auto', width: '100%',  display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
        {
          parties_card.map((data) => (
            <CardsRolls key={data.id} title={data.title} sessions={data.parties as GameSession[]} />
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
          <CardsRolls title='Vendredi au FSV de 14h à 22h' sessions={sessions['2025-05-16']}/>
          <CardsRolls title='Samedi au FSV de 14h à 22h' sessions={sessions['2025-05-17']}/>
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
              <CardsRolls key={jour.date} title={formatDateToFrench(Date.parse(jour.date))} sessions={jour.sessions}/>
            ))}
          </Box>
          <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem  />
        </Box>
      ))}


    </Box>
  ); 
  
}
