import * as React from 'react';

import ToggleButton from '@mui/material/ToggleButton';
import ViewListIcon from '@mui/icons-material/ViewList';
import ViewQuiltIcon from '@mui/icons-material/ViewQuilt';
import ViewModuleIcon from '@mui/icons-material/ViewModule';
import ToggleButtonGroup from '@mui/material/ToggleButtonGroup';

import { parties } from './data/parties'
import PartiesTables from './PartiesTables';
import PartiesRecomendations from './PartiesRecomendations';
import MainPage from '../components/MainPage';
import PartiesCard from './PartiesCards'
import { Stack, useMediaQuery, useTheme } from '@mui/material';


export default function PartyTab(props) {
    
    const theme = useTheme();
    const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));
    const [view, setView] = React.useState(localStorage.getItem( 'partyPageSate' ) || 'card');

    const handleChange = (event, nextView) => {
        setView(nextView);
        localStorage.setItem( 'partyPageSate', nextView );
    };

    

    return (
        <Stack
            spacing={2}
            sx={{
            alignItems: 'center',
            px: 0,
            pb: 5,
            width: '100%',
            }}
        >
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
                <ToggleButton value="table" aria-label="table">
                    <ViewListIcon />
                </ToggleButton>
                <ToggleButton value="card" aria-label="card">
                    <ViewModuleIcon />
                </ToggleButton>
                <ToggleButton value="quilt" aria-label="quilt">
                    <ViewQuiltIcon />
                </ToggleButton>
                {/* <ToggleButton value="table" aria-label="table">
                <span class="material-symbols-outlined">table</span>
                </ToggleButton> */}
            </ToggleButtonGroup>

            { view=='table' && <PartiesTables {...parties}/> }
            { view=='card' && <PartiesCard {...parties}/> }
            { view=='quilt' && <PartiesRecomendations {...parties}/> }
            {/* { view=='quilt' && <TableAccordillon {...parties}/> } */}
        
        </Stack>
    );
}