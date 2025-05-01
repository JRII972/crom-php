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

interface GameSession {
  id: number;              // "101" is a string, not a number
  date: Date;            // "04/04/2025" is a string (you could use Date if parsed)
  maitre_de_jeu: string;   // "Julien"
  jeu: string;             // "Warhammer"
  type: string;            // "Cmp"
  lieu: string;            // "FSV"
  commentaire: string;     // Long text description
  number_of_players_registered: number;
  max_player: number;             // "5" is a string (could be number if parsed)
  players: string[];       // Array of player names
  locked: boolean;         // true or false
  image: string;
  image_alt: string;
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
    {Object.keys(sessions).map((date) => (
    <Box >
    <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom:0 }}>
        Partie du {formatDateToFrench(Date.parse(date))}
    </Typography>
    <Typography variant="subtitle1" sx={{ mb: 2, marginTop:0, paddingLeft:'1em' }}>
        au FSV de 14h à 22h
    </Typography>

    <Grid container spacing={2} sx={{ width: '100%', paddingBottom: '1em'}}>
        
        
        {sessions[date].map((partie:GameSession) => ( 
          <Grid>
              <Card sx={{ maxWidth: 300, height:400 }}>
              <CardActionArea>
                <CardMedia
                    component="img"
                    height="140"
                    image={partie.image}
                    alt={partie.image_alt}
                  />
                {/* {loading ? ( TODO: add falback when no image can be loaded
                    <Skeleton sx={{ height: 140 }} animation="wave" variant="rectangular" />
                  ) : (
                    <CardMedia
                      component="img"
                      height="140"
                      image={partie.image}
                      alt={partie.image_alt}
                    />
                  )} */}
                <CardContent>
                  <Typography gutterBottom variant="h5" component="div">
                    {partie.jeu}
                  </Typography>
                  <Box sx={{ display: 'inline-flex', aligntems: 'center'}}>
                    <Typography gutterBottom variant="subtitle2" component="div">
                      {partie.maitre_de_jeu}
                    </Typography>
                    <Divider orientation='vertical' variant='middle' flexItem sx={{ marginInline: '0.5em'}}/>
                    <Typography gutterBottom variant="subtitle2" component="div">
                      {partie.type}  
                    </Typography>
                    <Divider orientation='vertical' variant='middle' flexItem sx={{ marginInline: '0.5em'}}/>
                    <Typography gutterBottom variant="subtitle2" component="div">
                      {partie.lieu}  
                    </Typography>
                    <Divider orientation='vertical' variant='middle' flexItem sx={{ marginInline: '0.5em'}}/>
                    {playerNumber(partie)}
                  </Box>
                  <Typography variant="body2" sx={{ 
                    color: 'text.secondary', 
                    display: '-webkit-box',
                    WebkitBoxOrient: 'vertical',
                    WebkitLineClamp: 6, // Limit to 2 lines
                    overflow: 'hidden',
                    textOverflow: 'ellipsis'}} 
                    component={'p'}>
                      {partie.commentaire} ddddddddddddddddddd
                      ddddddddddddddddddddddddddddd7
                      dddddddddddddddddddddd
                      dddddddddddd
                      dddddddddddddddddddddddd
                  </Typography>
                  <Box sx={{ display: 'inline-flex', flexWrap:'wrap'}}>
                    {partie.players.map((joueur) => (
                      <Box >
                        <Typography variant="overline" gutterBottom sx={{ width: "max-content", paddingInline:'0.25em', margin:0, height:'1.5em', display:'block' }}>
                        {joueur} 
                      </Typography>
                      </Box>
                    ))}
                  </Box>
                </CardContent>
              </CardActionArea>
              {/* <CardActions>
                <Button size="small" color="primary">
                  Share
                </Button>
              </CardActions> */}
            </Card>
            </Grid>
            ))}
            
        
    </Grid>
    <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem  />
    </Box>
    ))}
    </Box>
  );
}
