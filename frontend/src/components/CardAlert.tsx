import React, { ReactNode } from 'react';
import { Card, CardContent, Typography, Button } from '@mui/material';
import InfoOutlinedIcon from '@mui/icons-material/InfoOutlined';
import AutoAwesomeRoundedIcon from '@mui/icons-material/AutoAwesomeRounded';
import PriorityHighOutlinedIcon from '@mui/icons-material/PriorityHighOutlined';
import WarningAmberOutlinedIcon from '@mui/icons-material/WarningAmberOutlined';
import ErrorOutlineOutlinedIcon from '@mui/icons-material/ErrorOutlineOutlined';

export type AlertType = 'INFO' | 'ACTION_NEEDED' | 'WARNING' | 'ALERT' | 'NEW';

export interface CardAlertProps {
  /** Type d’alerte, qui détermine l’icône et le style */
  type: AlertType;
  /** Titre principal (toujours affiché) */
  title: string;
  /** Texte secondaire (optionnel) */
  description?: string;
  /** Contenu custom (optionnel), peut être n’importe quel ReactNode */
  children?: ReactNode;
  /** URL pour le bouton (optionnel) */
  btnLink?: string;
  /** Text pour le bouton (optionnel) */
  btnText?: string;
}

const iconMap: Record<AlertType, ReactNode> = {
  INFO:     <InfoOutlinedIcon     color="info"     fontSize="small" />,
  ACTION_NEEDED: <PriorityHighOutlinedIcon color="secondary" fontSize="small" />,
  WARNING:  <WarningAmberOutlinedIcon color="warning"  fontSize="small" />,
  ALERT:    <ErrorOutlineOutlinedIcon color="error"    fontSize="small" />,
  NEW:    <AutoAwesomeRoundedIcon fontSize="small" />,
};

const CardAlert: React.FC<CardAlertProps> = ({
  type = 'INFO',
  title,
  description,
  children,
  btnLink,
  btnText,
}) => {
  const Icon = iconMap[type];
  //TODO: Ajout d'un bouton pour fermer
  return (
    <Card variant="outlined" sx={{ m: 1.5, flexShrink: 0 }}>
      <CardContent>
        {/* Icône selon le type */}
        {Icon}

        {/* Titre */}
        <Typography gutterBottom sx={{ fontWeight: 600 }}>
          {title}
        </Typography>

        {/* Description, si fournie */}
        {description && (
          <Typography variant="body2" sx={{ mb: 2, color: 'text.secondary' }}>
            {description}
          </Typography>
        )}

        {/* Contenu React custom, si fourni */}
        {children}

        {/* Bouton lien, si URL donnée */}
        {btnLink && (
          <Button
            component="a"
            href={btnLink}
            target="_blank"
            rel="noopener noreferrer"
            variant="contained"
            size="small"
            fullWidth
            sx={{ mt: 1 }}
          >
            {btnText ? btnText : 'En savoir plus'}
          </Button>
        )}
      </CardContent>
    </Card>
  );
};

export default CardAlert;
