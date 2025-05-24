import Typography from '@mui/material/Typography';
import { Box, Divider, Stack, ToggleButton, ToggleButtonGroup } from '@mui/material';
import LockOutlineIcon from '@mui/icons-material/LockOutline';
import CardsRoll, { PartyGameCardsRoll } from './components/CardsRoll';
import { vos_partie, les_parties } from './data/parties_cards';
import GameSession from '../types/GameSession';
import groupedParties from './data/grouped_parties';
import { parties } from './data/parties';
import { useState } from 'react';

import { games } from './data/games'

import { groupByDate, groupByWeek, groupByMonth, groupByLocation, groupByType, groupByCategorie } from './utils/filter'
import GameCard from './components/GameCard';

function formatDateToFrench(date: Date | number): string {
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

function formatWeekToFrench(weekStart: Date, sessions : GameSession[]): string {
  const shortenData = sessions.length < 2
  const options: Intl.DateTimeFormatOptions = {
    day: 'numeric',
    month: shortenData ? 'short' : 'long',
  };
  const dates = sessions.map(s => new Date(s.date).getDay());
  const isWeekend = dates.every(day => day === 5 || day === 6 || day === 0); // Friday, Saturday, Sunday
  if (isWeekend) {
    const friday = new Date(weekStart);
    friday.setDate(weekStart.getDate() + 5); // Friday
    const sunday = new Date(weekStart);
    sunday.setDate(weekStart.getDate() + 7); // Sunday
    const start = new Intl.DateTimeFormat('fr-FR', options).format(friday);
    const end = new Intl.DateTimeFormat('fr-FR', options).format(sunday);
    return `${shortenData ? 'W.' : 'Weekend'} du ${start} au ${end}`;
  } else {
    const start = new Intl.DateTimeFormat('fr-FR', options).format(weekStart);
    const end = new Intl.DateTimeFormat('fr-FR', options).format(new Date(weekStart.getTime() + 6 * 24 * 60 * 60 * 1000));
    return `${shortenData ? 'Sem.' : 'Semaine'} du ${start} au ${end}`;
  }
}

function formatMonthToFrench(month: number, year: number): string {
  const date = new Date(year, month);
  const options: Intl.DateTimeFormatOptions = { month: 'long', year: 'numeric' };
  const formatted = new Intl.DateTimeFormat('fr-FR', options).format(date);
  return formatted.charAt(0).toUpperCase() + formatted.slice(1);
}


function playerNumber(partie: GameSession) {
  if (partie.locked) {
    return <LockOutlineIcon fontSize="small" />;
  } else {
    return (
      <Typography gutterBottom variant="subtitle2" component="div">
        {partie.number_of_players_registered}/{partie.max_player} joueurs
      </Typography>
    );
  }
}

export default function PartiesPage() {
  const sessions = groupByDate(Object.values(parties));
  const allDays = groupedParties.flatMap((week) => week.jours);
  const [personalizedList, setPersonalizedList] = useState<'presonalized' | 'week' | 'type' | 'categorie'>(localStorage.getItem( 'PersonalizedListMode' ) || 'presonalized');
  const [allGamesGroupMode, setAllGamesGroupMode] = useState<'categorie' | 'alphabetique'>(localStorage.getItem( 'GamesGroupMode' ) || 'alphabetique');

  const handlePersonalizedListChange = (
    event: React.MouseEvent<HTMLElement>,
    newMode: 'presonalized' | 'week' | 'type' | 'categorie' | null,
  ) => {
    if (newMode !== null) {
      setPersonalizedList(newMode);
      localStorage.setItem( 'PersonalizedListMode', newMode );
    }
  };

  const handleAllGamesGroupModeChange = (
    event: React.MouseEvent<HTMLElement>,
    newMode: 'categorie' | 'alphabetique' | null,
  ) => {
    if (newMode !== null) {
      setAllGamesGroupMode(newMode);
      localStorage.setItem( 'GamesGroupMode', newMode );
    }
  };

  // Prepare grouped data for next sessions
  const personalizedSessionsList = vos_partie[1]?.parties as GameSession[] || [];

  // Prepare grouped data for all parties
  const allGameGrouped = allGamesGroupMode === 'alphabetique'
    ? games.sort((a,b) => (a.nom > b.nom) ? 1 : ((b.nom > a.nom) ? -1 : 0))
    : games;

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
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom: 0 }}>
          Qui pourrais vous intérrésser
        </Typography>
        
        <ToggleButtonGroup
          value={personalizedList}
          exclusive
          onChange={handlePersonalizedListChange}
          aria-label="next sessions grouping mode"
          sx={{ mb: 2 }}
        >
          <ToggleButton value="presonalized" aria-label="group by presonalization">
            Par Préférence
          </ToggleButton>
          <ToggleButton value="week" aria-label="group by week">
            Par semaine
          </ToggleButton>
          <ToggleButton value="type" aria-label="group by type">
            Par Type
          </ToggleButton>
          <ToggleButton value="categorie" aria-label="group by categorie">
            Par Catégorie
          </ToggleButton>
        </ToggleButtonGroup>
        
        <Box sx={{overflow:'auto', width: '100%',  display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
          {personalizedList === 'week' ? (
            Object.entries(groupByWeek(personalizedSessionsList)).map(([key, group]) => (              
              <CardsRoll
                key={key}
                title={formatWeekToFrench(group.weekStart, group.sessions)}
                sessions={group.sessions}
              />
            ))
          ) : personalizedList === 'type' ? (
            Object.entries(groupByType(personalizedSessionsList)).map(([key, group]) => (
              <CardsRoll
                key={key}
                title={`Les ${group.type}`}
                sessions={group.sessions}
              />
            ))
          ) : personalizedList === 'categorie' ? (
            Object.entries(groupByCategorie(personalizedSessionsList)).map(([key, group]) => (
              <CardsRoll
                key={key}
                title={`Les ${group.type}`}
                sessions={group.sessions}
              />
            ))
          ) : Object.entries(groupByCategorie(personalizedSessionsList)).map(([key, group]) => (
              <CardsRoll
                key={key}
                title={`Sessions à ${group.location}`}
                sessions={group.sessions}
              />
            ))}
        </Box>
        
        <Divider sx={{ margin: '2em' }} orientation="horizontal" variant="middle" flexItem />
      </Box>

      {/* Section dédier au proposition */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom: 0 }}>
          Les parties ouverte
        </Typography>
        <Box>
          {Object.entries(les_parties).map(([key, group]) => (
            <Box key={key} sx={{overflow:'auto', width: '100%',  display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
                <PartyGameCardsRoll title={group.type} sessions={group.sessions} />
            </Box>
          ))}
        </Box>
        <Divider sx={{ margin: '2em' }} orientation="horizontal" variant="middle" flexItem />
      </Box>

      {/* Section toutes les parties avec filtre */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom: 0 }}>
          Les différents jeux
        </Typography>
        <ToggleButtonGroup
          value={allGamesGroupMode}
          exclusive
          onChange={handleAllGamesGroupModeChange}
          aria-label="all game grouping mode"
          sx={{ mb: 2 }}
        >
          <ToggleButton value="categorie" aria-label="group by categorie">
            Par mois
          </ToggleButton>
          <ToggleButton value="alphabetique" aria-label="group by Alphabétique">
            Par semaine
          </ToggleButton>
        </ToggleButtonGroup>
        <Box>
          {allGamesGroupMode === 'categorie' ? (
            Object.entries(allGameGrouped).map(([key, group]) => (
              <Box key={key}>
                {/* <Typography variant="h5" component="h2" sx={{ mb: 2, marginBottom: 0 }}>
                  {formatMonthToFrench(group.categorie, group.year)}
                </Typography>

                <Box sx={{overflow:'auto', width: '100%',  display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
                {Object.entries(groupByWeek(group.sessions)).map(([key, group]) => (
                  <CardsRoll
                    key={key}
                    title={formatWeekToFrench(group.weekStart, group.sessions)}
                    sessions={group.sessions}
                  />
                ))}
                </Box>
                <Divider orientation="vertical" flexItem /> */}
              </Box>
            ))
          ) : (
            <Box sx={{overflow:'auto', width: '100%',  display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
              
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
                    Jeu
                  </Typography>
                </Box>
                <Box sx={{ display:'inline-flex', gap:'1em' }}>
                  {allGameGrouped.map((game) => ( 
                    <GameCard game={game} key={game.nom}/>
                    ))}
                </Box>
              </Stack>
            </Box>
          )}
        </Box>
      </Box>
    </Stack>
  );
}