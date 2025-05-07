import { styled } from '@mui/material/styles';
import LockOutlineIcon from '@mui/icons-material/LockOutline';

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

export default function PartyTable({party}) {
    return(
      <TableContainer component={Paper}>
        <Table sx={{ width: "100%" }} aria-label="customized table" stickyHeader>
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
            {party.map((row) => (
                <StyledTableRow key={row.id}>
                <StyledTableCell component="th" scope="row">
                    {row.maitre_de_jeu}
                </StyledTableCell>
                <StyledTableCell align="center">{row.jeu}</StyledTableCell>
                <StyledTableCell align="center">{row.type}</StyledTableCell>
                <StyledTableCell align="center">{row.lieu}</StyledTableCell>
                <StyledTableCell align="left">{row.coment}</StyledTableCell>
                <StyledTableCell align="center">{row.locked ? <LockOutlineIcon /> : row.max_player}</StyledTableCell>
                {/* TODO: change player mapping */}
                <StyledTableCell align="center">{row.players.map((joueur) => (joueur + ', '))}</StyledTableCell> 
                </StyledTableRow>
            ))}
            </TableBody>
        </Table>
      </TableContainer>
      )
  }