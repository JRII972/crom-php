import { createContext } from 'react';

export const ViewContext = createContext(localStorage.getItem('partyPageSate') || 'card');