import * as React from 'react';

import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Avatar from '@mui/material/Avatar';
import Divider from '@mui/material/Divider';
import MuiAvatar from '@mui/material/Avatar';
import { styled } from '@mui/material/styles';
import MenuItem from '@mui/material/MenuItem';
import Typography from '@mui/material/Typography';
import ListItemText from '@mui/material/ListItemText';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListSubheader from '@mui/material/ListSubheader';
import AddRoundedIcon from '@mui/icons-material/AddRounded';
import MuiListItemAvatar from '@mui/material/ListItemAvatar';
import Select, { selectClasses } from '@mui/material/Select';
import MuiDrawer, { drawerClasses } from '@mui/material/Drawer';
import DevicesRoundedIcon from '@mui/icons-material/DevicesRounded';
import SmartphoneRoundedIcon from '@mui/icons-material/SmartphoneRounded';
import ConstructionRoundedIcon from '@mui/icons-material/ConstructionRounded';

import CardAlert from './CardAlert';
import MenuContent from './MenuContent';
import OptionsMenu from './OptionsMenu';
import SideMenuUser from './SideMenuUser';
import SelectContent from './SelectContent';
import ListItemAvatar from '@mui/material/ListItemAvatar';

const drawerWidth = 240;

const Drawer = styled(MuiDrawer)({
  width: drawerWidth,
  flexShrink: 0,
  boxSizing: 'border-box',
  mt: 10,
  [`& .${drawerClasses.paper}`]: {
    width: drawerWidth,
    boxSizing: 'border-box',
  },
});

export default function SideMenu() {
  return (
    <Drawer
      variant="permanent"
      sx={{
        display: { xs: 'none', md: 'block' },
        [`& .${drawerClasses.paper}`]: {
          backgroundColor: 'background.paper',
        },
      }}
    >
      <Box
        sx={{
          display: 'flex',
          mt: 'calc(var(--template-frame-height, 0px) + 4px)',
          p: 1.5,
        }}
      >
        {/* <SideMenuUser /> */}
        <SelectContent />
        {/* <MenuItem value="">
          <ListItemAvatar>
            <Avatar alt="Sitemark web">
              <DevicesRoundedIcon sx={{ fontSize: '1rem' }} />
            </Avatar>
          </ListItemAvatar>
          <ListItemText primary="Sitemark-web" secondary="Web app" />
        </MenuItem> */}
      </Box>
      <Divider />
      <Box
        sx={{
          overflow: 'auto',
          height: '100%',
          display: 'flex',
          flexDirection: 'column',
        }}
      >
        <MenuContent />
        <CardAlert
          type="NEW"
          title="Bienvenue"
          description="Votre nouveau CROM v2.0 Beta"
          // content={<MyCustomChart />}
          // btnLink="https://example.com/upgrade"
        />
      </Box>
      <SideMenuUser />
    </Drawer>
  );
}
