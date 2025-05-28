import * as React from 'react';
import { styled } from '@mui/material/styles';
import Link from '@mui/material/Link';
import Typography from '@mui/material/Typography';
import Breadcrumbs, { breadcrumbsClasses } from '@mui/material/Breadcrumbs';
import NavigateNextRoundedIcon from '@mui/icons-material/NavigateNextRounded';
import { Link as RouterLink, useMatches } from 'react-router-dom';

const StyledBreadcrumbs = styled(Breadcrumbs)(({ theme }) => ({
  margin: theme.spacing(1, 0),
  [`& .${breadcrumbsClasses.separator}`]: {
    color: (theme.vars || theme).palette.action.disabled,
    margin: 1,
  },
  [`& .${breadcrumbsClasses.ol}`]: {
    alignItems: 'center',
  },
}));

export default function NavbarBreadcrumbs() {
  // Récupère la liste des routes actives, dans l’ordre
  const matches = useMatches();

  return (
    <StyledBreadcrumbs
      aria-label="breadcrumb"
      separator={<NavigateNextRoundedIcon fontSize="small" />}
    >
      {matches.map((match, index) => {
        // Si la route n’a pas de handle.breadcrumb, on l’ignore
        const crumb = match.handle?.breadcrumb;
        if (!crumb) return null;

        // breadcrumb peut être une string ou une fonction (pour les params dynamiques)
        const label =
          typeof crumb === 'function'
            ? crumb(match)
            : crumb;

        // chemin complet jusqu’à cette route
        const to = match.pathname;

        const isLast = index === matches.length - 1;

        return isLast ? (
          // dernier élément : simple texte
          <Typography
            key={to}
            variant="body1"
            sx={{ color: 'text.primary', fontWeight: 600 }}
          >
            {label}
          </Typography>
        ) : (
          // lien vers la route
          <Link
            key={to}
            component={RouterLink}
            to={to}
            variant="body1"
            underline="hover"
            sx={{ color: 'inherit' }}
          >
            {label}
          </Link>
        );
      })}
    </StyledBreadcrumbs>
  );
}
