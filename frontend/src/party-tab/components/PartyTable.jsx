import { styled } from '@mui/material/styles';
import LockOutlineIcon from '@mui/icons-material/LockOutline';

import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell, { tableCellClasses } from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';
import { useTheme } from '@emotion/react';
import { useMediaQuery } from '@mui/material';
import { findGameByName } from '../data/games';


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
  '&:last-child td, &:last-child th': {
    border: 0,
  },
  // Ensure main row is hoverable
  '&.main-row:hover': {
    backgroundColor: theme.palette.action.selected,
  },
  // Style for info row
  '&.info-row': {
    visibility: 'collapse', // Hide without removing from layout
    opacity: 0,
    transform: 'translateY(-10px)', // Start slightly above for slide-in effect
    transition: 'opacity 0.3s ease, transform 0.5s ease', // Smooth transition
  },
  '&.info-row:hover': {
    visibility: 'visible', // Show the row
    opacity: 1,
    transform: 'translateY(0)', // Start slightly above for slide-in effect
    // transition: 'opacity 0.3s ease, transform 0.3s ease', // Smooth transition
  },
  // Show info row when main row is hovered
  '&.main-row:hover + &.info-row': {
    visibility: 'visible', // Show the row
    opacity: 1,
    transform: 'translateY(0)', // Slide to original position
  },
  // Ensure subsequent info rows are shown if the previous main row is hovered
  '&.main-row:hover ~ &.info-row:first-of-type': {
    visibility: 'visible', // Show the row
    opacity: 1,
    transform: 'translateY(0)',
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

export default function   PartyTable({party}) {  
  const theme = useTheme();
  const isMobileScreen = useMediaQuery(theme.breakpoints.down('lg'));
  
    return(
      <TableContainer component={Paper}>
        <Table sx={{ width: "100%" }} aria-label="customized table" stickyHeader>
            <TableHead>
            <TableRow>
                <StyledTableCell sx={{ width: "12%" }} >Maître de jeu</StyledTableCell>
                <StyledTableCell sx={{ width: "10%" }}>Jeu</StyledTableCell>
                <StyledTableCell sx={{ width: "5%" }}>Type</StyledTableCell>
                <StyledTableCell sx={{ width: "5%" }}>Lieu</StyledTableCell>
                <StyledTableCell sx={{ width: "45%" }}>Commentaire</StyledTableCell>
                <StyledTableCell  sx={{ width: "3%" }}>Max</StyledTableCell>
                <StyledTableCell align="center" sx={{ width: "20%" }}>Joueurs</StyledTableCell>
            </TableRow>
            </TableHead>
            <TableBody>
            {party.map((row) => (
                <>
                <StyledTableRow key={row.id} className="main-row">
                  <StyledTableCell component="th" scope="row">
                      {row.maitre_de_jeu}
                  </StyledTableCell>
                  <StyledTableCell align="center">{row.jeu}</StyledTableCell>
                  <StyledTableCell align="center">{row.type}</StyledTableCell>
                  <StyledTableCell align="center">{row.lieu}</StyledTableCell>
                  <StyledTableCell align="left">{row.short_coment}</StyledTableCell>
                  <StyledTableCell align="center">{row.locked ? <LockOutlineIcon /> : row.max_player}</StyledTableCell>
                  {/* TODO: change player mapping */}
                  <StyledTableCell align="center">{row.players.map((joueur) => (joueur + ', '))}</StyledTableCell> 
                </StyledTableRow>

                {/* Show only when above row is */}
                <StyledTableRow key={row.id + '-info'} className="info-row">
                  <StyledTableCell component="th" scope="row" colSpan={2} 
                    sx={{ display: isMobileScreen ? 'none' : '' }}
                  >
                      <img 
                        src={findGameByName(row.jeu).image} 
                        alt={row.image_alt} 
                        // TODO: Corriger les soucis de taille d'iamge quand écrant trop petit
                        style={{ 
                          maxWidth: '18em',
                          width: '20vw',
                          // maxHeight: '100px', // Set a max height to prevent image from dictating row height
                          objectFit: 'contain', // Maintain aspect ratio
                          display: 'block'
                          }}/>
                  </StyledTableCell>
                  <StyledTableCell align="left" colSpan={isMobileScreen ? 6 : 4}>{row.coment}</StyledTableCell>
                  <StyledTableCell align="center" colSpan={1}>
                    <Table aria-label="Prochaine date">
                      <TableHead>
                        <TableRow>
                          <TableCell align='center' sx={{ pb: 0.3, pt: 0.2}}>Prochaine date</TableCell>
                        </TableRow>
                      </TableHead>
                      <TableBody>
                        {['16/04/25', '18/05/25', '11/06/25'].map((date) => (
                          <StyledTableRow
                            key={row.id + 'info' + '-' + date}
                            // sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
                            // sx={{
                            //   '&:nth-child(odd)': {
                            //     backgroundColor: 'blue',
                            //   },
                            // }}
                          >
                            <StyledTableCell scope="row" align='center' sx={{ p:0.2 }}>
                              {date}
                            </StyledTableCell>
                          </StyledTableRow>
                        ))}
                      </TableBody>
                    </Table>
                  </StyledTableCell> 
                </StyledTableRow>
                </>
            ))}
            </TableBody>
        </Table>
      </TableContainer>
      )
  }