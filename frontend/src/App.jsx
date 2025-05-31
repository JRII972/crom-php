import * as React from 'react';
import Container from '@mui/material/Container';
import Typography from '@mui/material/Typography';
import Box from '@mui/material/Box';
import ProTip from './ProTip';
import Copyright from './Copyright';import {
  createBrowserRouter,
  RouterProvider,
} from "react-router-dom";

import SignIn from './sign-in/SignIn'
import Dashboard from './dashboard/Dashboard'
import PartyTab from './party-tab/PartyTab'
import MainPage from './base/MainPage';
import PartiePage from './party-tab/PartiePage';
import { getPartieNameFromId } from './utils/utils';
import Blog from './blog/Blog';
import UserManagement from './API/test/user';

import PlayerParties from './party-tab/PlayerParties';
import PartiesPage from './party-tab/PartiesPage';
import { DisplayLBDR } from './utils/LBDRDisplay';
import TestApp from './test';
import { AuthProvider } from './contexts/AuthContext';
import ProtectedRoute from './components/ProtectedRoute';
import Profile from './profile/Profile';

const router = createBrowserRouter([
  {
    path: "/*", //TODO: verifiquer si ca ne bloque pas l'accès à d'autre page
    element: <MainPage noHeader/>,
    handle: { breadcrumb: 'CROM' },
    children: [
      {
        index: true,
        element: <PartyTab />,
        handle: { breadcrumb: 'Parties', title: 'Calendrier Rôliste à Option Multiples' },
      }
    ]
  },
  {
    path: "/", //TODO: verifiquer si ca ne bloque pas l'accès à d'autre page
    element: <MainPage/>,
    handle: { breadcrumb: 'CROM' },
    children: [
      {
        path: "test",   
        element: <TestApp />, 
        handle: {
          breadcrumb: (match) => {
            const partieName = getPartieNameFromId(match.params.id); 
            return partieName || "Chargement...";
          }
        }
      },
      {
        path: "partie/:id",   
        element: <PartiePage />, 
        handle: {
          breadcrumb: (match) => {
            const partieName = getPartieNameFromId(match.params.id); 
            return partieName || "Chargement...";
          }
        }
      },
      {
        path: "mes-parties",   
        element: <PlayerParties />, 
        handle: {
          breadcrumb: 'Mes Parties',
          title: 'Mes Parties',
        }
      },
      {
        path: "parties",   
        element: <PartiesPage />, 
        handle: {
          breadcrumb: 'Parties',
          title: 'Parties',
        }
      },
      {
        path: "dashboard",
        element: <ProtectedRoute><Dashboard /></ProtectedRoute>,
        handle: { breadcrumb: 'Dashboard' },
      },
      {
        path: "blog",
        element: <Blog />,
        handle: { breadcrumb: 'Dashboard' },
      },
      {
        // Only for test purpose
        path: "api",
        // element: <UserManagement />,
        handle: { breadcrumb: 'API' },
        children: [
          {
            path: "user",
            element: <UserManagement />,
            handle: { breadcrumb: 'USER' },
            children: [
              
            ]
          },
        ]
      },
      {
        path: "profile",
        element: <ProtectedRoute><Profile /></ProtectedRoute>,
        handle: { breadcrumb: 'Profil' },
      },
    ]
  }, 
  {
    path: "/login",
    element: <SignIn />,
    handle: { breadcrumb: 'Connexion' },
  },
]);

export default function App() {
  return (
    <AuthProvider>
      <RouterProvider router={router} />
    </AuthProvider>
  );
}