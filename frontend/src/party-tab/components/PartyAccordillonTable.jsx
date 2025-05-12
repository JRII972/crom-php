import React, { useRef, useEffect, useCallback } from 'react';

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
import { Accordion, AccordionDetails, AccordionSummary, Stack } from '@mui/material';

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

const PartyRow = React.memo(({ row, expanded, handleAccordionChange, accordionRefs }) => {
  const panelId = 'panel' + row.id;
  if (row.id === undefined) {
    console.error('row.id is undefined', row);
    return null;
  }
  return (
    <TableRow key={'row-' + row.id}>
      <TableCell colSpan="6" sx={{ padding: 0 }}>
        <Accordion
          name="Pending (click me to collapse)"
          sx={{ padding: 0 }}
          expanded={expanded === panelId}
          onChange={handleAccordionChange(panelId)}
          ref={(el) => {
            if (el) accordionRefs.current[panelId] = el;
          }}
        >
          <AccordionSummary
            expandIcon={<GridExpandMoreIcon />}
            aria-controls="panel1a-content"
            id="panel1a-header"
            sx={{ padding: 0 }}
            key={'accordion-' + row.id}
          >
            <Table>
              <TableBody>
                <TableRow>
                  <TableCell sx={{ width: "20%", padding: '0.7em', paddingInline: '0.5em' }}>{row.maitre_de_jeu}</TableCell>
                  <TableCell sx={{ width: "36%", padding: '0.7em', paddingInline: '0.5em' }}>
                    <Stack direction='column' spacing={0} sx={{ width: '100%' }}>
                      <span>{row.jeu}</span>
                      <span style={{ fontSize: '0.8em', color: 'gray' }}>{row.jeu}</span>
                    </Stack>
                  </TableCell>
                  <TableCell sx={{ width: "10%", padding: '0.7em', paddingInline: '0.5em' }} align='center'>{row.type}</TableCell>
                  <TableCell sx={{ width: "10%", padding: '0.7em', paddingInline: '0.5em' }} align='center'>{row.lieu}</TableCell>
                  <TableCell sx={{ width: "20%", padding: '0.7em', paddingInline: '0.5em' }} align='center'>
                    {row.locked ? <LockOutlineIcon /> : row.max_player}
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </AccordionSummary>
          <AccordionDetails sx={{ maxWidth: "80vw" }}>
            <Box sx={{ margin: '1em', fontStyle: 'italic' }}>{row.coment}</Box>
            <PlayerTable row={row} />
          </AccordionDetails>
        </Accordion>
      </TableCell>
    </TableRow>
  );
});

export default function PartyAccordillonTable({party, expanded, setExpanded}) {

  const accordionRefs = useRef({});

  useEffect(() => {
    if (expanded && accordionRefs.current[expanded]) {
      const timeout = setTimeout(() => {
        accordionRefs.current[expanded].scrollIntoView({
          behavior: 'smooth',
          block: 'nearest',
          inline: 'nearest',
        });
      }, 100); // Adjust delay as needed
      return () => clearTimeout(timeout);
    }
  }, [expanded]);

  const handleAccordionChange = useCallback(
    (panel) => (event, isExpanded) => {
      // setExpanded(isExpanded ? panel : false);
    },
    [setExpanded]
  );


  return(
      <Box className="TableAccordillon" sx={{ width: "100%" }} key={party.date + '-' + party.lieu}>
        <TableContainer component={Paper} key={'TableContainer-' + party.date + '-' + party.lieu}>
        <Table stickyHeader  key={'table-' + party.date +  '-' + party.lieu}>
          <TableHead > 
            <TableRow >
            <TableCell sx={{ width: "20%", padding: '0.7em', paddingInline: '0.5em' , border:'none' }}>MJ</TableCell>
            <TableCell sx={{ width: "36%", padding: '0.7em', paddingInline: '0.5em'  }}>Jeu</TableCell>
            <TableCell sx={{ width: "10%", padding: '0.7em', paddingInline: '0.5em'  }} align='center' >Type</TableCell>
            <TableCell sx={{ width: "10%", padding: '0.7em', paddingInline: '0.5em'  }} align='center' >Lieu</TableCell>
            <TableCell sx={{ width: "20%", padding: '0.7em', paddingInline: '0.5em'  }} align='center' >Nbr de joueurs</TableCell>
            <TableCell sx={{ width: "6%", padding: '0.7em', paddingInline: '0.5em'  }}></TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {party.map((row) => (
              <PartyRow
                key={'row-' + row.id}
                row={row}
                expanded={expanded}
                handleAccordionChange={handleAccordionChange}
                accordionRefs={accordionRefs}
              />
            ))}
          </TableBody>
        </Table>
      </TableContainer>
      </Box>
    )
  }