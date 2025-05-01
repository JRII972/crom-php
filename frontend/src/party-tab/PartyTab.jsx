import * as React from 'react';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Checkbox from '@mui/material/Checkbox';
import CssBaseline from '@mui/material/CssBaseline';
import FormControlLabel from '@mui/material/FormControlLabel';
import Divider from '@mui/material/Divider';
import FormLabel from '@mui/material/FormLabel';
import FormControl from '@mui/material/FormControl';
import Link from '@mui/material/Link';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import Stack from '@mui/material/Stack';
import MuiCard from '@mui/material/Card';
import { styled } from '@mui/material/styles';
import AppTheme from '../shared-theme/AppTheme';

import SideMenu from './components/SideMenu';
import AppNavbar from './components/AppNavbar';
import Header from '../dashboard/components/Header';

import parties from './data/parties'

import ViewListIcon from '@mui/icons-material/ViewList';
import ViewModuleIcon from '@mui/icons-material/ViewModule';
import ViewQuiltIcon from '@mui/icons-material/ViewQuilt';
import ToggleButton from '@mui/material/ToggleButton';
import ToggleButtonGroup from '@mui/material/ToggleButtonGroup';
import TableMap from './table-map';
import PartiesCard from './parties-card';
import { List, Paper } from '@mui/material';





export default function SignIn(props) {

    const [view, setView] = React.useState(localStorage.getItem( 'partyPageSate' ) || 'list');

    const handleChange = (event, nextView) => {
        setView(nextView);
        localStorage.setItem( 'partyPageSate', nextView );
    };

    
    

    return (
            <AppTheme {...props}>
              {/* TODO:Fix this import and material symbol */}
              <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=table" />
              <CssBaseline enableColorScheme />
                <Box sx={{ display: 'flex' }}>
                    <SideMenu />
                    <AppNavbar /> {/* Mobiile Menu*/}
                    
                    {/* Main content */}
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
                        mx: 3,
                        pb: 5,
                        mt: { xs: 8, md: 0 },
                        }}
                    >
                        <Header/>  

                        <Typography variant="h4" component="h1" sx={{ mb: 2 }}>
                            Calendrier roliste à option multiple
                        </Typography>
                        
                        {/* <Paper elevation={3}>
                            <Box margin={1}>
                                <Typography variant='subtitle2'>
                                Horaires des séances de jeux des BDR
                                </Typography>
                                <List>
                                    <li><b>Vendredi</b> : de 19H15 à 1H00 au foyer saint Vincent - Orléans.</li>
                                    <li><b>Samedi</b> : de 14H00 à 20H00 à la Maison des associations d'Orléans.</li>
                                </List>
                            </Box>
                        </Paper> */}

                        <ToggleButtonGroup
                            orientation="horizontal"
                            value={view}
                            exclusive
                            onChange={handleChange}
                            >
                            <ToggleButton value="list" aria-label="list">
                                <ViewListIcon />
                            </ToggleButton>
                            <ToggleButton value="card" aria-label="card">
                                <ViewModuleIcon />
                            </ToggleButton>
                            <ToggleButton value="quilt" aria-label="quilt">
                                <ViewQuiltIcon />
                            </ToggleButton>
                            <ToggleButton value="table" aria-label="table">
                            <span class="material-symbols-outlined">table</span>
                            </ToggleButton>
                        </ToggleButtonGroup>

                        { view=='table' && <TableMap {...parties}/> }
                        { view=='card' && <PartiesCard {...parties}/> }
                        
                    </Stack>
                    </Box>
                </Box>  
            </AppTheme>
        
    );
}