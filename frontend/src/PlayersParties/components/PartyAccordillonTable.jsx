import { useRef, useEffect } from 'react';

import Box from '@mui/material/Box';
import Table from '@mui/material/Table';
import Paper from '@mui/material/Paper';
import { styled } from '@mui/material/styles';
import TableRow from '@mui/material/TableRow';
import TableBody from '@mui/material/TableBody';
import TableHead from '@mui/material/TableHead';
import { GridExpandMoreIcon } from '@mui/x-data-grid';
import TableContainer from '@mui/material/TableContainer';
import LockOutlineIcon from '@mui/icons-material/LockOutline';
import TableCell, { tableCellClasses } from '@mui/material/TableCell';
import { Accordion, AccordionDetails, AccordionSummary } from '@mui/material';

import PlayerTable from './PlayersTable';


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



export default function PartyAccordillonTable({party, expanded, setExpanded}) {
  const accordionRefs = useRef({});

  useEffect(() => {
    if (expanded && accordionRefs.current[expanded]) {
      accordionRefs.current[expanded].scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
        inline: 'nearest',
      });
    }
  }, [expanded]);

  const handleAccordionChange =
    (panel) => (event, isExpanded) => {
      setExpanded(isExpanded ? panel : false);
    };

  

  return(
      <Box className="TableAccordillon" sx={{ width: "100%" }} key={party.date + '-' + party.lieu}>
        <TableContainer component={Paper} key={'TableContainer-' + party.date + '-' + party.lieu}>
        <Table stickyHeader  key={'table-' + party.date + '-' + party.lieu}>
          <TableHead > 
            <TableRow >
            <TableCell sx={{ width: "20%", padding: '0.7em', paddingInline: '0.5em' , border:'none' }}>MJ</TableCell>
            <TableCell sx={{ width: "35%", padding: '0.7em', paddingInline: '0.5em'  }}>Jeu</TableCell>
            <TableCell sx={{ width: "15%", padding: '0.7em', paddingInline: '0.5em'  }}>Type</TableCell>
            <TableCell sx={{ width: "15%", padding: '0.7em', paddingInline: '0.5em'  }}>Lieu</TableCell>
            <TableCell sx={{ width: "10%", padding: '0.7em', paddingInline: '0.5em'  }}>Nombre de joueurs</TableCell>
            <TableCell sx={{ width: "15%", padding: '0.7em', paddingInline: '0.5em'  }}></TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
          {party.map((row) => {
            const panelId = 'panel' + row.id;
            return (
            <TableRow key={'row-' + party.key}>
              <TableCell colSpan="6" sx={{ padding: 0 }}>
                <Accordion
                  name="Pending (click me to collapse)"
                  sx={{ padding: 0 }}
                  expanded={expanded === panelId} onChange={handleAccordionChange(panelId)}
                  ref={(el) => {
                    if (el) accordionRefs.current[panelId] = el;
                  }}
                >
                  <AccordionSummary
                    expandIcon={<GridExpandMoreIcon />}
                    aria-controls="panel1a-content"
                    id="panel1a-header"
                    sx={{ padding: 0 }}
                    key={'accordion-' + party.key}
                  >
                    <Table>
                        <TableRow>
                          <TableCell sx={{ width: "20%", padding: '0.7em', paddingInline: '0.5em'  }}>{row.maitre_de_jeu}</TableCell>
                          <TableCell sx={{ width: "35%", padding: '0.7em', paddingInline: '0.5em'  }}>{row.jeu}</TableCell>
                          <TableCell sx={{ width: "15%", padding: '0.7em', paddingInline: '0.5em'  }}>{row.type}</TableCell>
                          <TableCell sx={{ width: "10%", padding: '0.7em', paddingInline: '0.5em'  }}>{row.lieu}</TableCell>
                          <TableCell sx={{ width: "15%", padding: '0.7em', paddingInline: '0.5em'  }}>{row.locked ? <LockOutlineIcon /> : row.max_player}</TableCell>
                        </TableRow>
                      </Table>
                  </AccordionSummary>
                  <AccordionDetails sx={{ maxWidth: "80vw"}}>
                    <Box sx={{ margin: '1em', fontStyle: 'italic'}}>
                      {row.coment}
                    </Box>
                    <PlayerTable row={row} />
                  </AccordionDetails>
                </Accordion>
              </TableCell>
            </TableRow>
          )})}
          </TableBody>
        </Table>
      </TableContainer>
      </Box>
    )
  }