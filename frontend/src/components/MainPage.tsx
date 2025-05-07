import { Outlet } from 'react-router-dom';
import React, { ReactNode } from 'react';
import Box from '@mui/material/Box';
import CssBaseline from '@mui/material/CssBaseline';
import Typography from '@mui/material/Typography';
import Stack from '@mui/material/Stack';
import { alpha } from '@mui/material/styles';

import AppTheme from '../shared-theme/AppTheme';
import SideMenu from './SideMenu';
import AppNavbar from './AppNavbar';
import Header from './Header';
import { useCurrentMeta } from '../utils/utils';

export interface MainPageProps extends React.ComponentProps<typeof AppTheme> {
  /** Titre affiché dans le header */
  title: string;
  /** État de connexion de l’utilisateur (ex. true si connecté) */
  connected: boolean;
  /** Contenu principal de la page */
  children?: ReactNode;
}

const MainPage: React.FC<MainPageProps> = ({
  title,
  connected,
  children,
  ...themeProps
}) => {
  return (
    <AppTheme {...themeProps}>
      {/* TODO: Fix this import and material symbol */}
      <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=table"
      />
      <CssBaseline enableColorScheme />
      <Box sx={{ display: 'flex' }}>
        <SideMenu />
        <AppNavbar />
        <Box
          component="main"
          sx={(theme) => ({
            flexGrow: 1,
            backgroundColor: theme.vars
              ? `rgba(${theme.vars.palette.background.defaultChannel} / 1)`
              : alpha(theme.palette.background.default, 1),
            overflow: 'auto',
          })}
        >
          <Stack
            spacing={2}
            sx={{
              alignItems: 'center',
              backgroundColor: '#fee',
              pb: 5,
              mt: { xs: 8, md: 0 },
            }}
          >
            <Header />

            <Typography variant="h2" component="h1" sx={{
              fontFamily: 'Ravenholm',
              fontWeight: 700,
              textAlign: 'center',
              mb: 2
            }}>
              {title ? title : useCurrentMeta('title')}
            </Typography>

            {children}
            <Outlet />
          </Stack>
        </Box>
      </Box>
    </AppTheme>
  );
};

export default MainPage;
