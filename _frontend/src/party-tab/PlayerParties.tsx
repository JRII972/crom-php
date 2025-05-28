import Typography from '@mui/material/Typography';
import { Box, Divider, Stack, ToggleButton, ToggleButtonGroup } from '@mui/material';
import LockOutlineIcon from '@mui/icons-material/LockOutline';
import CardsRoll, { PartyGameCardsRoll } from './components/CardsRoll';
import { vos_partie } from './data/parties_cards';
import GameSession from '../types/GameSession';
import groupedParties from './data/grouped_parties';
import { parties } from './data/parties';
import { useState } from 'react';

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

// Function to group by week
const groupByWeek = (sessions: GameSession[]) => {
  return sessions.reduce((result, session) => {
    const date = new Date(session.date);
    const year = date.getFullYear();
    const week = Math.floor((date.getTime() - new Date(year, 0, 1).getTime()) / (7 * 24 * 60 * 60 * 1000));
    const key = `${year}-W${week}`;
    if (!result[key]) {
      const weekStart = new Date(date);
      weekStart.setDate(date.getDate() - date.getDay());
      result[key] = { weekStart, sessions: [] };
    }
    result[key].sessions.push(session);
    return result;
  }, {});
};

// Function to group by month
const groupByMonth = (sessions: GameSession[]) => {
  return sessions.reduce((result, session) => {
    const date = new Date(session.date);
    const key = `${date.getFullYear()}-${date.getMonth() + 1}`;
    if (!result[key]) {
      result[key] = { month: date.getMonth(), year: date.getFullYear(), sessions: [] };
    }
    result[key].sessions.push(session);
    return result;
  }, {});
};

// Function to group by location
const groupByLocation = (sessions: GameSession[]) => {
  return sessions.reduce((result, session) => {
    const location = session.lieu || 'Unknown';
    if (!result[location]) {
      result[location] = { location, sessions: [] };
    }
    result[location].sessions.push(session);
    return result;
  }, {});
};

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

export default function PlayerParties() {
  const sessions = groupByDate(Object.values(parties));
  const allDays = groupedParties.flatMap((week) => week.jours);
  const [nextSessionsGroupMode, setNextSessionsGroupMode] = useState<'week' | 'location'>(localStorage.getItem( 'SessionsGroupMode' ) || 'week');
  const [allPartiesGroupMode, setAllPartiesGroupMode] = useState<'month' | 'week' | 'location'>(localStorage.getItem( 'PartiesGroupMode' ) || 'month');

  const handleNextSessionsGroupModeChange = (
    event: React.MouseEvent<HTMLElement>,
    newMode: 'week' | 'location' | null,
  ) => {
    if (newMode !== null) {
      setNextSessionsGroupMode(newMode);
      localStorage.setItem( 'SessionsGroupMode', newMode );
    }
  };

  const handleAllPartiesGroupModeChange = (
    event: React.MouseEvent<HTMLElement>,
    newMode: 'month' | 'week' | 'location' | null,
  ) => {
    if (newMode !== null) {
      setAllPartiesGroupMode(newMode);
      localStorage.setItem( 'PartiesGroupMode', newMode );
    }
  };

  // Prepare grouped data for next sessions
  const nextSessions = vos_partie[1]?.parties as GameSession[] || [];
  const nextSessionsGroupedByWeek = groupByWeek(nextSessions);
  const nextSessionsGroupedByLocation = groupByLocation(nextSessions);

  // Prepare grouped data for all parties
  const allPartiesGrouped = allPartiesGroupMode === 'month'
    ? groupByMonth(allDays.flatMap(day => day.sessions))
    : allPartiesGroupMode === 'week'
    ? groupByWeek(allDays.flatMap(day => day.sessions))
    : groupByLocation(allDays.flatMap(day => day.sessions));

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
          Vos prochaines sessions
        </Typography>
        <ToggleButtonGroup
          value={nextSessionsGroupMode}
          exclusive
          onChange={handleNextSessionsGroupModeChange}
          aria-label="next sessions grouping mode"
          sx={{ mb: 2 }}
        >
          <ToggleButton value="week" aria-label="group by week">
            Par semaine
          </ToggleButton>
          <ToggleButton value="location" aria-label="group by location">
            Par lieux
          </ToggleButton>
        </ToggleButtonGroup>
        
        <Box sx={{overflow:'auto', width: '100%',  display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
          {nextSessionsGroupMode === 'week' ? (
            Object.entries(nextSessionsGroupedByWeek).map(([key, group]) => (              
              <CardsRoll
                key={key}
                title={formatWeekToFrench(group.weekStart, group.sessions)}
                sessions={group.sessions}
              />
            ))
          ) : (
            Object.entries(nextSessionsGroupedByLocation).map(([key, group]) => (
              <CardsRoll
                title={`Sessions à ${group.location}`}
                sessions={group.sessions}
              />
            ))
          )}
        </Box>
        
        <Divider sx={{ margin: '2em' }} orientation="horizontal" variant="middle" flexItem />
      </Box>

      {/* Section dédier au proposition */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom: 0 }}>
          Vos parties
        </Typography>
        <Box>
            {vos_partie.map((data) => (
          <Box key={data.id} sx={{overflow:'auto', width: '100%',  display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
              <PartyGameCardsRoll key={data.id} title={data.title} sessions={data.parties as GameSession[]} />
          </Box>
          ))}
        </Box>
        <Divider sx={{ margin: '2em' }} orientation="horizontal" variant="middle" flexItem />
      </Box>

      {/* Section toutes les parties avec filtre */}
      <Box>
        <Typography variant="h4" component="h1" sx={{ mb: 2, marginBottom: 0 }}>
          Toutes vos prochaines parties
        </Typography>
        <ToggleButtonGroup
          value={allPartiesGroupMode}
          exclusive
          onChange={handleAllPartiesGroupModeChange}
          aria-label="all parties grouping mode"
          sx={{ mb: 2 }}
        >
          <ToggleButton value="month" aria-label="group by month">
            Par mois
          </ToggleButton>
          <ToggleButton value="week" aria-label="group by week">
            Par semaine
          </ToggleButton>
          <ToggleButton value="location" aria-label="group by location">
            Par lieux
          </ToggleButton>
        </ToggleButtonGroup>
        <Box>
          {allPartiesGroupMode === 'month' ? (
            Object.entries(allPartiesGrouped).map(([key, group]) => (
              <Box key={key}>
                <Typography variant="h5" component="h2" sx={{ mb: 2, marginBottom: 0 }}>
                  {formatMonthToFrench(group.month, group.year)}
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
                <Divider orientation="vertical" flexItem />
              </Box>
            ))
          ) : allPartiesGroupMode === 'week' ? (
            Object.entries(allPartiesGrouped).map(([key, group]) => (
              <Box sx={{overflow:'auto', width: '100%',  display:'inline-flex',  flexWrap:'nowrap', gap:'1em'}} className={'className'}>
                <CardsRoll
                  title={formatWeekToFrench(group.weekStart, group.sessions)}
                  sessions={group.sessions}
                />
                <Divider flexItem />
              </Box>
            ))
          ) : (
            Object.entries(allPartiesGrouped).map(([key, group]) => (
              <Box key={key}>
                <Typography variant="h5" component="h2" sx={{ mb: 2, marginBottom: 0 }}>
                  {key}
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
                <Divider flexItem />
              </Box>
            ))
          )}
        </Box>
      </Box>
    </Stack>
  );
}