import {
  FC,
  useState, SyntheticEvent
} from 'react';
import {
  Card,
  Box,
  CardActionArea,
  Skeleton,
  CardMedia, Typography, Collapse,
  useMediaQuery,
  useTheme, useColorScheme,
  CardContent,
  Stack
} from '@mui/material';
import BrokenImageIcon from '@mui/icons-material/BrokenImage';
import GameSession from '../../types/GameSession';
import { Link } from 'react-router-dom';

import { findGameByName } from '../data/games';
import {default_image} from '../utils/default_image'

interface Game {
  nom: string;
  description: string;
  short_coment?: string;
  image: string;
  categories?: string[];
  image_alt?: string;
  icon?: string;
  displayName?: React.ReactNode;
}

interface GameCardProps {
  game: Game;
  type?: 'jdr' | 'tabletop' | 'other';
  displayDate?: boolean;
}


const GameCard: FC<GameCardProps> = ({ game, type='jdr'}) => {
  // états
  const [loaded, setLoaded] = useState<boolean>(false);
  const [error, setError] = useState<boolean>(false);
  const [open, setOpen] = useState<boolean>(false);
  const theme = useTheme();
  const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));

  const { mode, systemMode, setMode } = useColorScheme();
  const actualMode = systemMode || mode;

  //TODO: Scroll quand survolle
  // 1) ref pour le contenu du Collapse
  // const collapseRef: RefObject<HTMLDivElement> = useRef<HTMLDivElement>(null);

  // 2) scroll quand on ouvre
  // useEffect(() => {
  //   if (open && collapseRef.current) {
  //     collapseRef.current.scrollIntoView({
  //       behavior: 'smooth',
  //       block: 'end',
  //       inline: 'nearest',
  //     });
  //   }
  // }, [open]);

  // 3) handlers
  const handleMouseEnter = (): void => setOpen(true);
  const handleMouseLeave = (): void => setOpen(false);  

  game.image = game.image ? game.image : default_image()

  const handleImageError = (
    e: SyntheticEvent<HTMLImageElement, Event>
  ): void => {
    setError(true);
    setLoaded(false);
    (e.currentTarget as HTMLImageElement).onerror = null;
    (e.currentTarget as HTMLImageElement).src = 'https://placehold.co/216x140';
  };

  const handleImageLoad = (): void => {
    setLoaded(true);
  };

  // Change les tailles de la carte en fonction de la taille de l'écran
  const cardStyles = {
    minWidth: isMobileScreen ? 160 : 200,
    height: isMobileScreen ? 300 : 280,
    display: 'flex',
    flexDirection: 'row',
    alignItems: 'stretch',
    maxWidth: isMobileScreen ? 160 : 550,
    p: 0,
    
    // px: isMobileScreen ? 1 : 2,    
  };

  const cardImageSize = isMobileScreen ? 100 : 140;
  const titleSize = isMobileScreen ? '1em' : '1.15em';
  const subTitleSize = isMobileScreen ? '0.8em' : '0.9em';
  const subTitleLineHeight = isMobileScreen ? '0.8em' : '0.9em';
  const commentSize = isMobileScreen ? '0.75em' : '0.85em';
  const playerListPadding = isMobileScreen ? 0 : 0;

  return (
    <Card
      sx={cardStyles}
      key={game.nom}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}

      variant={actualMode === 'light' ? 'outlined' : ''}
    >
      <Box sx={{ width: cardStyles.minWidth, height: '100%', position: 'relative', overflow: 'hidden' }}>

      {loaded && !error && (<Box
        sx={{
          position: 'absolute',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          backgroundImage: `url(${game.image})`,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          filter: 'blur(10px)', // Ajustez la valeur du flou selon vos besoins
          zIndex: 0, // Derrière l'image principale
        }}
      />)}

        <CardActionArea
          component={Link}
          to={"/game/" + game.nom}
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
            height: cardImageSize,
            // borderTopLeftRadius: 5,
            // borderTopRightRadius: 5,
            borderRadius: 1,
            overflow: 'hidden',
           }}
          >
            {!loaded && !error && (
              <Skeleton variant="rectangular" width="100%" height={cardImageSize} />
            )}
            {!error && (
              <CardMedia
                component="img"
                height={cardImageSize}
                // image={game.image}
                image={game.image}
                alt={game.image_alt}
                onLoad={handleImageLoad}
                onError={handleImageError}
                sx={{ display: loaded ? 'block' : 'none', objectFit: 'cover', width: '100%' }}
              />
            )}
            {error && (
              <Box
                height={cardImageSize}
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
            }}
                  >
            <Stack spacing={0.2} >
                <Stack sx={{  }}>
                  <Typography variant="h5" component="div" 
                      sx={{
                          textalign: 'center',
                          alignItems: 'center',
                          fontSize: titleSize,
                          whiteSpace: 'nowrap',
                          textAlign: 'center'
                      }}
                  >
                      {game.nom}
                  </Typography>
                  
				
                  <Stack direction={"column"} spacing={0.7} sx={{alignItems: 'center', justifyContent: 'space-between', my: 0.5 }}>
                      {game.categories && 
                        <Stack
                          direction={'row'}
                        >
                        {game.categories.map((categorie) => (
                          <Typography variant="subtitle1" component="div" 
                          key={categorie}
                          sx={{
                              fontSize: subTitleSize,
                              lineHeight: subTitleLineHeight,
                              alignItems: 'center',
                              '&:not(:last-child):after' : {
                                content : '"|"',
                                mx : 0.5
                              }
                          }}
                          >
                            {categorie}
                          </Typography>
                        ))}
                        </Stack>
                        }

                    <Typography variant="subtitle1" component="div" 
                      sx={{
                          fontSize: subTitleSize,
                          lineHeight: subTitleLineHeight,
                          alignItems: 'center',
                      }}
                      >
                      
                      5 Parties
                    </Typography>
                  </Stack>




                </Stack>

                
                <Typography
                  variant="body2"
                  sx={{
                      color: 'text.secondary',
                      display: '-webkit-box',
                      WebkitBoxOrient: 'vertical',
                      WebkitLineClamp: 3,
                      overflow: 'hidden',
                      textOverflow: 'ellipsis',
                      fontSize: commentSize,
                  }}
                >
                  {game.short_coment ? game.short_coment : game.description}
                </Typography>
            </Stack>

          </CardContent>

            
        </CardActionArea>

        
      </Box>

      <Collapse
        in={open}
        orientation="horizontal"
        collapsedSize={0}
        sx={{
          display: open ? 'flex' : 'none',
          alignItems: 'center',
          transformOrigin: 'left center',
        }}
      >
        {/* 4) ref ici */}
        <Box
          // ref={collapseRef}
          sx={{
            width: 250,
            p: 2,
            // backgroundColor: 'grey.100',
            height: '100%',
          }}
        >
          <Typography
            variant="body2"
            sx={{
              color: 'text.secondary',
              display: '-webkit-box',
              WebkitBoxOrient: 'vertical',
              WebkitLineClamp: 14,
              overflow: 'hidden',
              textOverflow: 'ellipsis',
              fontSize: subTitleSize,
            }}
          >
            {game.description}
          </Typography>
        </Box>
      </Collapse>
    </Card>
  );
};

export default GameCard;
