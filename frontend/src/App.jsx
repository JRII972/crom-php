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

const router = createBrowserRouter([
  {
    path: "/*", //TODO: verifiquer si ca ne bloque pas l'accès à d'autre page
    element: <PartyTab/>,
  },
  {
    path: "/login",
    element: <SignIn />,
  },
  {
    path: "/parties",
    element: <PartyTab />,
  },
  {
    path: "/dashboard",
    element: <Dashboard />,
  },
]);

export default function App() {
  return (
    <RouterProvider router={router} />
  );
}