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
import FilterAltIcon from '@mui/icons-material/FilterAlt';
import { Divider, Fab, Stack, useMediaQuery, useTheme, Popper, Paper, MenuItem, MenuList, ClickAwayListener, Checkbox, ListItemText, Typography, Switch } from '@mui/material';
import { parties } from '../data/parties';
import PartiesCard from '../PartiesCards';
import PartiesRecomendations from '../PartiesRecomendations';
import PartiesTables from '../PartiesTables';


export default function mainFilter(control, selectedDays, selectedFilters, handleDaySelect, handleFilterSelect, handleOuverteToggle) {
  const theme = useTheme();
  const isMobileScreen = useMediaQuery(theme.breakpoints.down('md'));
  
  const [dayAnchorEl, setDayAnchorEl] = React.useState(null);
  const [filterAnchorEl, setFilterAnchorEl] = React.useState(null);
  
  const days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
  const filterOptions = {
    categories: ['Débutant', 'Intermédiaire', 'Avancé'],
    type: ['Campagne', 'Oneshot', 'Even'],
    lieu: ['FSC', 'MDA'],
  };

  const handleDayClick = (event) => {
    setDayAnchorEl(dayAnchorEl ? null : event.currentTarget);
  };

  const handleFilterClick = (event) => {
    setFilterAnchorEl(filterAnchorEl ? null : event.currentTarget);
  };

  const handleClose = () => {
    setDayAnchorEl(null);
    setFilterAnchorEl(null);
  };  

    
  return <FormControl
    sx={{
      width: '100%',
      display: 'inline-flex',
      flexDirection: 'row',
      gap: '1em'
    }}
    variant="outlined"
  >
    <OutlinedInput
      size="small"
      id="search"
      placeholder="Rechercher une partie…"
      sx={{ flexGrow: 1 }}
      startAdornment={<InputAdornment position="start" sx={{ color: 'text.primary' }}>
        <SearchRoundedIcon fontSize="small" />
      </InputAdornment>}
      inputProps={{
        'aria-label': 'search',
      }} />

    <ToggleButtonGroup size="small" {...control}>
      <ToggleButton value="table" aria-label="table">
        <ViewListIcon />
      </ToggleButton>
      <ToggleButton value="card" aria-label="card">
        <ViewModuleIcon />
      </ToggleButton>
    </ToggleButtonGroup>

    <Stack direction='row' spacing={1}>
      <Fab
        color="primary"
        variant="extended"
        sx={{ width: 'fit-content', fontSize: "small" }}
        size='small'
        onClick={handleDayClick}
      >
        <DateRangeIcon sx={{ mr: 0.5 }} fontSize="small" />
        Jours
      </Fab>

      <Fab
        color="secondary"
        size='small'
        onClick={handleFilterClick}
      >
        <FilterAltIcon />
      </Fab>
    </Stack>


    <Popper
      open={Boolean(dayAnchorEl)}
      anchorEl={dayAnchorEl}
      placement="bottom-start"
      sx={{ zIndex: 1300 }}
    >
      <ClickAwayListener onClickAway={handleClose}>
        <Paper elevation={3}>
          <MenuList dense>
            {days.map((day) => (
              <MenuItem key={day} onClick={() => handleDaySelect(day)}>
                <Checkbox checked={selectedDays.includes(day)} />
                <ListItemText primary={day} />
              </MenuItem>
            ))}
          </MenuList>
        </Paper>
      </ClickAwayListener>
    </Popper>

    <Popper 
        open={Boolean(filterAnchorEl)} 
        anchorEl={filterAnchorEl} 
        placement="bottom-start"
        sx={{ zIndex: 1300 }}
      >
        <ClickAwayListener onClickAway={handleClose}>
          <Paper elevation={3} sx={{ minWidth: 200 }}>
            <MenuList dense>

              <Typography variant="subtitle2" sx={{ px: 2, pt: 1 }}>Catégories</Typography>
              {filterOptions.categories.map((category) => (
                <MenuItem 
                  key={category} 
                  onClick={() => handleFilterSelect('categories', category)}
                >
                  <Checkbox checked={selectedFilters.categories.includes(category)} />
                  <ListItemText primary={category} />
                </MenuItem>
              ))}
              <Divider />

              <Typography variant="subtitle2" sx={{ px: 2, pt: 1 }}>Type</Typography>
              {filterOptions.type.map((type) => (
                <MenuItem 
                  key={type} 
                  onClick={() => handleFilterSelect('type', type)}
                >
                  <Checkbox checked={selectedFilters.type.includes(type)} />
                  <ListItemText primary={type} />
                </MenuItem>
              ))}
              <Divider />

              <Typography variant="subtitle2" sx={{ px: 2, pt: 1 }}>Lieu</Typography>
              {filterOptions.lieu.map((lieu) => (
                <MenuItem 
                  key={lieu} 
                  onClick={() => handleFilterSelect('lieu', lieu)}
                >
                  <Checkbox checked={selectedFilters.lieu.includes(lieu)} />
                  <ListItemText primary={lieu} />
                </MenuItem>
              ))}
              <Divider />

              <MenuItem onClick={handleOuverteToggle}>
                <Switch checked={selectedFilters.ouverte} />
                <ListItemText primary="Parties ouvertes" />
              </MenuItem>
            </MenuList>
          </Paper>
        </ClickAwayListener>
      </Popper>
  </FormControl>;
}


