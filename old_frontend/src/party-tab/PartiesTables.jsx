import Box from '@mui/material/Box';
import Divider from '@mui/material/Divider';
import Typography from '@mui/material/Typography';
import { styled } from '@mui/material/styles';
import TableCell, { tableCellClasses } from '@mui/material/TableCell';
import TableRow from '@mui/material/TableRow';

import { isMobile } from '../config';
import PartyTable from './components/PartyTable';
import PartyAccordillonTable from './components/PartyAccordillonTable'
import { useState } from 'react';
import { formatDateToFrench } from './PartiesCards';
import { useTheme } from '@emotion/react';
import { useMediaQuery } from '@mui/material';

const StyledTableCell = styled(TableCell)(({ theme }) => ({
  [`&.${tableCellClasses.head}`]: {
    backgroundColor: theme.palette.common.black,
    color: theme.palette.common.white,
  },
  [`&.${tableCellClasses.body}`]: {
    fontSize: 14,
  },
}));

const StyledTableRow = styled(TableRow)(({ theme }) => ({
  '&:nth-of-type(odd)': {
    backgroundColor: theme.palette.action.hover,
  },
  // hide last border
  '&:last-child td, &:last-child th': {
    border: 0,
  },
}));

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

function groupSessionsByDay(sessionsList) {
  const grouped = {};
  sessionsList.forEach((session) => {
    const date = session.date_session;
    if (!grouped[date]) {
      grouped[date] = [];
    }
    grouped[date].push(session);
  });
  // Retourne un tableau [{ date, sessions }]
  return Object.entries(grouped).map(([date, sessions]) => ({
    date: new Date(date),
    sessions,
  }));
}

export default function PartiesTables({sessionsList, setSessionsList}) {
    const groupedSessions = groupSessionsByDay(sessionsList)
    const [expanded, setExpanded] = useState(false);
    const theme = useTheme();
    const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));

    return(
      <Box sx={{ width: '100%', px: 1}}>
        {groupedSessions.map(({date, sessions}) => (
          // TODO Mettre un meilleur ID {formatDateToFrench(date)} a
          <Box sx={{ width: '100%', paddingBottom: '1em'}} key={'session-'+date}>
            <Typography variant="h4" component="h1" sx={{ mb: 2 }}>
                Partie du {formatDateToFrench(date)}
            </Typography>
            {isMobileScreen ? <PartyAccordillonTable 
                            key={date}
                            sessions={sessions}
                            expanded={expanded} 
                            setExpanded={setExpanded}
                            /> : <PartyTable sessionsList={sessions} key={date}/> }
            <Divider sx={{marginTop: '2em'}} orientation="horizontal" variant="middle" flexItem  />
          </Box>
        ))} 
      </Box>
    )
  }