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

export default function PartiesTables(parties) {
    const sessions = groupByDate(Object.values(parties));
    const [expanded, setExpanded] = useState(false);

    return(
      <div>
      {Object.keys(sessions).map((date) => (
        // TODO Mettre un meilleur ID 
        <Box sx={{ width: '100%', paddingBottom: '1em'}} key={'session-'+date}>
        <Typography variant="h4" component="h1" sx={{ mb: 2 }}>
            Partie du {date}
        </Typography>
        {isMobile() ? <PartyAccordillonTable 
                        party={sessions[date]}
                        expanded={expanded} 
                        setExpanded={setExpanded}
                         /> : <PartyTable party={sessions[date]}/> }
        <Divider sx={{marginTop: '2em'}} orientation="horizontal" variant="middle" flexItem  />
        </Box>
        ))}
      </div>
      )
  }