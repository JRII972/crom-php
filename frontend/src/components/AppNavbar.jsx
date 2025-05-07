import * as React from 'react';

import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import AppBar from '@mui/material/AppBar';
import { styled } from '@mui/material/styles';
import MuiToolbar from '@mui/material/Toolbar';
import { tabsClasses } from '@mui/material/Tabs';
import Typography from '@mui/material/Typography';
import MenuRoundedIcon from '@mui/icons-material/MenuRounded';
import DashboardRoundedIcon from '@mui/icons-material/DashboardRounded';

import MenuButton from './MenuButton';
import SideMenuMobile from './SideMenuMobile';
import ColorModeIconDropdown from '../shared-theme/ColorModeIconDropdown';
import { LogoIcon } from '../utils/utils';

const Toolbar = styled(MuiToolbar)({
  width: '100%',
  padding: '12px',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'start',
  justifyContent: 'center',
  gap: '12px',
  flexShrink: 0,
  [`& ${tabsClasses.flexContainer}`]: {
    gap: '8px',
    p: '8px',
    pb: 0,
  },
});

export default function AppNavbar() {
  const [open, setOpen] = React.useState(false);

  const toggleDrawer = (newOpen) => () => {
    setOpen(newOpen);
  };

  return (
    <AppBar
      position="fixed"
      sx={{
        display: { xs: 'auto', md: 'none' },
        boxShadow: 0,
        bgcolor: 'background.paper',
        backgroundImage: 'none',
        borderBottom: '1px solid',
        borderColor: 'divider',
        top: 'var(--template-frame-height, 0px)',
      }}
    >
      <Toolbar variant="regular">
        <Stack
          direction="row"
          sx={{
            alignItems: 'center',
            flexGrow: 1,
            width: '100%',
            gap: 1,
            justifyContent: 'space-between'
          }}
        >
          <Stack
            direction="row"
            spacing={1}
            sx={{ justifyContent: 'center' }}
          >
            <LogoIcon />
          </Stack>
          
          <Stack
            direction="row"
            spacing={1}
            sx={{ justifyContent: 'center', m: 'auto' }}
          >            
          {/* TODO : Améliorer l'alignement */}
            <Typography variant="h4" component="h1" sx={{ 
              color: 'text.primary',
              fontFamily: 'Ravenholm',
              fontWeight: 700,
            }}>
              LBDR
            </Typography>            
          </Stack>

          <Box>
            <MenuButton aria-label="menu" onClick={toggleDrawer(true)}>
              <MenuRoundedIcon />
            </MenuButton>
            <SideMenuMobile open={open} toggleDrawer={toggleDrawer} />
          </Box>
        </Stack>
      </Toolbar>
    </AppBar>
  );
}


