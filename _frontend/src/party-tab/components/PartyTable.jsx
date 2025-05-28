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
import { default_image } from '../utils/default_image';
import { playerNumber, TypePartie } from '../../utils/utils';


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
  // Cibler le 2ème td enfant d'une ligne avec la classe main-row
  '&.main-row > *:nth-of-type(2)': {
    width: '2%',
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

export default function   PartyTable({sessionsList}) {  
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
                <StyledTableCell sx={{ width: "3%" }}>Max</StyledTableCell>
                <StyledTableCell align="center" sx={{ width: "20%" }}>Joueurs</StyledTableCell>
            </TableRow>
            </TableHead>
            <TableBody>
            {sessionsList.map((session) => (
                innerData(session, isMobileScreen)
            ))}
            </TableBody>
        </Table>
      </TableContainer>
      )
  }

function innerData(session, isMobileScreen) {
  console.log(session, isMobileScreen)
  return <>
    <StyledTableRow key={session.id} className="main-row">
      <StyledTableCell component="th" scope="row">
        {session.maitre_jeu.pseudo || (session.maitre_jeu.nom + ' ' + session.maitre_jeu.prenom)}
      </StyledTableCell>
      <StyledTableCell align="center" >
        {
          session.partie.nom ?
          <>
          <span>
            {session.partie.nom} <br/>
          </span>
          <span>
            {session.partie.jeu.nom}
          </span>
          </> :
          <>
          {session.partie.jeu.nom}
          </>
        }
        
      </StyledTableCell>
      <StyledTableCell align="center" sx={{textTranforme: 'capitalize'}}>{ TypePartie(session.partie.type_partie, isMobileScreen) }</StyledTableCell>
      <StyledTableCell align="center">{isMobileScreen ? session.lieu.short_nom : session.lieu.nom}</StyledTableCell>
      <StyledTableCell align="left">{session.partie.description_courte || session.jeu.description}</StyledTableCell>
      <StyledTableCell align="center">{playerNumber(session, isMobileScreen=isMobileScreen)}</StyledTableCell>
      <StyledTableCell align="center">
        {session.joueurs_session.map((joueur, idx) => (
          <span key={joueur.utilisateur.id || idx}>
            {joueur.utilisateur.pseudonyme || (joueur.utilisateur.nom + ' ' + joueur.utilisateur.prenom)}
            <br />
          </span>
        ))}
      </StyledTableCell>
    </StyledTableRow>
    
    <StyledTableRow key={session.id + '-info'} className="info-row">
      <StyledTableCell component="th" scope="row" colSpan={2}
        sx={{ display: isMobileScreen ? 'none' : '' }}
      >
        <img
          src={session.partie.image ?  session.partie.image.url : session.partie.jeu.image ? session.partie.jeu.image.url : default_image(session.partie.type_partie)}
          alt={session.partie.image ? session.partie.image.imageAlt : session.partie.jeu.image ? session.partie.image.imageAlt : 'Image par default pour ' + session.partie.type_partie}
          // TODO: Corriger les soucis de taille d'iamge quand écrant trop petit
          style={{
            maxWidth: '18em',
            width: '20vw',
            // maxHeight: '100px', // Set a max height to prevent image from dictating row height
            objectFit: 'contain', // Maintain aspect ratio
            display: 'block'
          }} />
      </StyledTableCell>
      <StyledTableCell align="left" colSpan={isMobileScreen ? 6 : 4}>{session.partie.description}</StyledTableCell>
      <StyledTableCell align="center" colSpan={1}>
        <Table aria-label="Prochaine date">
          <TableHead>
            <TableRow>
              <TableCell align='center' sx={{ pb: 0.3, pt: 0.2 }}>Prochaine date</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
          {['16/04/25', '18/05/25', '11/06/25'].map((date) => (
            <StyledTableRow
              key={session.id + 'info' + '-' + date}
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
  </>;
}


