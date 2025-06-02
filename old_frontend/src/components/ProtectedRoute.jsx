import React from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function ProtectedRoute({ children }) {
  const { isAuthenticated, loading } = useAuth();
  const location = useLocation();

  if (loading) {
    return <div>Chargement...</div>; // Ou un spinner de chargement
  }

  if (!isAuthenticated) {
    // Rediriger vers la page de connexion en conservant l'URL demandée
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  return children;
}