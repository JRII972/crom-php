import React from "react";
import {
  Box,
  Button,
  Card,
  CardContent,
  CardMedia,
  Typography,
  Avatar,
  Stack,
  Divider,
  List,
  ListItem,
  ListItemAvatar,
  ListItemText,
  ListItemButton,
  CardActionArea,
  Fade,
  Modal,
  Backdrop,
  useMediaQuery,
  alpha,
} from "@mui/material";

import {partie as _p} from './data/partie';
import parties from './data/parties';
import { DynamicThemeProvider, createDynamicTheme } from "../utils/DynamicThemeProvider";
import { getCurrentUser } from "../models/User";

import LockOutlineIcon from '@mui/icons-material/LockOutline';
import BlockIcon from '@mui/icons-material/Block';
import SizeBoundary from "../utils/SizeBoundaryProps";
import { useTheme } from "@emotion/react";
import { useParams } from 'react-router-dom';
import { findGameByName } from "./data/games";

const Modalstyle = {
  position: 'absolute',
  top: '50%',
  left: '50%',
  transform: 'translate(-50%, -50%)',
  width: 400,
  bgcolor: 'background.paper',
  border: '2px solid #000',
  boxShadow: 24,
  p: 4,
};

const sessionButton = (session) => {
  if (false) { //TODO: indiquer les différents cas
    return(
    <Button variant="outlined" color="secondary" sx={{ ml: "auto" }}>
      S'inscrire
    </Button>
    )
  } else if (false) {
    return(
      <Button variant="outlined" color="secondary" sx={{ ml: "auto" }} disabled endIcon={<LockOutlineIcon />}>
        Complet
      </Button>
    )
  } else if (true) {
    return(
      <>
      {/* <Box
        component="span"
        sx={{
          // cache ce span quand le bouton fait 215px ou moins

        }}
      > */}
        <Button
          variant="outlined"
          color="secondary"
          disabled
          endIcon={<BlockIcon />}
          sx={{
            ml: "auto",
            '@media (max-width: 1650px)': {
            display: 'none',
          },
          }}
        >
          Réservé aux membres de la campagne
        </Button>
        <Button
          variant="outlined"
          color="secondary"
          disabled
          endIcon={<BlockIcon />}
          sx={{
            ml: "auto",
            minWidth: '100px',
            display: 'none',                // caché par défaut
            '@media (max-width: 1650px)': {  // mais visible sur petits espaces
              display: 'flex',
          },
          }}
        >
          Réservé
        </Button>
      {/* </Box> */}
      </>
    )
  }
}

