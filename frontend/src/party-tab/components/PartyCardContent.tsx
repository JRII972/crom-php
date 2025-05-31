import { FC } from 'react';
import {
  CardContent,
  Typography, useMediaQuery,
  useTheme,
  Stack,
  Grid
} from '@mui/material';
import { Session } from '../../api/types/db';
import { playerNumber, trimString } from '../../utils/utils';
import PlayersDisplay from './PlayersDisplay';
import { TypePartie } from '../../utils/utils';

interface PartyCardContentProps { 
  session: Session;
  cardMinWidth: number;
  displayDate: boolean;
  type?: 'session' | 'game' | 'party';
}

export const PartyCardContent: FC<PartyCardContentProps> = ({ 
  session, 
  cardMinWidth, 
  type='session', 
  displayDate=false 
}) => {
  const theme = useTheme();
  const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));

  const titleSize = isMobileScreen ? '1em' : '1.15em';
  const subTitleSize = isMobileScreen ? '0.8em' : '0.9em';
  const subTitleLineHeight = isMobileScreen ? '0.8em' : '0.9em';
  const commentSize = isMobileScreen ? '0.75em' : '0.85em';

  const isSession = (type === 'session');
  const isGame = (type === 'game');

  const maitreDuJeu = session.partie.maitre_jeu.pseudonyme || 
    `${session.partie.maitre_jeu.prenom} ${session.partie.maitre_jeu.nom}`;
  
  // Options de formatage de date
  const dateFormatingOption = maitreDuJeu.length < 11 ? {
    weekday: 'short',
    day: 'numeric',
    month: 'numeric',
  } : { day: 'numeric', month: 'numeric' };
  
  const partieDate = new Date(Date.parse(session.date_session)).toLocaleDateString('fr-FR', dateFormatingOption);

  // Détermine les joueurs inscrits pour l'affichage
  const playersDisplay = session.joueurs ? session.joueurs.map(joueur => ({
    nom: joueur.pseudonyme || `${joueur.prenom} ${joueur.nom}`,
    avatar: joueur.avatar || ''
  })) : [];
  
  return (
    <CardContent
      sx={{
        backgroundColor: 'background.card',
        p: 1,
        flex: 1,
        width: '100%',
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'space-between',
      }}
    >
      <Stack spacing={0.2}>
        <Stack>
          <Typography 
            variant="h5" 
            component="div" 
            sx={{
              textalign: 'center',
              alignItems: 'center',
              fontSize: titleSize,
              whiteSpace: 'nowrap',
              textAlign: 'center'
            }}
          >
            {session.partie.nom}
          </Typography>
          
          {isSession && (
            <>
              <Stack 
                direction="column" 
                spacing={0.5} 
                sx={{ alignItems: 'center', justifyContent: 'space-between' }}
              >
                <Typography 
                  gutterBottom 
                  variant="subtitle1" 
                  component="div" 
                  sx={{
                    fontSize: subTitleSize,
                    lineHeight: subTitleLineHeight,
                    alignItems: 'center',
                  }}
                >
                  {session.partie.jeu.nom || session.partie.jeu.icon}
                </Typography>
                <Typography 
                  gutterBottom 
                  variant="subtitle1" 
                  component="div" 
                  sx={{
                    fontSize: subTitleSize,
                    lineHeight: subTitleLineHeight,
                    alignItems: 'center',
                  }}
                >
                  {!displayDate && maitreDuJeu}
                  {displayDate && `${trimString(maitreDuJeu, 17, 16, '.')} | ${partieDate}`}
                </Typography>
              </Stack>

              <Grid container spacing={1} sx={{ mt: 0.7 }}>
                <Grid item xs={4}
                  sx={{
                    display: 'flex',
                    justifyContent: 'left',
                    alignItems: 'center',
                  }}
                >
                  <Typography 
                    variant="subtitle1" 
                    component="div" 
                    sx={{
                      fontSize: subTitleSize,
                      lineHeight: subTitleLineHeight,
                      alignItems: 'center',
                      textTranforme: 'capitalize'
                    }}
                  >
                    {TypePartie(session.partie.type_partie)}
                  </Typography>
                </Grid>

                <Grid item xs={4}                  
                  sx={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                  }}
                >
                  <Typography 
                    variant="subtitle1" 
                    component="div" 
                    sx={{
                      fontSize: subTitleSize,
                      lineHeight: subTitleLineHeight,
                      alignItems: 'center',
                    }}
                  >
                    {playerNumber(session, '', true)}
                  </Typography>
                </Grid>

                <Grid item xs={4} 
                  sx={{
                    display: 'flex',
                    justifyContent: 'right',
                    alignItems: 'center',
                  }}
                >
                  <Typography 
                    variant="subtitle1" 
                    component="div" 
                    sx={{
                      fontSize: subTitleSize,
                      lineHeight: subTitleLineHeight,
                      alignItems: 'center',
                    }}
                  >
                    {session.lieu?.nom || ''}
                  </Typography>                  
                </Grid>
              </Grid>
            </>
          )}

          {isGame && (
            <Stack 
              direction="column" 
              spacing={0.5} 
              sx={{ alignItems: 'center', justifyContent: 'space-between', my: 0.5 }}
            >
              <Typography 
                variant="subtitle1" 
                component="div" 
                sx={{
                  fontSize: subTitleSize,
                  lineHeight: subTitleLineHeight,
                  alignItems: 'center',
                }}
              >
                {session.partie.jeu.nom}
              </Typography>
              <Typography 
                variant="subtitle1" 
                component="div" 
                sx={{
                  fontSize: subTitleSize,
                  lineHeight: subTitleLineHeight,
                  alignItems: 'center',
                }}
              >
                {playerNumber(session, '', true)}
              </Typography>
            </Stack>
          )}
        </Stack>
        
        {type !== 'game' && (
          <Typography
            variant="body2"
            sx={{
              color: 'text.secondary',
              display: '-webkit-box',
              WebkitBoxOrient: 'vertical',
              WebkitLineClamp: 3,
              overflow: 'hidden',
              textOverflow: 'ellipsis',
              fontSize: commentSize,
            }}
          >
            {session.partie.description}
          </Typography>
        )}
      </Stack>

      <PlayersDisplay
        players={playersDisplay}
        maxWidth={cardMinWidth - 20}
        spaceWidth={5}
        separator=", "
        fontSize={commentSize}
      />
    </CardContent>
  );
};