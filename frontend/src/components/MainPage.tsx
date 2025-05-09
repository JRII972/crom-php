import { Outlet } from 'react-router-dom';
import React, { ReactNode } from 'react';
import Box from '@mui/material/Box';
import CssBaseline from '@mui/material/CssBaseline';
import Typography from '@mui/material/Typography';
import Stack from '@mui/material/Stack';
import { alpha, useColorScheme, useTheme } from '@mui/material/styles';

import SideMenu from './SideMenu';
import AppNavbar from './AppNavbar';
import Header from './Header';
import { useCurrentMeta } from '../utils/utils';

export interface MainPageProps {
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
}) => {

  // const theme = useTheme();
  
  // // Access background.defaultChannel based on the theme mode
  // const { mode, systemMode, setMode } = useColorScheme();
  // console.log('Theme Mode:', mode);
  // console.log('System Mode:', systemMode);
  // const isDarkMode = theme.palette.mode === 'dark';
  // const backgroundDefaultChannel = theme.palette.background;
  // console.log('Background Default Channel:', backgroundDefaultChannel);

  // console.log('Theme Mode:', isDarkMode ? 'Dark' : 'Light');
  // console.log('Background Default Channel:', backgroundDefaultChannel);

  return (
    <>
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
          sx={{
            flexGrow: 1,
            bgcolor: 'background.lightPurple',
            overflow: 'auto',
          }}
        >
          <Stack
            spacing={2}
            sx={{
              alignItems: 'center',
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
    </>
  );
};

export default MainPage;