// TODO: Make the card mobile friendly
// TODO: add click on player and mj avatar for a new page with all parties and sessions likend to them
const PartiePage = () => {
  const { id } = useParams()
  console.log(id)

  const _partie = parties.find((party) => party.id === Number(id));

  // If _partie is not found, you might want to handle this case
  if (!_partie) {
    return <Typography>Partie non trouvée</Typography>;
  }

  // Use _partie directly, or merge with additional data if needed
  const partie = { ..._p, ..._partie, image: findGameByName(_partie.jeu).image };

  console.log(id);
  console.log(_partie);
  console.log(partie);

  const isCampagne = partie.type === "Cmp";
  const isFermé = true;
  const currentUser = getCurrentUser();

  const theme = useTheme();
  const dynamicTheme = createDynamicTheme(partie.image);
  const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));
  
  const [openPlayerModal, setOpenPlayerModal] = React.useState(false);
  const handleOpenPlayerModal = () => setOpenPlayerModal(true);
  const handleClosePlayerModal = () => setOpenPlayerModal(false);

  return (
  <Box
    sx={{
      width: {
        xs: '100%', // Full width on extra-small screens (<600px)
        sm: '100%', // 95% width on small screens (≥600px)
        md: '100%', // 90% width on medium screens (≥900px)
        lg: '85%', // 85% width on large screens (≥1200px)
        xl: '80%', // 80% width on extra-large screens (≥1536px)
      },
      maxWidth: {
        lg: 1200, // Max width of 1200px on lg
        xl: 1300, // Max width of 1300px on xl
      },
    }}
  >
    <DynamicThemeProvider imageUrl={partie.image}>
      {/* Bandeau principal */}
      {!isMobileScreen && bandeauPrincipalPC(partie, isCampagne, isFermé, handleOpenPlayerModal, theme)}
      {isMobileScreen && bandeauPrincipalMobile(partie, isCampagne, isFermé, handleOpenPlayerModal, theme)}

      {/* Zone secondaire */}
      <Box mt={4}
        sx={{
          bgcolor: 'background.subcontent',
          px: 4,
          py: 2,
          color: 'text.subcontent',
        }}
      >
        <Typography variant="h5" gutterBottom>
          Description
        </Typography>
        <Stack>
          <Stack direction='row' alignItems='center' gap={1}>
            <Typography variant="overline" gutterBottom>
              Système de jeu
            </Typography>
            <Typography variant="subtitle2" gutterBottom>
              {partie.jeu}
            </Typography>
          </Stack>
          <Stack direction='row' alignItems='center' gap={1}>
            <Typography variant="overline" gutterBottom>
              Type
            </Typography>
            <Typography variant="subtitle2" gutterBottom>
              Combat
            </Typography>
            <Divider orientation="vertical" flexItem variant="middle"/>
            <Typography variant="subtitle2" gutterBottom>
              Spacial
            </Typography>
            <Divider orientation="vertical" flexItem variant="middle"/>
            <Typography variant="subtitle2" gutterBottom>
              Dark Fantaisie
            </Typography>
            <Divider orientation="vertical" flexItem variant="middle"/>
            <Typography variant="subtitle2" gutterBottom>
              Alien
            </Typography>
          </Stack>
          <Stack>
            <Typography variant="overline" gutterBottom>
              Description
            </Typography>
            <Typography
              variant="subtitle2"
              sx={{ 
                pl: 2,
                textAlign: 'justify',
              }}
            >{partie.coment}</Typography>
          </Stack>
        </Stack>
      </Box>

      {/* Zone terciaire */}
      <Box mt={4}
        sx={{
          px: 4
        }}
      >
        <Typography variant="h5" gutterBottom>
          Prochaines sessions
        </Typography>
        <Typography variant="overline" gutterBottom>
          Vous devez être inscrit à cette campagne pour participer !
        </Typography>
        <List>
        {partie.prochainesSessions.map((session) => (
          <React.Fragment key={session.id}>
            <ListItem alignItems="flex-start">
              <ListItemAvatar>
                {/* TODO: Add the session_number to database */}
                {/* TODO: Add the other session information like time here */}
                <Avatar 
                  sx={{
                    bgcolor: 'primary.dark'
                  }}
                ># {session.session_number}</Avatar>
              </ListItemAvatar>
              <Box sx={{ flexGrow: 1 }}>
                <Typography variant="body1">
                  {`${new Date(session.date).toLocaleDateString()} - ${session.lieu}`}
                </Typography>
                <Stack
                  direction="row"
                  mt={1}
                  sx={{
                    overflow: 'auto',
                    justifyContent: 'flex-start',
                    alignItems: 'center',
                    '@media (max-width: 1650px)': {
                      flexWrap: 'wrap',
                    },
                  }}
                >
                  {session.joueurs.map((joueur) => (
                    <Stack direction="row" alignItems="center" key={joueur.id} mr={1}>
                      <Avatar
                        src={joueur.avatar}
                        alt={joueur.nom}
                        sx={{ width: 30, height: 30 }}
                      />
                      <Stack direction="column" p={0.5}>
                        <Typography variant="body2">{joueur.nom}</Typography>
                        {joueur.pseudo && (
                          <Typography variant="caption" sx={{ whiteSpace: 'nowrap' }}>
                            {joueur.pseudo}
                          </Typography>
                        )}
                      </Stack>
                    </Stack>
                  ))}
                </Stack>
              </Box>
              <ListItemButton
                sx={{
                  margin: 'auto',
                }}
              >
                {sessionButton(session)} {/* Updated to pass session instead of partie */}
              </ListItemButton>
            </ListItem>
            <Divider component="li" />
          </React.Fragment>
        ))}
      </List>
      </Box>

      {/* PLAYER LIST MODAL */}
      {PlayerListModal(partie, openPlayerModal, handleClosePlayerModal)}
    </DynamicThemeProvider>
  </Box>
  );
};

export default PartiePage;

