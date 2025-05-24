import React, { useRef, useEffect, useCallback, useState } from 'react';
import { Link } from 'react-router-dom';

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
import { Accordion, AccordionDetails, AccordionSummary, Avatar, CardActionArea, CardContent, CardMedia, Divider, Grid, Skeleton } from '@mui/material';

import Chip from '@mui/material/Chip';
import Stack from '@mui/material/Stack';
import FaceIcon from '@mui/icons-material/Face';
import Modal from '@mui/material/Modal';
import Button from '@mui/material/Button';
import BrokenImageIcon from '@mui/icons-material/BrokenImage';
import Typography from '@mui/material/Typography';
import Card from '@mui/material/Card';

import PlayerTable from './PlayersTable';
import { findGameByName } from '../data/games';
import { PartyCardContent } from './PartyCardContent';
import { playerNumber } from '../../utils/utils';


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

const PartyRow = function({ row, expanded, handleAccordionChange, accordionRefs }) {
  const panelId = 'panel' + row.id;

  const [open, setOpen] = React.useState(false);
  const handleModalDescriptionOpen = () => setOpen(true);
  const handleModalDescriptionClose = () => setOpen(false);

  const [loaded, setLoaded] = useState(false);
  const [error, setError] = useState(false);
  const gameData = findGameByName(row.jeu)

  const handleImageError = (
    e
  ) => {
    setError(true);
    setLoaded(false);
    (e.currentTarget).onerror = null;
    (e.currentTarget).src = 'https://placehold.co/216x140';
  };

  const handleImageLoad = () => {
    setLoaded(true);
  };

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
                      <span>{row.party_name}</span>
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
          <AccordionDetails sx={{ maxWidth: "95vw", px:1 }}>
            <Box sx={{ margin: '1em', fontStyle: 'italic' }} clickable onClick={handleModalDescriptionOpen}>
              {row.short_coment}
              <Button component='a' onClick={handleModalDescriptionOpen}>Plus d'info</Button>
            </Box>
            {/* <PlayerTable row={row} /> */}
            <Box
              sx={{
                display: 'inline-flex',
                gap: 1,
              }}
            >
              {row.players.map((player, rowIndex) => (
                <Chip 
                  variant="outlined"          
                  color="secondary"
                  size="medium"
                  label={player} 
                  // icon={<FaceIcon />}
                  avatar={<Avatar alt={player} src="/static/images/avatar/1.jpg" />}
                  
                  component="a"
                  href="#basic-chip"
                  clickable
                />
              ))}
            </Box>
          </AccordionDetails>
        </Accordion>

        <Modal
          open={open}
          onClose={handleModalDescriptionClose}
          aria-labelledby="modal-modal-title"
          aria-describedby="modal-modal-description"
        >
          <Box sx={{
            position: 'absolute',
            top: '50%',
            left: '50%',
            transform: 'translate(-50%, -50%)',
            width: 400,
            bgcolor: 'background.paper',
            border: '2px solid #000',
            boxShadow: 24,
            p: 0,
          }}>
            <Card
              // sx={cardStyles}
              key={row.id}
        
              // variant={actualMode === 'light' ? 'outlined' : ''}
            >
              <Box sx={{ width: '100%', height: '100%', position: 'relative', overflow: 'hidden' }}>
        
                    
                <CardActionArea
                  component={Link}
                  to={"./partie/" + row.id}
                  sx={{
                    flex: 1, // Prend l'espace disponible
                    height: '100%',
                    display: 'flex',
                    flexDirection: 'column',
                  }}
                >
                  <Box
                    sx={{
                    m: 1,
                    height: '15vh',
                    width: '100%',
                    // borderTopLeftRadius: 5,
                    // borderTopRightRadius: 5,
                    borderRadius: 1,
                    overflow: 'hidden',
                    }}
                  >
                    {!loaded && !error && (
                      <Skeleton variant="rectangular" width="100%" height={'100%'} />
                    )}
                    {!error && (
                      <CardMedia
                        component="img"
                        height={'100%'}
                        // image={row.image}
                        image={findGameByName(row.jeu).image}
                        alt={row.image_alt}
                        onLoad={handleImageLoad}
                        onError={handleImageError}
                        sx={{ display: loaded ? 'block' : 'none', objectFit: 'cover', width: '100%' }}
                      />
                    )}
                    {error && (
                      <Box
                        height={'100%'}
                        display="flex"
                        alignItems="center"
                        justifyContent="center"
                      >
                        <BrokenImageIcon fontSize="large" color="disabled" />
                      </Box>
                    )}
                  </Box>
        
                  {/* <Divider sx={{ my: 1 }}/> */}
        
                  
                  <CardContent
                        sx={{
                          backgroundColor: 'background.card',
                          p: 1,
                          
                          flex: 1,
                          // boxSizing: 'border-box',
                          width: '100%',
                          display: 'flex',
                          flexDirection: 'column',
                          justifyContent: 'space-between',
                          borderRadius: 1,
                        }}
                      >
                      <Stack spacing={0.8} >
                          <Stack sx={{  }}>
                            <Typography variant="h5" component="div" 
                                sx={{
                                    textalign: 'center',
                                    alignItems: 'center',
                                    whiteSpace: 'nowrap',
                                    textAlign: 'center'
                                }}
                            >
                                {row.party_name}
                            </Typography>
                            
                              <Stack direction={"column"} spacing={0.5} sx={{alignItems: 'center', justifyContent: 'space-between' }}>
                                  <Typography gutterBottom variant="subtitle1" component="div" 
                                  sx={{
                                      // lineHeight: subTitleLineHeight,
                                      alignItems: 'center',
                                  }}
                                  >
                                  {gameData ? gameData.displayName : row.jeu}
                                  </Typography>
                  
                                  <Typography gutterBottom variant="subtitle1" component="div" 
                                  sx={{
                                      // lineHeight: subTitleLineHeight,
                                      alignItems: 'center',
                                  }}
                                  >
                                    {row.maitre_de_jeu}
                                  </Typography>
                              </Stack>
                  
                              <Grid container spacing={1} sx={{mt: 0.7}} >
                  
                                <Grid size={4}
                                sx={{
                                    display: 'flex',
                                    justifyContent: 'left',
                                    alignItems: 'center',
                                }}
                                >
                                <Typography variant="subtitle1" component="div" 
                                    sx={{
                                        // lineHeight: subTitleLineHeight,
                                        alignItems: 'center',
                                    }}
                                    >
                                    {row.type}
                                </Typography>
                                </Grid>
                
                                <Grid size={4}                  
                                  sx={{
                                      display: 'flex',
                                      justifyContent: 'center',
                                      alignItems: 'center',
                                  }}
                                >
                                  {playerNumber(row, '', true)}
                                </Grid>
                
                                <Grid size={4} 
                                sx={{
                                    display: 'flex',
                                    justifyContent: 'right',
                                    alignItems: 'center',
                                }}
                                >
                                <Typography variant="subtitle1" component="div" 
                                    sx={{
                                        // lineHeight: subTitleLineHeight,
                                        alignItems: 'center',
                                    }}
                                    >
                                    {row.lieu}
                                </Typography>                  
                                </Grid>
                
                                
                            </Grid>
                  
                  
                  
                          </Stack>
                  
                          {/* <PartyCardSubInfo partie={partie} commentSize={commentSize} isMobileScreen={isMobileScreen}/> */}
                          
                          <Divider />

                          <Typography
                            variant="body2"
                            sx={{
                                color: 'text.secondary',
                                display: '-webkit-box',
                                WebkitBoxOrient: 'vertical',
                                // WebkitLineClamp: 3,
                                overflow: 'hidden',
                                textOverflow: 'ellipsis',
                                // fontSize: commentSize,
                                textAlign: 'justify',
                                mt : 2
                            }}
                          >
                            {row.coment}
                          </Typography>
                      </Stack>
                      
                    </CardContent>
        
                    
                </CardActionArea>
        
                
              </Box>
            </Card>
          </Box>
        </Modal>
      </TableCell>
    </TableRow>
  );
};

export default function PartyAccordillonTable({party, expanded, setExpanded}) {

  const accordionRefs = useRef({});

  useEffect(() => {
    if (expanded && accordionRefs.current[expanded]) {
      // TODO: Remove the timeout
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
      setExpanded(isExpanded ? panel : false);
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