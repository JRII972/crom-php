import { useMatches } from 'react-router-dom';

import { Avatar, Box, Typography } from '@mui/material';
import BrokenImageIcon from '@mui/icons-material/BrokenImage';
import LockOutlineIcon from '@mui/icons-material/LockOutline';

import { GameSession } from '../party-tab/PartiesCards';

export function playerNumber(partie:GameSession, fontSize?:string, isMobileScreen:boolean = false) {
  if (partie.locked) {
    return(
      <LockOutlineIcon fontSize={fontSize ? fontSize : "small"}/>
    )
  } else {
    return(
      <Typography gutterBottom variant="subtitle1" component="div" m="0 0 0 0" fontSize={fontSize ? fontSize : ""} sx={{ whiteSpace: 'nowrap' }}>
        {partie.number_of_players_registered}/{partie.max_player} joueurs
      </Typography>
    )
  }
}

export function useCurrentMeta(key:String) {
  const matches = useMatches();
  // On prend la dernière route appariée
  const current = matches[matches.length - 1] || {};
  return current.handle?.[key];
}

export function getPartieNameFromId(id:number) {
  return 'D&D'
}

export function LogoIcon() {
  return (
    <Box
      sx={{
        width: '1.5rem',
        height: '1.5rem',
        bgcolor: 'black',
        borderRadius: '999px',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        alignSelf: 'center',
        backgroundImage:
          'linear-gradient(135deg, hsl(210, 98%, 60%) 0%, hsl(210, 100%, 35%) 100%)',
        color: 'hsla(210, 100%, 95%, 0.9)',
        border: '1px solid',
        borderColor: 'hsl(210, 100%, 55%)',
        boxShadow: 'inset 0 2px 5px rgba(255, 255, 255, 0.3)',
      }}
    >
      <Avatar color="inherit" alt="Logo BDR" src="/data/images/logo-bdr.png"  sx={{ fontSize: '1rem', bgcolor: 'transparent' }} />
    </Box>
  );
}

export const trimString = (str:string, maxLength:number, sliceLenght?:number, strip?:string='...') => {
  sliceLenght = sliceLenght ? sliceLenght : maxLength
  if (str.length >= maxLength) {
    return str.slice(0, sliceLenght) + strip;
  }
  return str;
};