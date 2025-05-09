import * as React from 'react';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CardMedia from '@mui/material/CardMedia';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import CardActionArea from '@mui/material/CardActionArea';
import CardActions from '@mui/material/CardActions';
import { Box, Divider, Grid, Skeleton, Stack } from '@mui/material';
import { esES } from '@mui/material/locale';
import LockOutlineIcon from '@mui/icons-material/LockOutline';
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


export default function CardsRolls({ title, sessions }: { title: string; sessions: GameSession[] }): React.ReactElement {
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
          <PartyCard partie={partie} key={partie.id}/>
          ))}
      </Box>
    </Stack>
  );
}
