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
import { Session } from '../../api/types/db';
import { Link } from 'react-router-dom';
import { PartyCardContent } from './PartyCardContent';
import { default_image } from '../utils/default_image';

// Interface mise à jour pour utiliser Session
interface PartyCardProps {
  session: Session;
  type?: 'session' | 'game' | 'party';
  displayDate?: boolean;
}

const PartyCard: FC<PartyCardProps> = ({ session, type='session', displayDate=false }) => {
  // états
  const [loaded, setLoaded] = useState<boolean>(false);
  const [error, setError] = useState<boolean>(false);
  const [open, setOpen] = useState<boolean>(false);
  const theme = useTheme();
  const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));

  const { mode, systemMode } = useColorScheme();
  const actualMode = systemMode || mode;

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
    height: type === 'session' ? (isMobileScreen ? 300 : 350) : (isMobileScreen ? 300 : 280),
    display: 'flex',
    flexDirection: 'row',
    alignItems: 'stretch',
    maxWidth: isMobileScreen ? 160 : 550,
    p: 0,
  };

  const cardImageSize = isMobileScreen ? 100 : 140;
  const subTitleSize = isMobileScreen ? '0.8em' : '0.9em';

  // Récupérer l'image appropriée
  const getImage = () => {
    if (session.partie.image && session.partie.image.url) {
      return session.partie.image.url;
    } else if (session.partie.jeu.image && session.partie.jeu.image.url) {
      return session.partie.jeu.image.url;
    } else {
      return default_image(session.partie.type_partie);
    }
  };

  // Récupérer le texte alternatif approprié
  const getImageAlt = () => {
    if (session.partie.image && session.partie.image.imageAlt) {
      return session.partie.image.imageAlt;
    } else if (session.partie.jeu.image && session.partie.jeu.image.imageAlt) {
      return session.partie.jeu.image.imageAlt;
    } else {
      return 'Image par défaut pour ' + session.partie.type_partie;
    }
  };

  const imageUrl = getImage();

  return (
    <Card
      sx={cardStyles}
      key={session.id}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      variant={actualMode === 'light' ? 'outlined' : ''}
    >
      <Box sx={{ width: cardStyles.minWidth, height: '100%', position: 'relative', overflow: 'hidden' }}>
        {loaded && !error && (
          <Box
            sx={{
              position: 'absolute',
              top: 0,
              left: 0,
              width: '100%',
              height: '100%',
              backgroundImage: `url(${imageUrl})`,
              backgroundSize: 'cover',
              backgroundPosition: 'center',
              filter: 'blur(10px)',
              zIndex: 0,
            }}
          />
        )}

        <CardActionArea
          component={Link}
          to={"/partie/" + session.partie.id}
          sx={{
            flex: 1,
            height: '100%',
            display: 'flex',
            flexDirection: 'column',
          }}
        >
          <Box
            sx={{
              m: 1,
              height: cardImageSize,
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
                image={imageUrl}
                alt={getImageAlt()}
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
          
          <PartyCardContent session={session} cardMinWidth={cardStyles.minWidth} type={type} displayDate={displayDate} />
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
        <Box
          sx={{
            width: 250,
            p: 2,
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
            {session.partie.description}
          </Typography>
        </Box>
      </Collapse>
    </Card>
  );
};

export default PartyCard;
