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
  useTheme, useColorScheme
} from '@mui/material';
import BrokenImageIcon from '@mui/icons-material/BrokenImage';
import GameSession from '../../types/GameSession';
import { Link } from 'react-router-dom';

import { findGameByName } from '../data/games';
import { PartyCardContent } from './PartyCardContent';


interface PartyCardProps {
  partie: GameSession;
  type?: 'session' | 'game' | 'party';
  displayDate?: boolean;
}


const PartyCard: FC<PartyCardProps> = ({ partie, type='session', displayDate=false }) => {
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
    height: type == 'session' ? (isMobileScreen ? 300 : 350) : (isMobileScreen ? 300 : 280),
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
      key={partie.id}
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
          backgroundImage: `url(${findGameByName(partie.jeu).image})`,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          filter: 'blur(10px)', // Ajustez la valeur du flou selon vos besoins
          zIndex: 0, // Derrière l'image principale
        }}
      />)}

        <CardActionArea
          component={Link}
          to={"./partie/" + partie.id}
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
                // image={partie.image}
                image={findGameByName(partie.jeu).image}
                alt={partie.image_alt}
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

          
          <PartyCardContent partie={partie} cardMinWidth={cardStyles.minWidth} type={type} displayDate={displayDate}/>

            
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
            {partie.coment}
          </Typography>
        </Box>
      </Collapse>
    </Card>
  );
};

export default PartyCard;
