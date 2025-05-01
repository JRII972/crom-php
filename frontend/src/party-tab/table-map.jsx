import * as React from 'react';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Checkbox from '@mui/material/Checkbox';
import CssBaseline from '@mui/material/CssBaseline';
import FormControlLabel from '@mui/material/FormControlLabel';
import Divider from '@mui/material/Divider';
import FormLabel from '@mui/material/FormLabel';
import FormControl from '@mui/material/FormControl';
import Link from '@mui/material/Link';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import Stack from '@mui/material/Stack';
import MuiCard from '@mui/material/Card';
import { styled } from '@mui/material/styles';
import AppTheme from '../shared-theme/AppTheme';

import SideMenu from './components/SideMenu';
import AppNavbar from './components/AppNavbar';
import Header from '../dashboard/components/Header';
import LockOutlineIcon from '@mui/icons-material/LockOutline';

import ViewListIcon from '@mui/icons-material/ViewList';
import ViewModuleIcon from '@mui/icons-material/ViewModule';
import ViewQuiltIcon from '@mui/icons-material/ViewQuilt';
import ToggleButton from '@mui/material/ToggleButton';
import ToggleButtonGroup from '@mui/material/ToggleButtonGroup';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell, { tableCellClasses } from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';


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

export default function TableMap(parties) {
    const sessions = groupByDate(Object.values(parties));

    return(
      <div>
      {Object.keys(sessions).map((date) => (
        <Box sx={{ width: '100%', paddingBottom: '1em'}}>
        <Typography variant="h4" component="h1" sx={{ mb: 2 }}>
            Partie du {date}
        </Typography>
        <Box >
        <TableContainer component={Paper}>
        <Table sx={{ minWidth: 700 }} aria-label="customized table">
            <TableHead>
            <TableRow>
                <StyledTableCell>Maître de jeu</StyledTableCell>
                <StyledTableCell>Jeu</StyledTableCell>
                <StyledTableCell>Type</StyledTableCell>
                <StyledTableCell>Lieu</StyledTableCell>
                <StyledTableCell>Commentaire</StyledTableCell>
                <StyledTableCell>Max</StyledTableCell>
                <StyledTableCell>Joueurs</StyledTableCell>
            </TableRow>
            </TableHead>
            <TableBody>
            {sessions[date].map((row) => (
                <StyledTableRow key={row.id}>
                <StyledTableCell component="th" scope="row">
                    {row.maitre_de_jeu}
                </StyledTableCell>
                <StyledTableCell align="center">{row.jeu}</StyledTableCell>
                <StyledTableCell align="center">{row.type}</StyledTableCell>
                <StyledTableCell align="center">{row.lieu}</StyledTableCell>
                <StyledTableCell align="left">{row.commentaire}</StyledTableCell>
                <StyledTableCell align="center">{row.locked ? <LockOutlineIcon /> : row.max_player}</StyledTableCell>
                {/* TODO: change player mapping */}
                <StyledTableCell align="center">{row.players.map((joueur) => (joueur + ', '))}</StyledTableCell> 
                </StyledTableRow>
            ))}
            </TableBody>
        </Table>
        </TableContainer>
        </Box>
        <Divider sx={{marginTop: '2em'}} orientation="horizontal" variant="middle" flexItem  />
        </Box>
        ))}
      </div>
      )
  }