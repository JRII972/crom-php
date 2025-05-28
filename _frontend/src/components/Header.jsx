import * as React from 'react';
import Stack from '@mui/material/Stack';
import NotificationsRoundedIcon from '@mui/icons-material/NotificationsRounded';
import CustomDatePicker from './CustomDatePicker';
import NavbarBreadcrumbs from './NavbarBreadcrumbs';
import MenuButton from './MenuButton';
import ColorModeIconDropdown from '../shared-theme/ColorModeIconDropdown';

import Search from './Search';
import { Typography } from '@mui/material';

export default function Header({noHeader, title}) {
  const noHeaderCSS = (noHeader ? {    
    pb: 5} : {})
  
  console.log(title)
  return (
      <Stack
        direction="row"
        sx={{
          display: { xs: 'none', md: 'flex' },
          width: '100%',
          alignItems: { xs: 'flex-start', md: (!noHeader ? 'center' : 'baseline')},
          justifyContent: 'space-between',
          maxWidth: { sm: '100%', md: '1700px' },
          pt: (!noHeader ? 1.5 : 3.5),
          ...noHeaderCSS
        }}
        spacing={2}
      >
        
        <NavbarBreadcrumbs />
      
        {noHeader && 
          <Typography variant="h2" component="h1" sx={{
            fontFamily: 'Ravenholm',
            fontWeight: 700,
            textAlign: 'center',
            mb: 2
          }}>
            {title}
          </Typography>
        }

        <Stack direction="row" sx={{ gap: 1 }}>
          {!noHeader && <Search /> }
          <CustomDatePicker />
          <MenuButton showBadge aria-label="Open notifications">
            <NotificationsRoundedIcon />
          </MenuButton>
          <ColorModeIconDropdown />
        </Stack>
      </Stack>
      
  );
}
