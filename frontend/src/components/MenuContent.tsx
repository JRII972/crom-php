import React from 'react';
import { Stack, List, ListItem, ListItemButton, ListItemIcon, ListItemText } from '@mui/material';
import { Schedule, Dashboard, Event, List as ListIcon, HomeRounded, AnalyticsRounded, PeopleRounded, AssignmentRounded, SettingsRounded, InfoRounded, HelpRounded } from '@mui/icons-material';
import { NavLink, useLocation } from 'react-router-dom';

// Définition du type pour chaque entrée
interface MenuItem {
  text: string;
  icon: React.ReactNode;
  to: string;           // la route associée
}

const mainListItems: MenuItem[] = [
  { text: 'Acceuil',      icon: <HomeRounded />,      to: '/' },
  { text: 'Mes parties',   icon: <Event />,    to: '/mes-parties', children : [
      {
        section: 'Info',
        item: [
          { text: 'Mes disponibilités',     icon: <Schedule />, to: '/disponibilité' },
        ]
      },
    ] 
  },
  { text: 'Parties', icon: <ListIcon />, to: '/parties', children : [
      {
        section: 'Type',
        item: [
          { text: 'Campagne', icon: <ListIcon />, to: '/campagne' },
          { text: 'OneShot', icon: <ListIcon />, to: '/oneshot' },
          { text: 'Event', icon: <ListIcon />, to: '/event' },
        ]
      },
      {
        section: 'Catégorie',
        item: [
          { text: 'Horreur', icon: <ListIcon />, to: '/horreur' },
          { text: 'Action', icon: <ListIcon />, to: '/action' },
          { text: 'Fantasy', icon: <ListIcon />, to: '/fantasy' },
        ]
      },
    ] 
  },
  
  { text: 'Administration',     icon: <Schedule />, to: '/admin', children : [
      {
        section: 'Site',
        item: [
          { text: 'Paramêtre',  icon: <SettingsRounded />,  to: '/settings' },
          { text: 'Dashboard',  icon: <Dashboard />,  to: '/dashboard' },
          { text: 'About',     icon: <InfoRounded />,      to: '/about' },
          { text: 'Feedback',  icon: <HelpRounded />,      to: '/feedback' },
        ]
      },
    ] 
  },
];

const secondaryListItems: MenuItem[] = [
  { text: 'Paramêtre',  icon: <SettingsRounded />,  to: '/settings' },
  { text: 'Dashboard',  icon: <Dashboard />,  to: '/dashboard' },
  { text: 'About',     icon: <InfoRounded />,      to: '/about' },
  { text: 'Feedback',  icon: <HelpRounded />,      to: '/feedback' },
];

export default function MenuContent() {
  const { pathname } = useLocation();

  return (
    <Stack sx={{ flexGrow: 1, p: 1, justifyContent: 'space-between' }}>
      <List dense>
        {mainListItems.map((item) => (
          <ListItem key={item.to} disablePadding sx={{ display: 'block' }}>
            <ListItemButton
              component={NavLink}
              to={item.to}
              selected={pathname === item.to}
              sx={{
                display: 'flex',
                '&.active': {
                  backgroundColor: theme => theme.palette.action.selected,
                },
              }}
            >
              <ListItemIcon>{item.icon}</ListItemIcon>
              <ListItemText primary={item.text} />
            </ListItemButton>
          </ListItem>
        ))}
      </List>

      <List dense>
        {secondaryListItems.map((item) => (
          <ListItem key={item.to} disablePadding sx={{ display: 'block' }}>
            <ListItemButton
              component={NavLink}
              to={item.to}
              selected={pathname === item.to}
              sx={{
                display: 'flex',
                '&.active': {
                  backgroundColor: theme => theme.palette.action.selected,
                },
              }}
            >
              <ListItemIcon>{item.icon}</ListItemIcon>
              <ListItemText primary={item.text} />
            </ListItemButton>
          </ListItem>
        ))}
      </List>
    </Stack>
  );
}
