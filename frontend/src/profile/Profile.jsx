import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Container from '@mui/material/Container';
import Grid from '@mui/material/Grid';
import Paper from '@mui/material/Paper';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import SaveIcon from '@mui/icons-material/Save';
import Avatar from '@mui/material/Avatar';
import Alert from '@mui/material/Alert';

export default function Profile() {
  const { user, updateUserInfo } = useAuth();
  
  const [formData, setFormData] = useState({
    prenom: user?.prenom || '',
    nom: user?.nom || '',
    email: user?.email || '',
    pseudonyme: user?.pseudonyme || '',
    idDiscord: user?.idDiscord || '',
  });
  
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState('');
  
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    setSuccess(false);
    setError('');
    
    try {
      await updateUserInfo(formData);
      setSuccess(true);
    } catch (err) {
      setError(err.message || 'Une erreur est survenue lors de la mise à jour du profil.');
    }
  };
  
  return (
    <Container maxWidth="lg" sx={{ mt: 4, mb: 4 }}>
      <Typography variant="h4" gutterBottom>
        Mon Profil
      </Typography>
      
      {success && (
        <Alert severity="success" sx={{ mb: 2 }}>
          Profil mis à jour avec succès !
        </Alert>
      )}
      
      {error && (
        <Alert severity="error" sx={{ mb: 2 }}>
          {error}
        </Alert>
      )}
      
      <Paper sx={{ p: 3 }}>
        <Box component="form" onSubmit={handleSubmit} noValidate>
          <Grid container spacing={3}>
            <Grid item xs={12} display="flex" justifyContent="center" mb={2}>
              <Avatar
                alt={user?.prenom || 'User'}
                src={user?.image || ''}
                sx={{ width: 120, height: 120 }}
              >
                {!user?.image && (user?.prenom?.[0]?.toUpperCase() || 'U')}
              </Avatar>
            </Grid>
            
            <Grid item xs={12} sm={6}>
              <TextField
                required
                fullWidth
                id="prenom"
                name="prenom"
                label="Prénom"
                value={formData.prenom}
                onChange={handleChange}
              />
            </Grid>
            
            <Grid item xs={12} sm={6}>
              <TextField
                required
                fullWidth
                id="nom"
                name="nom"
                label="Nom"
                value={formData.nom}
                onChange={handleChange}
              />
            </Grid>
            
            <Grid item xs={12}>
              <TextField
                fullWidth
                id="email"
                name="email"
                label="Email"
                type="email"
                value={formData.email}
                onChange={handleChange}
              />
            </Grid>
            
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                id="pseudonyme"
                name="pseudonyme"
                label="Pseudonyme"
                value={formData.pseudonyme}
                onChange={handleChange}
              />
            </Grid>
            
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                id="idDiscord"
                name="idDiscord"
                label="ID Discord"
                value={formData.idDiscord}
                onChange={handleChange}
              />
            </Grid>
            
            <Grid item xs={12}>
              <Button
                type="submit"
                variant="contained"
                startIcon={<SaveIcon />}
              >
                Enregistrer
              </Button>
            </Grid>
          </Grid>
        </Box>
      </Paper>
    </Container>
  );
}