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
import MainPage from './components/MainPage';
import PartiePage from './party-tab/PartiePage';
import { getPartieNameFromId } from './utils/utils';
import NotFound404 from './404';
import NotFound404_v2 from './404_v2';
import Blog from './blog/Blog';
import UserManagement from './API/test/user';

import PlayerParties from './PlayersParties/PlayerParties';

const router = createBrowserRouter([
  {
    path: "/*", //TODO: verifiquer si ca ne bloque pas l'accès à d'autre page
    element: <MainPage/>,
    handle: { breadcrumb: 'CROM' },
    children: [
      {
        index: true,
        element: <PartyTab />,
        handle: { breadcrumb: 'Parties', title: 'Calendrier Rôliste à Option Multiples' },
      },
      {
        path: "partie/:id",   // Route dynamique
        element: <PartiePage />, 
        handle: {
          breadcrumb: (match) => {
            const partieName = getPartieNameFromId(match.params.id); 
            return partieName || "Chargement...";
          }
        }
      },
      {
        path: "mes-parties",   // Route dynamique
        element: <PlayerParties />, 
        handle: {
          breadcrumb: 'Mes Parties',
          title: 'Mes Parties',
        }
      },
      {
        path: "dashboard",
        element: <Dashboard />,
        handle: { breadcrumb: 'Dashboard' },
      },
      {
        path: "blog",
        element: <Blog />,
        handle: { breadcrumb: 'Dashboard' },
      },
      {
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
    ]
  },
  {
    path: "/login",
    element: <SignIn />,
    handle: { breadcrumb: 'Connexion' },
  },
  {
    path: "/404",
    element: <NotFound404 />,
    handle: { breadcrumb: 'Connexion' },
  },
  {
    path: "/404_v2",
    element: <NotFound404_v2 />,
    handle: { breadcrumb: 'Connexion' },
  },
]);

export default function App() {
  return (
    <RouterProvider router={router} />
  );
}