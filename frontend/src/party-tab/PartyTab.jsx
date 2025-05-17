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
import { Divider, Fab, Stack, useMediaQuery, useTheme, Popper, Paper, MenuItem, MenuList, ClickAwayListener, Checkbox, ListItemText } from '@mui/material';
import { parties } from './data/parties';
import PartiesCard from './PartiesCards';
import PartiesRecomendations from './PartiesRecomendations';
import PartiesTables from './PartiesTables';
import mainFilter from './components/MainFilter';

export default function PartyTab(props) {
  const [view, setView] = React.useState(localStorage.getItem('partyPageSate') || 'card');
  
  const [selectedDays, setSelectedDays] = React.useState([]);
  const [selectedFilters, setSelectedFilters] = React.useState({
    categories: [],
    type: [],
    lieu: [],
    ouverte: false,
  });

  
  const handleChange = (event, nextView) => {
    setView(nextView);
    localStorage.setItem('partyPageSate', nextView);
  };
  
  const handleDaySelect = (day) => {
    setSelectedDays(prev => 
      prev.includes(day) ? prev.filter(d => d !== day) : [...prev, day]
    );
  };

  const handleFilterSelect = (category, value) => {
    setSelectedFilters(prev => ({
      ...prev,
      [category]: prev[category].includes(value)
        ? prev[category].filter(v => v !== value)
        : [...prev[category], value],
    }));
  };

  const handleOuverteToggle = () => {
    setSelectedFilters(prev => ({
      ...prev,
      ouverte: !prev.ouverte,
    }));
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
      {mainFilter(control, selectedDays, selectedFilters, handleDaySelect, handleFilterSelect, handleOuverteToggle)}

      <Divider sx={{margin: '2em'}} orientation="horizontal" variant="middle" flexItem />

      {view === 'table' && <PartiesTables {...parties} />}
      {view === 'card' && <PartiesCard {...parties} />}
      {view === 'quilt' && <PartiesRecomendations {...parties} />}
    </Stack>
  );
}