function PlayerListModal(partie, openPlayerModal, handleClosePlayerModal) {
  const isMobileScreen = useMediaQuery(theme => theme.breakpoints.down('md'));

  return (
    <Modal
      aria-labelledby="transition-modal-title"
      aria-describedby="transition-modal-description"
      open={openPlayerModal}
      onClose={handleClosePlayerModal}
      closeAfterTransition
      slots={{ backdrop: Backdrop }}
      slotProps={{
        backdrop: {
          timeout: 500,
        },
      }}
    >
      <Fade in={openPlayerModal}>
        <Box sx={Modalstyle}>
          <Typography id="transition-modal-title" variant="h6" component="h2" gutterBottom>
            Liste des joueurs
          </Typography>
          <List sx={{ maxHeight: '60vh', overflowY: 'auto' }}>
            {partie.joueurs.map((joueur) => (
              <ListItem key={joueur.id} sx={{ py: 0.5 }}>
                <ListItemAvatar>
                  <Avatar
                    src={joueur.avatar}
                    alt={joueur.nom}
                    sx={{
                      // Respect theme's MuiAvatar breakpoint styles
                      ...(isMobileScreen && { width: 24, height: 24 }), // Smaller on mobile
                    }}
                  />
                </ListItemAvatar>
                <ListItemText
                  primary={joueur.nom}
                  secondary={joueur.pseudo || null} // Show pseudo if it exists, else null
                  primaryTypographyProps={{ variant: 'body2' }}
                  secondaryTypographyProps={{ variant: 'caption' }}
                />
              </ListItem>
            ))}
          </List>
        </Box>
      </Fade>
    </Modal>
  );
}

function bandeauPrincipalMobile(partie, isCampagne, isFermé, handleOpenPlayerModal, theme) {
  return <Card
    sx={{
      position: 'relative',
      overflow: 'hidden',
      display: 'flex',
      flexDirection: 'column',
      justifyContent: 'flex-end',
      // p: {
      //   sm: 1,
      //   md: 2, // 90% width on medium screens (≥900px)
      // },
      // pb: 0,
      height: {
        xs: '40vh',
        sm: '40vh',
        md: '60vh',
      },
      background: theme => theme.palette.primary.main,
      color: theme => theme.palette.getContrastText(theme.palette.primary.main),
      textAlign: 'center',
    }}
  >
    <Box
      sx={{
        position: 'absolute',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        backgroundImage: `url(${partie.image})`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        // agrandit légèrement pour éviter les bords noirs lors du flou
        transform: 'scale(1.1)',
        // filter: 'blur(8px)',
      }} />
    {/* <CardMedia
        sx={{ position: 'relative'}}
          component="img"
          // height="50%"
          image={partie.image}
          alt={partie.party_name}
        /> */}
    <CardContent
      sx={{
        position: 'relative',
        backgroundColor: 'background.mainContent',
        p: 1,
        '&:last-child' : {
          pb: 1,
        }
      }}
    >
      <Stack direction="row" spacing={1} alignItems="center" justifyContent='space-between'>
        <Typography variant="h6" textAlign={'start'}>
        {partie.party_name}
      </Typography>
        
        <Box sx={{ display: 'flex', gap: 1 }}>
          {/* <Button variant="contained" color="warning" sx={{ ml: "auto" }}>
            Se désincrire
          </Button> */}
          <Button variant="contained" color="primary" size="small">
            S'inscrire
          </Button>
          {/* <Button variant="outlined" color="primary" sx={{ ml: "auto" }}>
            Complet
          </Button> */}
        </Box>
      </Stack>
      

      <Stack direction='row' gap={0.5}>
        <Typography variant="body2">{partie.jeu}</Typography>
        <Divider orientation="vertical" flexItem sx={{ borderColor: 'background.divider' }} />
        <Typography variant="body2">{isCampagne ? "Campagne" : "One-shot"}</Typography>
        { isCampagne && <Typography variant="body2">{isFermé ? "Fermé" : "Ouverte"}</Typography> }
      </Stack>

      <Typography variant="subtitle2" textAlign={'start'} fontSize={12}>
        {partie.short_coment}
      </Typography>
      
      <Stack direction="row" spacing={2} alignItems="center" justifyContent='space-between'>

        <Stack direction='column' gap={1} alignItems='start'>
          <Typography variant="overline" fontSize={10} whiteSpace='nowrap'>MJ :</Typography>
          <Stack direction='row' spacing={0.5} alignItems='center'>
            <Avatar src={partie.mj.avatar} size="small"/>
            <Typography variant="body2" fontSize={12} sx={{ whiteSpace: 'nowrap'}}>{partie.mj.nom}</Typography>
          </Stack>
        </Stack>
        
        
        {isCampagne && (
          partiePlayerList(partie, handleOpenPlayerModal, theme)
        )}
      </Stack>
      
    </CardContent>
  </Card>;
}

