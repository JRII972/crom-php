import React, { useMemo } from 'react';
import { ThemeProvider, alpha, createTheme } from '@mui/material/styles';
import { useDominantColor } from './useDominantColor';
import { gray } from '../shared-theme/themePrimitives'

export function DynamicThemeProvider({ imageUrl, children }) {
  const theme = createDynamicTheme(imageUrl);

  return <ThemeProvider theme={theme}>{children}</ThemeProvider>;
}

export function createDynamicTheme( imageUrl ) {
  const colors = useDominantColor(imageUrl);

  const theme = useMemo(() => {
    if (!colors) return createTheme(); // thème par défaut
    return createTheme({
      components: {
        MuiAvatar: {
          styleOverrides: {
            root: ({ theme }) => ({
              backgroundColor: theme.palette.background.avatar || gray[500], // Fallback to gray
              [theme.breakpoints.up('xs')]: {
                width: 24,
                height: 24,
                fontSize: '0.75rem', // Smaller text on xs
              },
              [theme.breakpoints.up('sm')]: {
                width: 32,
                height: 32,
                fontSize: '0.875rem',
              },
              [theme.breakpoints.up('md')]: {
                width: 40,
                height: 40,
                fontSize: '1rem',
              },
              variants: [
                {
                  props: {
                    size: 'small',
                  },
                  style: {
                    width: 24, height: 24
                  },
                },
              ],
            }),
          },
        },
      },
      colorSchemes: {
        light: {
          palette: {
            primary: {
              main: colors.primary.main,
              light: colors.primary.light,
              dark: colors.primary.dark,
            },
            background: {
              mainContent: `${alpha( colors.primary.light, 0.8)}`, // Use primary.main for avatar in light mode
              subcontent: colors.primary.light, // Use primary.main for avatar in light mode
              avatar: colors.primary.main, // Use primary.main for avatar in light mode
              divider: colors.primary.main, // Use primary.main for avatar in light mode
            },
          },
        },
        dark: {
          palette: {
            primary: {
              main: colors.secondary.main,
              light: colors.secondary.light, // Fixed typo: ligh -> light
              dark: colors.secondary.dark,
            },
            background: {
              mainContent: `${alpha( colors.primary.dark, 0.8)}`,
              subcontent: colors.primary.dark,
              avatar: colors.primary.light, // Consistent with dark mode
              divider: colors.primary.light, // Consistent with dark mode
            },
          },
        },
      },
    });;
  }, [colors]);

  return theme;
}

