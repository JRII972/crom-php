import * as React from 'react';
import { styled } from '@mui/material/styles';
import Avatar from '@mui/material/Avatar';
import MuiDrawer, { drawerClasses } from '@mui/material/Drawer';
import Box from '@mui/material/Box';
import Divider from '@mui/material/Divider';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';
import SelectContent from './SelectContent';
import MenuContent from './MenuContent';
import CardAlert from './CardAlert';
import OptionsMenu from './OptionsMenu';
import { useAuth } from '../../contexts/AuthContext';

export default function SideMenuUser() {
    const { user, isAuthenticated } = useAuth();
    
    if (!isAuthenticated) {
        return null; // Ne pas afficher si non connecté
    }
    
    return(
        <Stack
            direction="row"
            sx={{
            p: 2,
            gap: 1,
            alignItems: 'center',
            borderTop: '1px solid',
            borderColor: 'divider',
            }}
        >
            <Avatar
            sizes="small"
            alt={user?.prenom || 'U'}
            src={user?.image || ''}
            sx={{ width: 36, height: 36 }}
            >
                {!user?.image && (user?.prenom?.[0]?.toUpperCase() || 'U')}
            </Avatar>
            <Box sx={{ mr: 'auto' }}>
            <Typography variant="body2" sx={{ fontWeight: 500, lineHeight: '16px' }}>
                {`${user?.prenom || ''} ${user?.nom || ''}`}
            </Typography>
            <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                {user?.email || user?.nomUtilisateur || ''}
            </Typography>
            </Box>
            <OptionsMenu />
        </Stack>
    )
}