import lieuService from './api/services/lieuService';
import evenementService from './api/services/evenementService';
import { useState, useEffect } from 'react';

export default function TestApp() {
  const [lieux, setLieux] = useState([]);
  const [evenements, setEvenements] = useState([]);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchData = async () => {
      try {
        // Fetch locations with filters
        const lieuxData = await lieuService.listLieux({ keyword: '' });
        setLieux(lieuxData);

        // Fetch events
        const evenementsData = await evenementService.listEvenements({ date_debut: '2025-06-01' });
        setEvenements(evenementsData);
      } catch (err) {
        setError(err.message);
      }
    };
    fetchData();
  }, []);

  const handleCreateLieu = async () => {
    try {
      const newLieu = await lieuService.createLieu({
        nom: 'New Venue',
        adresse: '123 Main St',
        latitude: 48.8566,
        longitude: 2.3522,
        description: 'A new gaming venue',
      });
      setLieux([...lieux, newLieu]);
    } catch (err) {
      setError(err.message);
    }
  };

  return (
    <div>
      <h1>Lieux</h1>
      {lieux.map((lieu) => (
        <div key={lieu.id}>{lieu.nom}</div>
      ))}
      <h1>Événements</h1>
      {evenements.map((evenement) => (
        <div key={evenement.id}>{evenement.nom}</div>
      ))}
      <button onClick={handleCreateLieu}>Ajouter Lieu</button>
      {error && <p>{error}</p>}
    </div>
  );
}