function bandeauPrincipalPC(partie, isCampagne, isFermé, handleOpenPlayerModal, theme) {
  return <Card
    sx={{
      position: 'relative',
      overflow: 'hidden',
      display: 'flex',
      flexDirection: 'column',
      justifyContent: 'flex-end',
      // p: {
      //   sm: 1,
      //   md: 2, // 90% width on medium screens (≥900px)
      // },
      // pb: 0,
      height: {
        xs: '40vh',
        sm: '40vh',
        md: '60vh',
      },
      background: theme => theme.palette.primary.main,
      color: theme => theme.palette.getContrastText(theme.palette.primary.main),
      textAlign: 'center',
    }}
  >
    <Box
      sx={{
        position: 'absolute',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        backgroundImage: `url(${partie.image})`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        // agrandit légèrement pour éviter les bords noirs lors du flou
        transform: 'scale(1.1)',
        // filter: 'blur(8px)',
      }} />
    {/* <CardMedia
        sx={{ position: 'relative'}}
          component="img"
          // height="50%"
          image={partie.image}
          alt={partie.party_name}
        /> */}
    <CardContent
      sx={{
        position: 'relative',
        backgroundColor: 'background.mainContent',
      }}
    >
      <Typography variant="h4" gutterBottom>
        {partie.party_name}
      </Typography>
      <Typography variant="subtitle1">
        {partie.short_coment}
      </Typography>
      <Divider sx={{ my: 2 }} />
      <Stack direction="row" spacing={2} alignItems="center" justifyContent='space-between'>
        <Stack direction='row' spacing={2} alignItems='center'>
          <Avatar src={partie.mj.avatar} />
          <Typography>{partie.mj.nom}</Typography>

          <Divider orientation="vertical" flexItem variant="middle" sx={{ borderColor: 'background.divider' }} />

          <Typography>{isCampagne ? "Campagne" : "One-shot"}</Typography>
        </Stack>
        {/* TODO: Handle unsubsbribe and scrrol to session list if onshot */}
        {/* TODO: Add possibility to closed campaign to a permanent enroll */}
        <Box sx={{ display: 'flex', gap: 1 }}>
          <Button variant="contained" color="warning" sx={{ ml: "auto" }}>
            Se désincrire
          </Button>
          <Button variant="contained" color="primary" sx={{ ml: "auto" }}>
            S'inscrire
          </Button>
          <Button variant="outlined" color="primary" sx={{ ml: "auto" }}>
            Complet
          </Button>
        </Box>
      </Stack>
      {isCampagne && (
        partiePlayerList(partie, handleOpenPlayerModal, theme)
      )}
    </CardContent>
  </Card>;
}

