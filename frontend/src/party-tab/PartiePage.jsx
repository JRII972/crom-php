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
} from "@mui/material";

import partie from './data/partie';
import { DynamicThemeProvider } from "../utils/DynamicThemeProvider";
import { getCurrentUser } from "../models/User";

import LockOutlineIcon from '@mui/icons-material/LockOutline';
import BlockIcon from '@mui/icons-material/Block';
import SizeBoundary from "../utils/SizeBoundaryProps";

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
  const isCampagne = partie.type === "campagne";
  const currentUser = getCurrentUser();

  
  const [openPlayerModal, setOpenPlayerModal] = React.useState(false);
  const handleOpenPlayerModal = () => setOpenPlayerModal(true);
  const handleClosePlayerModal = () => setOpenPlayerModal(false);

  return (
  <Box
    sx={{
      width: '90%',
      maxWidth: 1300
    }}
  >
    <DynamicThemeProvider imageUrl={partie.image} p={2}>
      {/* Bandeau principal */}
      <Card
        sx={{

        position: 'relative',
        overflow: 'hidden',
          p: 4,
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
            filter: 'blur(8px)',
          }}
        />
        <CardMedia
        sx={{ position: 'relative', }}
          component="img"
          height="350"
          image={partie.image}
          alt={partie.nom}
        />
        <CardContent
          sx={{ position: 'relative', backgroundColor: 'rgba(0, 0, 0, 0.4)', }}
        >
          <Typography variant="h4" gutterBottom>
            {partie.nom}
          </Typography>
          <Typography variant="subtitle1">
            {partie.description}
          </Typography>
          <Divider sx={{ my: 2 }} />
          <Stack direction="row" spacing={2} alignItems="center" justifyContent='space-between'>
            <Stack direction='row' spacing={2} alignItems='center'>
              <Avatar src={partie.mj.avatar} />
              <Typography>{partie.mj.nom}</Typography>
              <Typography>- {isCampagne ? "Campagne" : "One-shot"}</Typography>
            </Stack>
            {/* TODO: Handle unsubsbribe and scrrol to session list if onshot */}
            {/* TODO: Add possibility to closed campaign to a permanent enroll */}
            <Button variant="contained" color="primary" sx={{ ml: "auto" }}>
              S'inscrire
            </Button>
          </Stack>
          {isCampagne && (
            <CardActionArea onClick={handleOpenPlayerModal}>
              <Stack direction="column" mt={2}
                sx={{
                  justifyContent: "space-between",
                  alignItems: "flex-start",
                }}
              >
                <Typography variant="h6">Joueurs inscrits :</Typography>
                <Stack direction="row" spacing={1} mt={1} ml={2}>
                  {partie.joueurs.map((joueur) => (
                    <Avatar key={joueur.id} src={joueur.avatar} alt={joueur.nom} />
                  ))}
                </Stack>
              </Stack>
            </CardActionArea>
          )}
        </CardContent>
      </Card>

      {/* Zone secondaire */}
      <Box mt={4}>
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
                  {/* TODO: Add the other session informations like time her */}
                  <Avatar># {partie.session_number}</Avatar>
                </ListItemAvatar>
                {/* FIXME: change this bellow to not have a div inside a <p> */}
                <ListItemText
                  primary={`${new Date(session.date).toLocaleDateString()} - ${session.lieu}`}
                  secondary={
                    <Stack direction="row" mt={1} sx={{
                      overflow: 'auto',
                      justifyContent: 'flex-start',
                      alignItems: 'center',
                      '@media (max-width: 1650px)': {
                        flexWrap: 'wrap',
                      },
                    }}>
                      {session.joueurs.map((joueur) => (
                        <Stack direction='row' alignItems='center' key={joueur.id} mr={1}>
                          <Avatar key={joueur.id} src={joueur.avatar} alt={joueur.nom} sx={{ width: 30, height: 30 }} />
                          <Stack direction='column' p={0.5}>
                            <Typography variant="body2">{joueur.nom}</Typography>
                            {
                              joueur.pseudo ? <Typography variant="caption" sx={{ whiteSpace: 'nowrap'}}>{joueur.pseudo}</Typography>
                              : <></>
                            }
                          </Stack>
                        </Stack>
                      ))}
                    </Stack>
                  }
                />
                <ListItemButton
                  sx={{
                    margin: 'auto'
                  }}
                >
                  {sessionButton(partie)}
                </ListItemButton>
              </ListItem>
              <Divider component="li" />
            </React.Fragment>
          ))}
        </List>
      </Box>

      {/* PLAYER LIST MODAL */}
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
        {/* TODO: add player list in modal */}
        <Fade in={openPlayerModal}>
          <Box sx={Modalstyle}>
            <Typography id="transition-modal-title" variant="h6" component="h2">
              Text in a modal
            </Typography>
            <Typography id="transition-modal-description" sx={{ mt: 2 }}>
              Duis mollis, est non commodo luctus, nisi erat porttitor ligula.
            </Typography>
          </Box>
        </Fade>
      </Modal>
    </DynamicThemeProvider>
  </Box>
  );
};

export default PartiePage;