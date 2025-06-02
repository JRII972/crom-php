import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import {
  Alert,
  Avatar, 
  Badge,
  Box,
  Button,
  Card,
  CardContent,
  CardHeader,
  Checkbox,
  Chip,
  Container,
  Dialog,
  DialogActions,
  DialogContent,
  DialogTitle,
  Divider,
  FormControl,
  FormControlLabel,
  Grid,
  IconButton,
  InputAdornment,
  List,
  ListItem,
  ListItemIcon,
  ListItemText,
  MenuItem,
  Paper,
  Select,
  Stack,
  Switch,
  Tab,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Tabs,
  TextField,
  Typography
} from '@mui/material';
import { styled } from '@mui/material/styles';
import { LocalizationProvider, DatePicker } from '@mui/x-date-pickers';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
// Import supprimé: import fr from 'date-fns/locale/fr';

// Icons
import EditIcon from '@mui/icons-material/Edit';
import SaveIcon from '@mui/icons-material/Save';
import CancelIcon from '@mui/icons-material/Cancel';
import EmailIcon from '@mui/icons-material/Email';
import PersonIcon from '@mui/icons-material/Person';
import CakeIcon from '@mui/icons-material/Cake';
import DiscordIcon from '@mui/icons-material/Chat';
import CalendarMonthIcon from '@mui/icons-material/CalendarMonth';
import EventIcon from '@mui/icons-material/Event';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';
import HistoryIcon from '@mui/icons-material/History';
import SettingsIcon from '@mui/icons-material/Settings';
import PaymentIcon from '@mui/icons-material/Payment';
import ViewModuleIcon from '@mui/icons-material/ViewModule';
import NotificationsIcon from '@mui/icons-material/Notifications';
import ContentCopyIcon from '@mui/icons-material/ContentCopy';
import AddIcon from '@mui/icons-material/Add';
import DeleteIcon from '@mui/icons-material/Delete';
import VisibilityIcon from '@mui/icons-material/Visibility';
import AccessTimeIcon from '@mui/icons-material/AccessTime';
import PhotoCameraIcon from '@mui/icons-material/PhotoCamera';

// TabPanel personnalisé pour afficher le contenu des onglets
function TabPanel(props) {
  const { children, value, index, ...other } = props;

  return (
    <div
      role="tabpanel"
      hidden={value !== index}
      id={`profile-tabpanel-${index}`}
      aria-labelledby={`profile-tab-${index}`}
      {...other}
    >
      {value === index && (
        <Box sx={{ p: 3 }}>
          {children}
        </Box>
      )}
    </div>
  );
}

// Style pour l'avatar avec badge d'édition
const ProfileAvatar = styled(Avatar)(({ theme }) => ({
  width: 120,
  height: 120,
  border: `4px solid ${theme.palette.background.paper}`,
  boxShadow: theme.shadows[3]
}));

// Style pour le badge d'édition
const EditBadge = styled(Badge)(({ theme }) => ({
  '& .MuiBadge-badge': {
    backgroundColor: theme.palette.primary.main,
    color: theme.palette.primary.contrastText,
    width: 32,
    height: 32,
    borderRadius: '50%',
    cursor: 'pointer'
  }
}));

