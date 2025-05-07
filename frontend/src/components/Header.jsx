import * as React from 'react';
import Stack from '@mui/material/Stack';
import NotificationsRoundedIcon from '@mui/icons-material/NotificationsRounded';
import CustomDatePicker from './CustomDatePicker';
import NavbarBreadcrumbs from './NavbarBreadcrumbs';
import MenuButton from './MenuButton';
import ColorModeIconDropdown from '../shared-theme/ColorModeIconDropdown';

import Search from './Search';
import { Typography } from '@mui/material';

export default function Header() {
  return (
    <Stack
      direction="column"
    >
      <Stack
        direction="row"
        sx={{
          display: { xs: 'none', md: 'flex' },
          width: '100%',
          alignItems: { xs: 'flex-start', md: 'center' },
          justifyContent: 'space-between',
          maxWidth: { sm: '100%', md: '1700px' },
          pt: 1.5,
        }}
        spacing={2}
      >
        <Typography
          variant="h1"
          sx={{
            fontFamily: 'Ravenholm',
            fontWeight: 700,
          }}
        >
          Les Batisseurs de rève
        </Typography>

        <Stack direction="row" sx={{ gap: 1 }}>
          <Search />
          <CustomDatePicker />
          <MenuButton showBadge aria-label="Open notifications">
            <NotificationsRoundedIcon />
          </MenuButton>
          <ColorModeIconDropdown />
        </Stack>
      </Stack>
      

      <NavbarBreadcrumbs />
    </Stack>
  );
}
