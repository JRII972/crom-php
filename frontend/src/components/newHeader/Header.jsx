import * as React from 'react';

import { NavLink, useLocation } from 'react-router-dom';

import Stack from '@mui/material/Stack';
import NotificationsRoundedIcon from '@mui/icons-material/NotificationsRounded';
import Typography from '@mui/material/Typography';
import Popper from '@mui/material/Popper';
import AppBar from '@mui/material/AppBar';
import MenuItem from '@mui/material/MenuItem';
import Button from '@mui/material/Button';
import MenuList from '@mui/material/MenuList';
import Box from '@mui/material/Box';
import Paper from '@mui/material/Paper';
import ListIcon from '@mui/icons-material/List';
import Schedule from '@mui/icons-material/Schedule';
import SettingsRounded from '@mui/icons-material/SettingsRounded';
import Dashboard from '@mui/icons-material/Dashboard';
import InfoRounded from '@mui/icons-material/InfoRounded';
import HelpRounded from '@mui/icons-material/HelpRounded';
import ArrowDropDown from '@mui/icons-material/ArrowDropDown';
import Slide from '@mui/material/Slide';
import useScrollTrigger from '@mui/material/useScrollTrigger';
import CssBaseline from '@mui/material/CssBaseline';

import CustomDatePicker from './CustomDatePicker';
import MenuButton from './MenuButton';
import ColorModeIconDropdown from '../shared-theme/ColorModeIconDropdown';
import Search from './Search';
import { useTheme } from '@emotion/react';
import { useMediaQuery } from '@mui/material';

const mainListItems = [
  { text: 'Acceuil', to: '/' },
  { text: 'Mes parties', to: '/mes-parties', children: [
      { section: 'Info', item: [
          { text: 'Mes disponibilités', to: '/disponibilité' },
        ]
      },
    ] 
  },
  { text: 'Parties', to: '/parties', children: [
      { section: 'Type', item: [
          { text: 'Campagne', to: '/campagne' },
          { text: 'OneShot', to: '/oneshot' },
          { text: 'Event', to: '/event' },
        ]
      },
      { section: 'Catégorie', item: [
          { text: 'Horreur', to: '/horreur' },
          { text: 'Action', to: '/action' },
          { text: 'Fantasy', to: '/fantasy' },
        ]
      },
    ] 
  },
  { text: 'Administration', to: '/admin', children: [
      { section: 'Site', item: [
          { text: 'Paramêtre', icon: <SettingsRounded />, to: '/settings' },
          { text: 'Dashboard', icon: <Dashboard />, to: '/dashboard' },
          { text: 'About', icon: <InfoRounded />, to: '/about' },
          { text: 'Feedback', icon: <HelpRounded />, to: '/feedback' },
        ]
      },
    ] 
  },
];

function HideOnScroll(props) {
  const { children, window } = props;
  // Note that you normally won't need to set the window ref as useScrollTrigger
  // will default to window.
  // This is only being set here because the demo is in an iframe.
  const trigger = useScrollTrigger({
    target: window ? window() : undefined,
  });

  return (
    <Slide appear={false} direction="down" in={!trigger}>
      {children ?? <div />}
    </Slide>
  );
}

export default function Header({ noHeader, noSearch }) {
  const theme = useTheme();
  const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));
  
  const [anchorEl, setAnchorEl] = React.useState(null);
  const [openMenu, setOpenMenu] = React.useState(null);
  const [timeoutId, setTimeoutId] = React.useState(null);

  const handleMenuClick = (event, index) => {
    if (timeoutId) {
      clearTimeout(timeoutId);
      setTimeoutId(null);
    }
    setAnchorEl(event.currentTarget);
    setOpenMenu(index);
  };

  const handleMenuClose = () => {
    const id = setTimeout(() => {
      setAnchorEl(null);
      setOpenMenu(null);
      setTimeoutId(null);
    }, 200);
    setTimeoutId(id);
  };

  const handlePopperMouseEnter = (index) => {
    if (timeoutId) {
      clearTimeout(timeoutId);
      setTimeoutId(null);
    }
    setOpenMenu(index);
  };

  const handlePopperMouseLeave = () => {
    handleMenuClose();
  };

  if (isMobileScreen) { return(<></>) }

  return (
    <>
    <HideOnScroll>
      <AppBar position="fixed">
          <Stack direction="row" spacing={2} sx={{ alignItems: 'center' }}>
            {mainListItems.map((item, index) => (
              <div key={index}>
                <Button
                  component={NavLink}
                  to={item.to}
                  onMouseEnter={(e) => handleMenuClick(e, index)}
                  onMouseLeave={handleMenuClose}
                  sx={{ 
                    color: 'inherit', 
                    textTransform: 'none',
                    display: 'flex',
                    alignItems: 'center',
                    gap: 0.5,
                    zIndex: 2,
                  }}
                >
                  {item.text} 
                  {item.icon && item.icon}
                  {item.children && (
                    <ArrowDropDown 
                      sx={{ 
                        transition: 'transform 0.2s ease',
                        transform: openMenu === index ? 'rotate(180deg)' : 'rotate(0deg)'
                      }} 
                    />
                  )}
                </Button>

                
              </div>
            ))}
          </Stack>

          <Stack direction="row" sx={{ gap: 1 }}>
            {!noSearch && <Search />}
            <CustomDatePicker />
            <MenuButton showBadge aria-label="Open notifications">
              <NotificationsRoundedIcon />
            </MenuButton>
            <ColorModeIconDropdown />
          </Stack>
          
      </AppBar>
    </HideOnScroll>

    {mainListItems.map((item, index) => (
      <Box key={index}
      >
        {item.children && 
          <Popper
            open={openMenu === index}
            anchorEl={anchorEl}
            placement="bottom-start"
            disablePortal
            transition
            modifiers={[
              {
                name: 'offset',
                options: { offset: [0, 8] },
              },
            ]}
            sx={{
              width: '100%',
              zIndex: 1,
            }}
            onMouseEnter={() => handlePopperMouseEnter(index)}
            onMouseLeave={handlePopperMouseLeave}
          >
            {({ TransitionProps }) => (
              <Slide {...TransitionProps} direction="down" timeout={350}>
                <Paper sx={{ p: 1, bgcolor: 'background.paper', pl: 12 }}>
                  <Stack direction='row'>
                    {item.children.map((section, secIndex) => (
                      <div key={secIndex}>
                        <Typography variant="subtitle2" sx={{ fontWeight: 'bold' }}>
                          {section.section}
                        </Typography>
                        <MenuList dense>
                          {section.item.map((subItem, subIndex) => (
                            <MenuItem key={subIndex} onClick={handleMenuClose}>
                              {subItem.text} {subItem.icon && subItem.icon}
                            </MenuItem>
                          ))}
                        </MenuList>
                      </div>
                    ))}
                  </Stack>
                </Paper>
              </Slide>
            )}
          </Popper>
        }
      </Box>
    ))}

    </>
  );
}