// Composant principal
export default function ProfilePage() {
  const { user, updateUserInfo } = useAuth();
  const [tabValue, setTabValue] = useState(0);
  const [openEditDialog, setOpenEditDialog] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState('');
  
  // État pour les données du formulaire
  const [formData, setFormData] = useState({
    prenom: user?.prenom || 'Thomas',
    nom: user?.nom || 'Dubois',
    email: user?.email || 'thomas@email.com',
    username: user?.username || 'tomdu92',
    dateNaissance: user?.dateNaissance || new Date('1990-04-15'),
    pseudonyme: user?.pseudonyme || 'TomD',
    idDiscord: user?.idDiscord || 'TomD#1234',
    genre: user?.genre || 'M',
    password: '',
    newPassword: '',
    confirmPassword: ''
  });
  
  // État pour les préférences utilisateur
  const [preferences, setPreferences] = useState({
    afficherJoueurs: true,
    afficherNomsParties: true,
    afficherPseudonymes: false,
    notificationsPersonnalisees: true,
    notificationsRappel: true,
    delaiRappel: '1 jour avant',
    syncGoogleCalendar: false,
    syncMjParties: true, 
    syncJoueurParties: true,
    syncEvenements: true
  });

  // Données fictives pour les tableaux
  const partiesEnCours = [
    { id: 1, nom: "Les Ombres d'Esteren", type: "Campagne", role: "Joueur", prochaine: "15 juin 2025" },
    { id: 2, nom: "Donjons & Dragons", type: "OneShot", role: "Maître du jeu", prochaine: "22 juin 2025" },
    { id: 3, nom: "Chroniques Oubliées", type: "Campagne", role: "Joueur", prochaine: "29 juin 2025" }
  ];

  const disponibilites = [
    { id: 1, jour: "Mercredi", debut: "19:00", fin: "23:00", type: "Disponible" },
    { id: 2, jour: "Vendredi", debut: "20:00", fin: "23:30", type: "Disponible" },
    { id: 3, jour: "Samedi", debut: "14:00", fin: "18:00", type: "Disponible" }
  ];

  const historique = [
    { id: 1, nom: "Appel de Cthulhu", type: "OneShot", date: "15 mai 2025", role: "Joueur", lieu: "Salle Principale" },
    { id: 2, nom: "Pathfinder", type: "Campagne", date: "8 mai 2025", role: "Maître du jeu", lieu: "Salle 2" },
    { id: 3, nom: "Dixit", type: "Jeu de société", date: "1 mai 2025", role: "Joueur", lieu: "Salle 3" }
  ];

  const paiements = [
    { id: 1, date: "15 janvier 2025", description: "Adhésion Annuelle 2025", montant: "25,00 €", statut: "Payé" },
    { id: 2, date: "10 janvier 2024", description: "Adhésion Annuelle 2024", montant: "25,00 €", statut: "Payé" },
    { id: 3, date: "5 janvier 2023", description: "Adhésion Annuelle 2023", montant: "20,00 €", statut: "Payé" }
  ];
  
  // Gestionnaire pour les changements dans le formulaire
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  // Gestionnaire pour les changements de date
  const handleDateChange = (newDate) => {
    setFormData(prev => ({
      ...prev,
      dateNaissance: newDate
    }));
  };
  
  // Gestionnaire pour le changement de préférences
  const handlePreferenceChange = (e) => {
    const { name, checked } = e.target;
    setPreferences(prev => ({
      ...prev,
      [name]: checked
    }));
  };

  // Gestionnaire pour le changement de délai de rappel
  const handleDelaiRappelChange = (e) => {
    setPreferences(prev => ({
      ...prev,
      delaiRappel: e.target.value
    }));
  };
  
  // Gestionnaire pour le changement d'onglet
  const handleTabChange = (event, newValue) => {
    setTabValue(newValue);
  };
  
  // Gestionnaire pour l'ouverture du dialogue d'édition
  const handleOpenEditDialog = () => {
    setOpenEditDialog(true);
  };
  
  // Gestionnaire pour la fermeture du dialogue d'édition
  const handleCloseEditDialog = () => {
    setOpenEditDialog(false);
  };
  
  // Gestionnaire pour l'enregistrement des modifications
  const handleSaveProfile = async () => {
    try {
      // Ici, vous appelleriez votre API pour enregistrer les modifications
      await updateUserInfo(formData);
      setSuccess(true);
      setOpenEditDialog(false);
      
      // Afficher une alerte de succès
      setTimeout(() => {
        setSuccess(false);
      }, 3000);
    } catch (err) {
      setError(err?.message || 'Une erreur est survenue lors de la mise à jour du profil.');
      
      // Masquer l'erreur après un délai
      setTimeout(() => {
        setError('');
      }, 3000);
    }
  };

  // Fonction pour simuler le changement de photo de profil
  const handleChangePhoto = () => {
    alert('Cette fonctionnalité permettrait de télécharger une nouvelle photo de profil');
  };

  // Fonction pour copier le lien iCalendar
  const handleCopyICalLink = () => {
    navigator.clipboard.writeText('https://lbdr-jdr.fr/calendar/ics/user/tomdu92');
    alert('Lien copié dans le presse-papier !');
  };

  // Calculer l'âge à partir de la date de naissance
  const calculerAge = (dateNaissance) => {
    const aujourdhui = new Date();
    const dateNaiss = new Date(dateNaissance);
    let age = aujourdhui.getFullYear() - dateNaiss.getFullYear();
    const mois = aujourdhui.getMonth() - dateNaiss.getMonth();
    
    if (mois < 0 || (mois === 0 && aujourdhui.getDate() < dateNaiss.getDate())) {
      age--;
    }
    
    return age;
  };

  // Formater la date pour l'affichage au format français
  const formaterDate = (date) => {
    const dateObj = new Date(date);
    return dateObj.toLocaleDateString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  };

  // Calculer depuis combien de temps l'utilisateur est membre
  const calculerDureeAdhesion = () => {
    // Date fictive d'adhésion: 15 juin 2022
    const dateAdhesion = new Date('2022-06-15');
    const aujourdhui = new Date();
    
    const diffAnnes = aujourdhui.getFullYear() - dateAdhesion.getFullYear();
    const diffMois = aujourdhui.getMonth() - dateAdhesion.getMonth();
    
    if (diffAnnes > 0) {
      return diffAnnes === 1 ? '1 an' : `${diffAnnes} ans`;
    } else if (diffMois > 0) {
      return diffMois === 1 ? '1 mois' : `${diffMois} mois`;
    } else {
      return 'Moins d\'un mois';
    }
  };

  return (
    <Container maxWidth="lg" sx={{ mt: 4, mb: 4 }}>
      {success && (
        <Alert severity="success" sx={{ mb: 3 }}>
          Profil mis à jour avec succès !
        </Alert>
      )}
      
      {error && (
        <Alert severity="error" sx={{ mb: 3 }}>
          {error}
        </Alert>
      )}
      
      <Grid container spacing={3}>
        {/* Colonne de gauche - Infos de base et photo */}
        <Grid item size={{ xs:12, md:4 }}>
          <Card sx={{ mb: 3 }}>
            <CardContent sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', position: 'relative' }}>
              {/* Photo de profil avec badge d'édition */}
              <EditBadge
                overlap="circular"
                anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
                badgeContent={
                  <IconButton 
                    size="small" 
                    onClick={handleChangePhoto}
                    sx={{ bgcolor: 'primary.main', color: 'white' }}
                  >
                    <PhotoCameraIcon fontSize="small" />
                  </IconButton>
                }
              >
                <ProfileAvatar
                  alt={`${formData.prenom} ${formData.nom}`}
                  src={user?.image || "https://picsum.photos/200"}
                >
                  {!user?.image && formData.prenom[0].toUpperCase() + formData.nom[0].toUpperCase()}
                </ProfileAvatar>
              </EditBadge>
              
              {/* Nom et prénom */}
              <Typography variant="h5" component="h2" sx={{ mt: 2, fontWeight: 'bold' }}>
                {formData.prenom} {formData.nom}
              </Typography>
              
              {/* Pseudonyme */}
              <Typography variant="subtitle1" color="text.secondary" gutterBottom>
                @{formData.pseudonyme}
              </Typography>
              
              {/* Type d'utilisateur */}
              <Chip 
                label="Membre inscrit" 
                color="secondary" 
                sx={{ mt: 1, mb: 3 }}
              />
              
              {/* Informations de base */}
              <List sx={{ width: '100%' }}>
                <ListItem>
                  <ListItemIcon>
                    <EmailIcon color="primary" />
                  </ListItemIcon>
                  <ListItemText primary={formData.email} />
                </ListItem>
                <ListItem>
                  <ListItemIcon>
                    <PersonIcon color="primary" />
                  </ListItemIcon>
                  <ListItemText primary={`login: ${formData.username}`} />
                </ListItem>
                <ListItem>
                  <ListItemIcon>
                    <CakeIcon color="primary" />
                  </ListItemIcon>
                  <ListItemText 
                    primary={`Né le ${formaterDate(formData.dateNaissance)} (${calculerAge(formData.dateNaissance)} ans)`} 
                  />
                </ListItem>
                <ListItem>
                  <ListItemIcon>
                    <DiscordIcon color="primary" />
                  </ListItemIcon>
                  <ListItemText primary={`Discord: ${formData.idDiscord}`} />
                </ListItem>
              </List>
              
              {/* Bouton éditer profil */}
              <Button 
                variant="outlined" 
                color="primary" 
                startIcon={<EditIcon />}
                onClick={handleOpenEditDialog}
                fullWidth
                sx={{ mt: 2 }}
              >
                Modifier mon profil
              </Button>
            </CardContent>
          </Card>

          {/* Statistiques */}
          <Card>
            <CardHeader title="Statistiques" />
            <CardContent>
              <Paper elevation={2} sx={{ p: 2, mb: 2 }}>
                <Typography variant="subtitle2" color="text.secondary">Membre depuis</Typography>
                <Typography variant="h6" color="primary" fontWeight="bold">{calculerDureeAdhesion()}</Typography>
                <Typography variant="caption" color="text.secondary">
                  Inscription le 15/06/2022
                </Typography>
              </Paper>
              
              <Paper elevation={2} sx={{ p: 2, mb: 2 }}>
                <Typography variant="subtitle2" color="text.secondary">Parties jouées</Typography>
                <Typography variant="h6" color="secondary" fontWeight="bold">42</Typography>
                <Typography variant="caption" color="text.secondary">
                  +8% par rapport à l'année dernière
                </Typography>
              </Paper>
              
              <Paper elevation={2} sx={{ p: 2 }}>
                <Typography variant="subtitle2" color="text.secondary">Parties créées</Typography>
                <Typography variant="h6" fontWeight="bold" sx={{ color: 'info.main' }}>7</Typography>
                <Typography variant="caption" color="text.secondary">
                  En tant que maître de jeu
                </Typography>
              </Paper>
            </CardContent>
          </Card>
        </Grid>
        
        {/* Colonne centrale et droite - Tabs et contenus */}
        <Grid item size={{ xs:12, md:8 }}>
          <Card sx={{ height: '100%' }}>
            <Box sx={{ borderBottom: 1, borderColor: 'divider' }}>
              <Tabs 
                value={tabValue} 
                onChange={handleTabChange} 
                aria-label="profile tabs"
                variant="scrollable"
                scrollButtons="auto"
              >
                <Tab icon={<EventIcon />} iconPosition="start" label="Mes Parties" id="profile-tab-0" aria-controls="profile-tabpanel-0" />
                <Tab icon={<AccessTimeIcon />} iconPosition="start" label="Disponibilités" id="profile-tab-1" aria-controls="profile-tabpanel-1" />
                <Tab icon={<HistoryIcon />} iconPosition="start" label="Historique" id="profile-tab-2" aria-controls="profile-tabpanel-2" />
                <Tab icon={<SettingsIcon />} iconPosition="start" label="Préférences" id="profile-tab-3" aria-controls="profile-tabpanel-3" />
                <Tab icon={<PaymentIcon />} iconPosition="start" label="Paiements" id="profile-tab-4" aria-controls="profile-tabpanel-4" />
              </Tabs>
            </Box>
            
            {/* Contenu des onglets */}
            <TabPanel value={tabValue} index={0}>
              <Typography variant="h6" gutterBottom fontWeight="bold">
                Mes parties en cours
              </Typography>
              
              <TableContainer component={Paper} sx={{ mb: 3 }}>
                <Table>
                  <TableHead>
                    <TableRow>
                      <TableCell>Nom de la partie</TableCell>
                      <TableCell>Type</TableCell>
                      <TableCell>Rôle</TableCell>
                      <TableCell>Prochaine session</TableCell>
                      <TableCell>Action</TableCell>
                    </TableRow>
                  </TableHead>
                  <TableBody>
                    {partiesEnCours.map((partie) => (
                      <TableRow key={partie.id}>
                        <TableCell>{partie.nom}</TableCell>
                        <TableCell>
                          <Chip 
                            label={partie.type} 
                            color={partie.type === "Campagne" ? "primary" : "secondary"} 
                            size="small"
                          />
                        </TableCell>
                        <TableCell>{partie.role}</TableCell>
                        <TableCell>{partie.prochaine}</TableCell>
                        <TableCell>
                          <Button variant="outlined" size="small">
                            <VisibilityIcon fontSize="small" sx={{ mr: 0.5 }} />
                            Voir
                          </Button>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </TableContainer>
              
              <Box display="flex" justifyContent="flex-end">
                <Button 
                  variant="contained" 
                  color="primary" 
                  startIcon={<AddIcon />}
                >
                  Créer une nouvelle partie
                </Button>
              </Box>
            </TabPanel>
            
            <TabPanel value={tabValue} index={1}>
              <Typography variant="h6" gutterBottom fontWeight="bold">
                Mes disponibilités
              </Typography>
              
              {/* Calendrier simplifié - Dans une application réelle, vous utiliseriez un composant de calendrier complet */}
              <Paper sx={{ p: 2, mb: 4 }}>
                <Grid container spacing={1} sx={{ mb: 2 }}>
                  {["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"].map((jour, index) => (
                    <Grid item xs={12/7} key={index} textAlign="center">
                      <Typography variant="subtitle2" fontWeight="medium">{jour}</Typography>
                    </Grid>
                  ))}
                  
                  {/* Exemple de jours */}
                  {[1, 2, 3, 4, 5, 6, 7].map((jour, index) => (
                    <Grid item xs={12/7} key={index} textAlign="center">
                      <Button 
                        variant="outlined" 
                        size="small"
                        color={jour === 3 || jour === 5 || jour === 6 ? "primary" : "inherit"}
                        sx={{ minWidth: 30 }}
                      >
                        {jour}
                      </Button>
                    </Grid>
                  ))}
                </Grid>
              </Paper>
              
              <Divider textAlign="center" sx={{ my: 3 }}>Disponibilités récurrentes</Divider>
              
              <TableContainer component={Paper} sx={{ mb: 3 }}>
                <Table>
                  <TableHead>
                    <TableRow>
                      <TableCell>Jour</TableCell>
                      <TableCell>Heure de début</TableCell>
                      <TableCell>Heure de fin</TableCell>
                      <TableCell>Type</TableCell>
                      <TableCell>Action</TableCell>
                    </TableRow>
                  </TableHead>
                  <TableBody>
                    {disponibilites.map((dispo) => (
                      <TableRow key={dispo.id}>
                        <TableCell>{dispo.jour}</TableCell>
                        <TableCell>{dispo.debut}</TableCell>
                        <TableCell>{dispo.fin}</TableCell>
                        <TableCell>
                          <Chip 
                            label={dispo.type} 
                            color="success" 
                            size="small"
                          />
                        </TableCell>
                        <TableCell>
                          <Button 
                            variant="outlined" 
                            color="error" 
                            size="small"
                            startIcon={<DeleteIcon />}
                          >
                            Supprimer
                          </Button>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </TableContainer>
              
              <Box display="flex" justifyContent="flex-end">
                <Button 
                  variant="contained" 
                  color="primary" 
                  startIcon={<AddIcon />}
                >
                  Ajouter une disponibilité
                </Button>
              </Box>
            </TabPanel>
            
            <TabPanel value={tabValue} index={2}>
              <Typography variant="h6" gutterBottom fontWeight="bold">
                Historique des parties
              </Typography>
              
              <Box sx={{ display: 'flex', gap: 2, mb: 3 }}>
                <FormControl fullWidth>
                  <Select
                    defaultValue=""
                    displayEmpty
                  >
                    <MenuItem value="" disabled>Filtrer par type</MenuItem>
                    <MenuItem value="all">Toutes les parties</MenuItem>
                    <MenuItem value="campagne">Campagnes</MenuItem>
                    <MenuItem value="oneshot">OneShots</MenuItem>
                    <MenuItem value="jds">Jeux de société</MenuItem>
                  </Select>
                </FormControl>
                
                <FormControl fullWidth>
                  <Select
                    defaultValue=""
                    displayEmpty
                  >
                    <MenuItem value="" disabled>Filtrer par rôle</MenuItem>
                    <MenuItem value="all">Tous les rôles</MenuItem>
                    <MenuItem value="mj">Maître du jeu</MenuItem>
                    <MenuItem value="joueur">Joueur</MenuItem>
                  </Select>
                </FormControl>
              </Box>
              
              <TableContainer component={Paper}>
                <Table>
                  <TableHead>
                    <TableRow>
                      <TableCell>Nom de la partie</TableCell>
                      <TableCell>Type</TableCell>
                      <TableCell>Date</TableCell>
                      <TableCell>Rôle</TableCell>
                      <TableCell>Lieu</TableCell>
                    </TableRow>
                  </TableHead>
                  <TableBody>
                    {historique.map((partie) => (
                      <TableRow key={partie.id}>
                        <TableCell>{partie.nom}</TableCell>
                        <TableCell>
                          <Chip 
                            label={partie.type} 
                            color={
                              partie.type === "Campagne" 
                                ? "primary" 
                                : partie.type === "OneShot" 
                                  ? "secondary" 
                                  : "default"
                            } 
                            size="small"
                          />
                        </TableCell>
                        <TableCell>{partie.date}</TableCell>
                        <TableCell>{partie.role}</TableCell>
                        <TableCell>{partie.lieu}</TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </TableContainer>
            </TabPanel>
            
            <TabPanel value={tabValue} index={3}>
              {/* Préférences d'affichage des cartes */}
              <Paper sx={{ p: 3, mb: 3 }}>
                <Box display="flex" alignItems="center" mb={2}>
                  <ViewModuleIcon sx={{ mr: 1 }} />
                  <Typography variant="h6" fontWeight="bold">
                    Préférences d'affichage des cartes
                  </Typography>
                </Box>
                <Typography variant="body2" color="text.secondary" paragraph>
                  Personnalisez l'affichage des cartes de parties et d'événements.
                </Typography>
                
                <Box sx={{ mt: 3 }}>
                  <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                    <Box>
                      <Typography variant="subtitle1" fontWeight="medium">Afficher les joueurs</Typography>
                      <Typography variant="body2" color="text.secondary">
                        Montrer la liste des joueurs inscrits sur les cartes de parties
                      </Typography>
                    </Box>
                    <Switch 
                      checked={preferences.afficherJoueurs} 
                      onChange={handlePreferenceChange}
                      name="afficherJoueurs"
                      color="primary"
                    />
                  </Box>
                  
                  <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                    <Box>
                      <Typography variant="subtitle1" fontWeight="medium">Afficher les noms de parties</Typography>
                      <Typography variant="body2" color="text.secondary">
                        Montrer les noms complets des parties plutôt que des descriptions courtes
                      </Typography>
                    </Box>
                    <Switch 
                      checked={preferences.afficherNomsParties} 
                      onChange={handlePreferenceChange}
                      name="afficherNomsParties"
                      color="primary"
                    />
                  </Box>
                  
                  <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                    <Box>
                      <Typography variant="subtitle1" fontWeight="medium">Afficher les pseudonymes</Typography>
                      <Typography variant="body2" color="text.secondary">
                        Utiliser les pseudonymes au lieu des noms réels des utilisateurs
                      </Typography>
                    </Box>
                    <Switch 
                      checked={preferences.afficherPseudonymes} 
                      onChange={handlePreferenceChange}
                      name="afficherPseudonymes"
                      color="primary"
                    />
                  </Box>
                </Box>
              </Paper>
              
              {/* Notifications */}
              <Paper sx={{ p: 3, mb: 3 }}>
                <Box display="flex" alignItems="center" mb={2}>
                  <NotificationsIcon sx={{ mr: 1 }} />
                  <Typography variant="h6" fontWeight="bold">
                    Notifications et rappels
                  </Typography>
                </Box>
                <Typography variant="body2" color="text.secondary" paragraph>
                  Gérez vos préférences de notifications et rappels pour les parties et événements.
                </Typography>
                
                <Box sx={{ mt: 3 }}>
                  <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                    <Box>
                      <Typography variant="subtitle1" fontWeight="medium">Afficher des informations personnalisées</Typography>
                      <Typography variant="body2" color="text.secondary">
                        Recevoir des recommandations basées sur vos préférences et votre historique
                      </Typography>
                    </Box>
                    <Switch 
                      checked={preferences.notificationsPersonnalisees} 
                      onChange={handlePreferenceChange}
                      name="notificationsPersonnalisees"
                      color="primary"
                    />
                  </Box>
                  
                  <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                    <Box>
                      <Typography variant="subtitle1" fontWeight="medium">Envoyer des notifications de rappel</Typography>
                      <Typography variant="body2" color="text.secondary">
                        Recevoir des rappels pour vos parties à venir
                      </Typography>
                    </Box>
                    <Switch 
                      checked={preferences.notificationsRappel} 
                      onChange={handlePreferenceChange}
                      name="notificationsRappel"
                      color="primary"
                    />
                  </Box>
                  
                  <Box sx={{ mt: 3, display: 'flex', justifyContent: 'flex-end', maxWidth: 300, ml: 'auto' }}>
                    <FormControl fullWidth>
                      <Typography variant="body2" sx={{ mb: 1 }}>Délai de rappel</Typography>
                      <Select
                        value={preferences.delaiRappel}
                        onChange={handleDelaiRappelChange}
                        fullWidth
                      >
                        <MenuItem value="1 jour avant">1 jour avant</MenuItem>
                        <MenuItem value="2 jours avant">2 jours avant</MenuItem>
                        <MenuItem value="1 semaine avant">1 semaine avant</MenuItem>
                      </Select>
                    </FormControl>
                  </Box>
                </Box>
              </Paper>
              
              {/* Calendrier */}
              <Paper sx={{ p: 3 }}>
                <Box display="flex" alignItems="center" mb={2}>
                  <CalendarMonthIcon sx={{ mr: 1 }} />
                  <Typography variant="h6" fontWeight="bold">
                    Synchronisation du calendrier
                  </Typography>
                </Box>
                <Typography variant="body2" color="text.secondary" paragraph>
                  Synchronisez vos parties et événements avec votre calendrier personnel.
                </Typography>
                
                <Box sx={{ mt: 3 }}>
                  <Button 
                    variant="contained" 
                    color="primary" 
                    startIcon={
                      <svg width="24" height="24" viewBox="0 0 200 200">
                        <g transform="translate(3.75 3.75)">
                          <path fill="#FFFFFF" d="M148.882,43.618l-47.368-5.263l-57.895,5.263L38.355,96.25l5.263,52.632l52.632,6.579l52.632-6.579l5.263-53.947L148.882,43.618z"/>
                          <path fill="#1A73E8" d="M65.211,125.276c-3.934-2.658-6.658-6.539-8.145-11.671l9.132-3.763c0.829,3.158,2.276,5.605,4.342,7.342c2.053,1.737,4.553,2.592,7.474,2.592c2.987,0,5.553-0.908,7.697-2.724s3.224-4.132,3.224-6.934c0-2.868-1.132-5.211-3.395-7.026s-5.105-2.724-8.5-2.724h-5.276v-9.039H76.5c2.921,0,5.382-0.789,7.382-2.368c2-1.579,3-3.737,3-6.487c0-2.447-0.895-4.395-2.684-5.855s-4.053-2.197-6.803-2.197c-2.684,0-4.816,0.711-6.395,2.145s-2.724,3.197-3.447,5.276l-9.039-3.763c1.197-3.395,3.395-6.395,6.618-8.987c3.224-2.592,7.342-3.895,12.342-3.895c3.697,0,7.026,0.711,9.974,2.145c2.947,1.434,5.263,3.421,6.934,5.947c1.671,2.539,2.5,5.382,2.5,8.539c0,3.224-0.776,5.947-2.329,8.184c-1.553,2.237-3.461,3.947-5.724,5.145v0.539c2.987,1.25,5.421,3.158,7.342,5.724c1.908,2.566,2.868,5.632,2.868,9.211s-0.908,6.776-2.724,9.579c-1.816,2.803-4.329,5.013-7.513,6.618c-3.197,1.605-6.789,2.421-10.776,2.421C73.408,129.263,69.145,127.934,65.211,125.276z"/>
                          <path fill="#1A73E8" d="M121.25,79.961l-9.974,7.25l-5.013-7.605l17.987-12.974h6.895v61.197h-9.895L121.25,79.961z"/>
                          <path fill="#EA4335" d="M148.882,196.25l47.368-47.368l-23.684-10.526l-23.684,10.526l-10.526,23.684L148.882,196.25z"/>
                          <path fill="#34A853" d="M33.092,172.566l10.526,23.684h105.263v-47.368H43.618L33.092,172.566z"/>
                          <path fill="#4285F4" d="M12.039-3.75C3.316-3.75-3.75,3.316-3.75,12.039v136.842l23.684,10.526l23.684-10.526V43.618h105.263l10.526-23.684L148.882-3.75H12.039z"/>
                          <path fill="#188038" d="M-3.75,148.882v31.579c0,8.724,7.066,15.789,15.789,15.789h31.579v-47.368H-3.75z"/>
                          <path fill="#FBBC04" d="M148.882,43.618v105.263h47.368V43.618l-23.684-10.526L148.882,43.618z"/>
                          <path fill="#1967D2" d="M196.25,43.618V12.039c0-8.724-7.066-15.789-15.789-15.789h-31.579v47.368H196.25z"/>
                        </g>
                      </svg>
                    }
                    sx={{ mb: 3 }}
                  >
                    Connecter avec Google Calendar
                  </Button>
                  
                  <Box sx={{ mb: 3 }}>
                    <Typography variant="subtitle1" fontWeight="medium" sx={{ mb: 1 }}>
                      Lien iCalendar (ICS)
                    </Typography>
                    <Box display="flex" alignItems="center">
                      <TextField 
                        value="https://lbdr-jdr.fr/calendar/ics/user/tomdu92"
                        fullWidth
                        InputProps={{
                          readOnly: true,
                          endAdornment: (
                            <InputAdornment position="end">
                              <IconButton onClick={handleCopyICalLink}>
                                <ContentCopyIcon />
                              </IconButton>
                            </InputAdornment>
                          )
                        }}
                      />
                    </Box>
                    <Typography variant="caption" color="text.secondary" sx={{ mt: 1, display: 'block' }}>
                      Copiez ce lien et ajoutez-le à votre calendrier préféré (Google Calendar, Apple Calendar, Outlook, etc.) 
                      pour synchroniser automatiquement vos parties et événements.
                    </Typography>
                  </Box>
                  
                  <Divider sx={{ my: 3 }} />
                  
                  <Typography variant="subtitle1" fontWeight="medium" sx={{ mb: 2 }}>
                    Données à synchroniser
                  </Typography>
                  
                  <Box>
                    <FormControlLabel
                      control={
                        <Checkbox 
                          checked={preferences.syncMjParties}
                          onChange={handlePreferenceChange}
                          name="syncMjParties"
                          color="primary"
                        />
                      }
                      label="Mes parties en tant que MJ"
                    />
                    
                    <FormControlLabel
                      control={
                        <Checkbox 
                          checked={preferences.syncJoueurParties}
                          onChange={handlePreferenceChange}
                          name="syncJoueurParties"
                          color="primary"
                        />
                      }
                      label="Mes parties en tant que joueur"
                    />
                    
                    <FormControlLabel
                      control={
                        <Checkbox 
                          checked={preferences.syncEvenements}
                          onChange={handlePreferenceChange}
                          name="syncEvenements"
                          color="primary"
                        />
                      }
                      label="Événements de l'association"
                    />
                  </Box>
                </Box>
              </Paper>
            </TabPanel>
            
            <TabPanel value={tabValue} index={4}>
              <Typography variant="h6" gutterBottom fontWeight="bold">
                Mes paiements
              </Typography>
              
              <Alert 
                icon={<CheckCircleIcon fontSize="inherit" />}
                severity="success" 
                sx={{ mb: 3 }}
              >
                Votre adhésion est à jour pour l'année 2025.
              </Alert>
              
              <TableContainer component={Paper} sx={{ mb: 3 }}>
                <Table>
                  <TableHead>
                    <TableRow>
                      <TableCell>Date</TableCell>
                      <TableCell>Description</TableCell>
                      <TableCell>Montant</TableCell>
                      <TableCell>Statut</TableCell>
                      <TableCell>Action</TableCell>
                    </TableRow>
                  </TableHead>
                  <TableBody>
                    {paiements.map((paiement) => (
                      <TableRow key={paiement.id}>
                        <TableCell>{paiement.date}</TableCell>
                        <TableCell>{paiement.description}</TableCell>
                        <TableCell>{paiement.montant}</TableCell>
                        <TableCell>
                          <Chip 
                            label={paiement.statut} 
                            color="success" 
                            size="small"
                          />
                        </TableCell>
                        <TableCell>
                          <Button variant="outlined" size="small">
                            Reçu
                          </Button>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </TableContainer>
              
              <Box display="flex" justifyContent="flex-end">
                <Button 
                  variant="contained" 
                  color="success" 
                  startIcon={<PaymentIcon />}
                >
                  Renouveler mon adhésion
                </Button>
              </Box>
            </TabPanel>
          </Card>
        </Grid>
      </Grid>
      
      {/* Modal d'édition de profil */}
      <Dialog 
        open={openEditDialog} 
        onClose={handleCloseEditDialog}
        fullWidth
        maxWidth="md"
      >
        <DialogTitle>Modifier mon profil</DialogTitle>
        <DialogContent dividers>
          <Grid container spacing={3}>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label="Prénom"
                name="prenom"
                value={formData.prenom}
                onChange={handleChange}
                margin="normal"
                required
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label="Nom"
                name="nom"
                value={formData.nom}
                onChange={handleChange}
                margin="normal"
                required
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label="Pseudonyme"
                name="pseudonyme"
                value={formData.pseudonyme}
                onChange={handleChange}
                margin="normal"
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label="Nom d'utilisateur"
                name="username"
                value={formData.username}
                onChange={handleChange}
                margin="normal"
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label="Email"
                name="email"
                type="email"
                value={formData.email}
                onChange={handleChange}
                margin="normal"
                required
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label="ID Discord"
                name="idDiscord"
                value={formData.idDiscord}
                onChange={handleChange}
                margin="normal"
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <LocalizationProvider dateAdapter={AdapterDayjs}>
                <DatePicker
                  label="Date de naissance"
                  value={formData.dateNaissance}
                  onChange={handleDateChange}
                  format="DD/MM/YYYY"
                  slotProps={{
                    textField: { fullWidth: true, margin: "normal" },
                  }}
                  localeText={{
                    previousMonth: 'Mois précédent',
                    nextMonth: 'Mois suivant',
                    today: "Aujourd'hui",
                    cancel: 'Annuler',
                    ok: 'OK',
                    dateTableLabel: 'Calendrier',
                    calendarLabel: 'Calendrier',
                  }}
                />
              </LocalizationProvider>
            </Grid>
            <Grid item xs={12} sm={6}>
              <FormControl fullWidth margin="normal">
                <Typography variant="body2" sx={{ mb: 1 }}>Sexe</Typography>
                <Select
                  value={formData.genre}
                  name="genre"
                  onChange={handleChange}
                >
                  <MenuItem value="M">Masculin</MenuItem>
                  <MenuItem value="F">Féminin</MenuItem>
                  <MenuItem value="Autre">Autre</MenuItem>
                </Select>
              </FormControl>
            </Grid>
          </Grid>
          
          <Divider sx={{ my: 3 }}>Changement de mot de passe</Divider>
          
          <Grid container spacing={3}>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label="Mot de passe actuel"
                name="password"
                type="password"
                value={formData.password}
                onChange={handleChange}
                margin="normal"
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label="Nouveau mot de passe"
                name="newPassword"
                type="password"
                value={formData.newPassword}
                onChange={handleChange}
                margin="normal"
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label="Confirmer le mot de passe"
                name="confirmPassword"
                type="password"
                value={formData.confirmPassword}
                onChange={handleChange}
                margin="normal"
              />
            </Grid>
          </Grid>
        </DialogContent>
        <DialogActions>
          <Button 
            variant="outlined" 
            color="error" 
            onClick={handleCloseEditDialog}
            startIcon={<CancelIcon />}
          >
            Annuler
          </Button>
          <Button 
            variant="contained" 
            color="primary" 
            onClick={handleSaveProfile}
            startIcon={<SaveIcon />}
          >
            Enregistrer les modifications
          </Button>
        </DialogActions>
      </Dialog>
    </Container>
  );
}