function partiePlayerList(partie, handleOpenPlayerModal, theme) {
  const maxPlayerDisplayedWithName = partie.joueurs.length > (useMediaQuery(theme.breakpoints.down('xl')) ? 3 : 6);
  let maxPlayerDisplayed = useMediaQuery(theme.breakpoints.down('sm')) ? 5 : 12;
  maxPlayerDisplayed = useMediaQuery(theme.breakpoints.down('md')) ? maxPlayerDisplayed : 10;
  maxPlayerDisplayed = useMediaQuery(theme.breakpoints.down('lg')) ? maxPlayerDisplayed : 15;
  const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));

  // Memoize displayedPlayers to prevent new array reference on every render
  const displayedPlayers = React.useMemo(
    () => partie.joueurs.slice(0, maxPlayerDisplayed),
    [partie.joueurs, maxPlayerDisplayed]
  );

  // Calculate if scrolling is needed
  const needsScroll = partie.joueurs.length > maxPlayerDisplayed;

  // Dynamic scroll width
  const listRef = React.useRef(null);
  const [scrollWidth, setScrollWidth] = React.useState(0);

  React.useEffect(() => {
    if (listRef.current && needsScroll) {
      const updateScrollWidth = () => {
        setScrollWidth(listRef.current.scrollWidth / 2 - listRef.current.clientWidth);
      };
      updateScrollWidth();

      // Handle window resize to recalculate scrollWidth
      window.addEventListener('resize', updateScrollWidth);
      return () => window.removeEventListener('resize', updateScrollWidth);
    }
  }, [needsScroll, displayedPlayers.length]); // Depend on length to avoid reference issues

  // Custom animation speed (seconds)
  const animationDuration = Math.max(1, partie.joueurs.length * 1.3);

  // Player item JSX to avoid duplication in map
  const renderPlayer = (joueur, index) => (
    <Stack
      direction="row"
      alignItems="center"
      key={`${joueur.id}-${index}`}
      onClick={handleOpenPlayerModal}
      mr={1}
      sx={{
        flexShrink: 0,
        '&:hover .playerInfo': {
          visibility: 'visible',
          width: 'auto',
          transform: 'translateX(0px)',
          opacity: 1,
        },
      }}
    >
      <Avatar
        key={joueur.id}
        src={joueur.avatar}
        alt={joueur.nom}
        sx={{
          ...(isMobileScreen && { width: 24, height: 24 }),
        }}
      />
      {!isMobileScreen && (
        <Stack
          direction="column"
          p={0.5}
          alignItems="start"
          className="playerInfo"
          sx={{
            visibility: maxPlayerDisplayedWithName ? 'hidden' : 'visible',
            width: maxPlayerDisplayedWithName ? '0px' : 'auto',
            transform: 'translateX(-5px)',
            opacity: 0,
            '&:hover': {
              visibility: 'visible',
              width: 'auto',
              transform: 'translateX(0px)',
              opacity: 1,
            },
            transition: 'opacity 0.3s ease, transform 0.5s ease',
          }}
        >
          <Typography variant="body2">{joueur.nom}</Typography>
          {joueur.pseudo ? (
            <Typography variant="caption" sx={{ whiteSpace: 'nowrap' }}>
              {joueur.pseudo}
            </Typography>
          ) : null}
        </Stack>
      )}
    </Stack>
  );

  return (
    <Box >
      <Stack
        direction="column"
        mt={isMobileScreen ? 0 : 2}
        sx={{
          justifyContent: 'space-between',
          alignItems: 'flex-start',
        }}
      >
        <Stack
          direction="row"
          gap={1}
          alignItems="start"
          justifyContent="space-between"
          width="100%"
        >
          <Typography variant="overline" fontSize={isMobileScreen ? 10 : ''} onClick={handleOpenPlayerModal}>
            Joueurs inscrits :
          </Typography>
          <Button
            color="secondary"
            onClick={handleOpenPlayerModal}
            size="small"
            sx={{
              color: 'background.avatar',
              fontSize: isMobileScreen ? 10 : '',              
              ...theme.applyStyles('light', {
                borderColor: theme.palette.main,
                backgroundColor: 'text.avatar',
              }),
            }}
          >
            Voir tout
          </Button>
        </Stack>

        <Box
          sx={{
            overflow: 'hidden',
            width: '100%',
            position: 'relative',
          }}
        >
          <Stack
            direction="row"
            mt={isMobileScreen ? 0 : 1}
            sx={{
              justifyContent: 'flex-start',
              alignItems: 'center',
              ...(needsScroll && {
                display: 'flex',
                width: `${scrollWidth}px`,
                animation: `scroll ${animationDuration}s linear infinite`,
                '&:hover': {
                  animationPlayState: 'paused',
                },
                '@keyframes scroll': {
                  '0%': { transform: 'translateX(0)' },
                  '100%': { transform: `translateX(-${scrollWidth}px)` },
                },
                '@media (prefers-reduced-motion: reduce)': {
                  animation: 'none',
                },
              }),
              ...(!needsScroll && {
                flexWrap: 'wrap',
              }),
            }}
            ref={listRef}
          >
            {needsScroll ? (
              <>
                {partie.joueurs.map((joueur, index) => renderPlayer(joueur, index))}
                {displayedPlayers.map((joueur, index) => renderPlayer(joueur, index + displayedPlayers.length))}
              </>
            ) : (
              partie.joueurs.map((joueur, index) => renderPlayer(joueur, index))
            )}
          </Stack>

          {needsScroll && (
            <Typography
              variant="caption"
              onClick={handleOpenPlayerModal}
              sx={{
                position: 'absolute',
                right: 0,
                top: '50%',
                transform: 'translateY(-50%)',
                bgcolor: 'background.paper',
                px: 1,
                py: 0.5,
                borderRadius: 1,
                boxShadow: 1,
                whiteSpace: 'nowrap',
                color: 'text.secondary',
                zIndex: 1,
                ...(isMobileScreen && { fontSize: '0.75rem' }),
              }}
            >
              +{partie.joueurs.length - maxPlayerDisplayed} autres
            </Typography>
          )}
        </Box>
      </Stack>
    </Box>
  );
}