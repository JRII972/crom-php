import * as React from 'react';

import SearchRoundedIcon from '@mui/icons-material/SearchRounded';
import ViewListIcon from '@mui/icons-material/ViewList';
import ViewModuleIcon from '@mui/icons-material/ViewModule';
import FormControl from '@mui/material/FormControl';
import InputAdornment from '@mui/material/InputAdornment';
import OutlinedInput from '@mui/material/OutlinedInput';
import ToggleButton from '@mui/material/ToggleButton';
import ToggleButtonGroup from '@mui/material/ToggleButtonGroup';


import DateRangeIcon from '@mui/icons-material/DateRange';
import { Divider, Fab, Stack, useMediaQuery, useTheme } from '@mui/material';
import { parties } from './data/parties';
import PartiesCard from './PartiesCards';
import PartiesRecomendations from './PartiesRecomendations';
import PartiesTables from './PartiesTables';
import FilterAltIcon from '@mui/icons-material/FilterAlt';


export default function PartyTab(props) {
    
  const theme = useTheme();
  const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));
  const [view, setView] = React.useState(localStorage.getItem( 'partyPageSate' ) || 'card');

  const handleChange = (event, nextView) => {
      setView(nextView);
      localStorage.setItem( 'partyPageSate', nextView );
  };

  const control = {
    value: view,
    onChange: handleChange,
    exclusive: true,
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
  <FormControl 
    sx={{ 
      width: '100%',
      display: 'inline-flex',
      flexDirection: 'row',
      gap: '1em'
     }} 
    variant="outlined">

      <OutlinedInput
        size="small"
        id="search"
        placeholder="Rechercher une partie…"
        sx={{ flexGrow: 1 }}
        startAdornment={
          <InputAdornment position="start" sx={{ color: 'text.primary' }}>
            <SearchRoundedIcon fontSize="small" />
          </InputAdornment>
        }
        inputProps={{
          'aria-label': 'search',
        }}
      />

      <ToggleButtonGroup size="small" {...control} >
        <ToggleButton  value="table" aria-label="table">
            <ViewListIcon />
        </ToggleButton>
        <ToggleButton value="card" aria-label="card">
            <ViewModuleIcon />
        </ToggleButton>
      </ToggleButtonGroup>

      <Stack direction='row' spacing={1}>
        <Fab color="primary" variant="extended" sx={{width: 'fit-content', fontSize:"small"}} size='small'>
          <DateRangeIcon sx={{ mr: 0.5}} fontSize="small" />
          Jours
        </Fab>
        {/* <Fab variant="extended" sx={{width: 'fit-content', fontSize:"small"}} size='small'>
          <CategoryIcon sx={{ mr: 0.5}} fontSize="small" />
          Type
        </Fab> */}
        <Fab color="secondary" size='small'>
          <FilterAltIcon />
          {/* Filtre */}
        </Fab>
      </Stack>
    </FormControl>
            

      <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem  />
      

      { view=='table' && <PartiesTables {...parties}/> }
      { view=='card' && <PartiesCard {...parties}/> }
      { view=='quilt' && <PartiesRecomendations {...parties}/> }
      {/* { view=='quilt' && <TableAccordillon {...parties}/> } */}
  
  </Stack>
    );
}

