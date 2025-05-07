import React, { useMemo } from 'react';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import { useDominantColor } from './useDominantColor';

export function DynamicThemeProvider({ imageUrl, children }) {
  const colors = useDominantColor(imageUrl);

  // Tant que les couleurs ne sont pas chargées, on attend
  const theme = useMemo(() => {
    if (!colors) return createTheme(); // thème par défaut
    return createTheme({
      colorSchemes: {
        light: {
          palette: {
            primary: {
              main: colors.primary.main,
              light: colors.primary.light,
              dark: colors.primary.dark
            },
            // secondary: {
            //   main: colors.secondary.main,
            //   // light: colors.secondary.light
            // }
          },
        },
        dark: {
          palette: {
            primary: {
              main: colors.secondary.main,
              ligh: colors.secondary.ligh,
              dark: colors.secondary.dark
            },
            // secondary: {
            //   main: colors.secondary.main,
            //   // light: colors.secondary.main
            // }
          },
        },
      },
    });;
  }, [colors]);

  return <ThemeProvider theme={theme}>{children}</ThemeProvider>;
}