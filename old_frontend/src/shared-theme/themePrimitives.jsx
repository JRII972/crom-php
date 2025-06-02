import { createTheme, alpha } from '@mui/material/styles';

const defaultTheme = createTheme();

const customShadows = [...defaultTheme.shadows];

// Palette Caramellatte (mode clair)
export const brand = {
  50: 'hsl(43, 96%, 98%)',
  100: 'hsl(43, 96%, 94%)',
  200: 'hsl(42, 87%, 84%)',
  300: 'hsl(42, 87%, 74%)',
  400: 'hsl(35, 91%, 65%)',  // primary color
  500: 'hsl(32, 95%, 44%)',
  600: 'hsl(31, 94%, 41%)',
  700: 'hsl(29, 92%, 35%)',
  800: 'hsl(28, 87%, 29%)',
  900: 'hsl(27, 83%, 23%)',
};

// Grays pour Caramellatte
export const gray = {
  50: 'hsl(30, 33%, 99%)',
  100: 'hsl(30, 33%, 97%)',
  200: 'hsl(30, 33%, 92%)',
  300: 'hsl(30, 33%, 85%)',
  400: 'hsl(30, 25%, 60%)',
  500: 'hsl(30, 25%, 50%)',
  600: 'hsl(30, 25%, 40%)',
  700: 'hsl(30, 25%, 25%)',
  800: 'hsl(30, 33%, 15%)',
  900: 'hsl(30, 33%, 8%)',
};

// Couleurs pour Sunset (mode sombre)
export const sunset = {
  50: 'hsl(42, 100%, 95%)',
  100: 'hsl(42, 100%, 90%)',
  200: 'hsl(37, 100%, 80%)',
  300: 'hsl(32, 100%, 70%)',
  400: 'hsl(27, 100%, 60%)',  // primary color for dark mode
  500: 'hsl(21, 100%, 55%)',
  600: 'hsl(14, 100%, 50%)',
  700: 'hsl(10, 100%, 45%)',
  800: 'hsl(6, 100%, 40%)',
  900: 'hsl(2, 100%, 35%)',
};

export const green = {
  50: 'hsl(142, 76%, 95%)',
  100: 'hsl(142, 72%, 90%)',
  200: 'hsl(142, 69%, 80%)',
  300: 'hsl(142, 66%, 65%)',
  400: 'hsl(142, 63%, 42%)',
  500: 'hsl(142, 70%, 32%)',
  600: 'hsl(142, 75%, 27%)',
  700: 'hsl(142, 80%, 18%)',
  800: 'hsl(142, 85%, 12%)',
  900: 'hsl(142, 90%, 8%)',
};

export const orange = {
  50: 'hsl(35, 100%, 97%)',
  100: 'hsl(35, 92%, 90%)',
  200: 'hsl(35, 94%, 80%)',
  300: 'hsl(35, 90%, 65%)',
  400: 'hsl(35, 91%, 60%)',
  500: 'hsl(35, 90%, 50%)',
  600: 'hsl(30, 91%, 45%)',
  700: 'hsl(25, 94%, 40%)',
  800: 'hsl(20, 95%, 35%)',
  900: 'hsl(15, 93%, 30%)',
};

export const red = {
  50: 'hsl(0, 100%, 97%)',
  100: 'hsl(0, 92%, 90%)',
  200: 'hsl(0, 94%, 80%)',
  300: 'hsl(0, 90%, 65%)',
  400: 'hsl(0, 90%, 60%)',
  500: 'hsl(0, 90%, 50%)',
  600: 'hsl(0, 91%, 45%)',
  700: 'hsl(0, 94%, 40%)',
  800: 'hsl(0, 95%, 35%)',
  900: 'hsl(0, 93%, 30%)',
};

export const getDesignTokens = (mode) => {
  customShadows[1] =
    mode === 'dark'
      ? 'hsla(20, 30%, 5%, 0.7) 0px 4px 16px 0px, hsla(20, 25%, 10%, 0.8) 0px 8px 16px -5px'
      : 'hsla(30, 30%, 5%, 0.07) 0px 4px 16px 0px, hsla(30, 25%, 10%, 0.07) 0px 8px 16px -5px';

  return {
    palette: {
      mode,
      primary: {
        light: mode === 'dark' ? sunset[300] : brand[300],
        main: mode === 'dark' ? sunset[400] : brand[400],
        dark: mode === 'dark' ? sunset[600] : brand[600],
        contrastText: mode === 'dark' ? 'hsl(0, 0%, 10%)' : 'hsl(0, 0%, 100%)',
      },
      secondary: {
        light: mode === 'dark' ? 'hsl(291, 64%, 60%)' : 'hsl(185, 58%, 60%)',
        main: mode === 'dark' ? 'hsl(291, 64%, 42%)' : 'hsl(185, 58%, 42%)',
        dark: mode === 'dark' ? 'hsl(291, 64%, 30%)' : 'hsl(185, 58%, 30%)',
        contrastText: 'hsl(0, 0%, 100%)',
      },
      info: {
        light: mode === 'dark' ? 'hsl(199, 89%, 60%)' : 'hsl(199, 89%, 60%)',
        main: mode === 'dark' ? 'hsl(199, 89%, 48%)' : 'hsl(199, 89%, 48%)',
        dark: mode === 'dark' ? 'hsl(199, 89%, 38%)' : 'hsl(199, 89%, 38%)',
        contrastText: 'hsl(0, 0%, 100%)',
      },
      warning: {
        light: orange[300],
        main: orange[500],
        dark: orange[700],
        contrastText: mode === 'dark' ? 'hsl(0, 0%, 10%)' : 'hsl(0, 0%, 10%)',
      },
      error: {
        light: red[300],
        main: red[500],
        dark: red[700],
        contrastText: 'hsl(0, 0%, 100%)',
      },
      success: {
        light: green[300],
        main: green[500],
        dark: green[700],
        contrastText: 'hsl(0, 0%, 100%)',
      },
      grey: {
        ...gray,
      },
      divider: mode === 'dark' ? alpha(sunset[700], 0.6) : alpha(gray[300], 0.4),
      background: {
        default: mode === 'dark' ? 'hsl(12, 23%, 15%)' : 'hsl(30, 43%, 99%)',
        paper: mode === 'dark' ? 'hsl(10, 23%, 18%)' : 'hsl(30, 33%, 97%)',
      },
      text: {
        primary: mode === 'dark' ? 'hsl(30, 20%, 95%)' : 'hsl(30, 25%, 15%)',
        secondary: mode === 'dark' ? 'hsl(30, 15%, 80%)' : 'hsl(30, 25%, 35%)',
        warning: orange[400],
      },
      action: {
        hover: mode === 'dark' ? alpha(sunset[600], 0.2) : alpha(gray[200], 0.2),
        selected: mode === 'dark' ? alpha(sunset[600], 0.3) : alpha(gray[200], 0.3),
      },
    },
    typography: {
      fontFamily: 'Ravenholm, Inter, sans-serif',
      h1: {
        fontSize: defaultTheme.typography.pxToRem(48),
        fontWeight: 600,
        lineHeight: 1.2,
        letterSpacing: -0.5,
      },
      h2: {
        fontSize: defaultTheme.typography.pxToRem(36),
        fontWeight: 600,
        lineHeight: 1.2,
      },
      h3: {
        fontSize: defaultTheme.typography.pxToRem(30),
        lineHeight: 1.2,
      },
      h4: {
        fontSize: defaultTheme.typography.pxToRem(24),
        fontWeight: 600,
        lineHeight: 1.5,
      },
      h5: {
        fontSize: defaultTheme.typography.pxToRem(20),
        fontWeight: 600,
      },
      h6: {
        fontSize: defaultTheme.typography.pxToRem(18),
        fontWeight: 600,
      },
      subtitle1: {
        fontSize: defaultTheme.typography.pxToRem(18),
      },
      subtitle2: {
        fontSize: defaultTheme.typography.pxToRem(14),
        fontWeight: 500,
      },
      body1: {
        fontSize: defaultTheme.typography.pxToRem(14),
      },
      body2: {
        fontSize: defaultTheme.typography.pxToRem(14),
        fontWeight: 400,
      },
      caption: {
        fontSize: defaultTheme.typography.pxToRem(12),
        fontWeight: 400,
      },
    },
    shape: {
      borderRadius: 8,
    },
    shadows: customShadows,
  };
};

export const colorSchemes = {
  light: {
    palette: {
      primary: {
        light: brand[300],
        main: brand[400],
        dark: brand[600],
        contrastText: 'hsl(0, 0%, 100%)',
      },
      secondary: {
        light: 'hsl(185, 58%, 60%)',
        main: 'hsl(185, 58%, 42%)',
        dark: 'hsl(185, 58%, 30%)',
        contrastText: 'hsl(0, 0%, 100%)',
      },
      info: {
        light: 'hsl(199, 89%, 60%)',
        main: 'hsl(199, 89%, 48%)',
        dark: 'hsl(199, 89%, 38%)',
        contrastText: 'hsl(0, 0%, 100%)',
      },
      warning: {
        light: orange[300],
        main: orange[500],
        dark: orange[700],
        contrastText: 'hsl(0, 0%, 10%)',
      },
      error: {
        light: red[300],
        main: red[500],
        dark: red[700],
        contrastText: 'hsl(0, 0%, 100%)',
      },
      success: {
        light: green[300],
        main: green[500],
        dark: green[700],
        contrastText: 'hsl(0, 0%, 100%)',
      },
      grey: {
        ...gray,
      },
      divider: alpha(gray[300], 0.4),
      background: {
        default: 'hsl(30, 43%, 99%)',
        paper: 'hsl(30, 33%, 97%)',
      },
      text: {
        primary: 'hsl(30, 25%, 15%)',
        secondary: 'hsl(30, 25%, 35%)',
        warning: orange[400],
      },
      action: {
        hover: alpha(gray[200], 0.2),
        selected: `${alpha(gray[200], 0.3)}`,
      },
      baseShadow:
        'hsla(30, 30%, 5%, 0.07) 0px 4px 16px 0px, hsla(30, 25%, 10%, 0.07) 0px 8px 16px -5px',
    },
  },
  dark: {
    palette: {
      primary: {
        contrastText: 'hsl(0, 0%, 10%)',
        light: sunset[300],
        main: sunset[400],
        dark: sunset[600],
      },
      secondary: {
        contrastText: 'hsl(0, 0%, 100%)',
        light: 'hsl(291, 64%, 60%)',
        main: 'hsl(291, 64%, 42%)',
        dark: 'hsl(291, 64%, 30%)',
      },
      info: {
        contrastText: 'hsl(0, 0%, 100%)',
        light: 'hsl(199, 89%, 60%)',
        main: 'hsl(199, 89%, 48%)',
        dark: 'hsl(199, 89%, 38%)',
      },
      warning: {
        light: orange[400],
        main: orange[500],
        dark: orange[700],
        contrastText: 'hsl(0, 0%, 10%)',
      },
      error: {
        light: red[400],
        main: red[500],
        dark: red[700],
        contrastText: 'hsl(0, 0%, 100%)',
      },
      success: {
        light: green[400],
        main: green[500],
        dark: green[700],
        contrastText: 'hsl(0, 0%, 100%)',
      },
      grey: {
        ...gray,
      },
      divider: alpha(sunset[700], 0.6),
      background: {
        default: 'hsl(12, 23%, 15%)',
        paper: 'hsl(10, 23%, 18%)',
      },
      text: {
        primary: 'hsl(30, 20%, 95%)',
        secondary: 'hsl(30, 15%, 80%)',
      },
      action: {
        hover: alpha(sunset[600], 0.2),
        selected: alpha(sunset[600], 0.3),
      },
      baseShadow:
        'hsla(20, 30%, 5%, 0.7) 0px 4px 16px 0px, hsla(20, 25%, 10%, 0.8) 0px 8px 16px -5px',
    },
  },
};

export const typography = {
  fontFamily: 'Ravenholm, Inter, sans-serif',
  h1: {
    fontSize: defaultTheme.typography.pxToRem(48),
    fontWeight: 600,
    lineHeight: 1.2,
    letterSpacing: -0.5,
  },
  h2: {
    fontSize: defaultTheme.typography.pxToRem(36),
    fontWeight: 600,
    lineHeight: 1.2,
  },
  h3: {
    fontSize: defaultTheme.typography.pxToRem(30),
    lineHeight: 1.2,
  },
  h4: {
    fontSize: defaultTheme.typography.pxToRem(24),
    fontWeight: 600,
    lineHeight: 1.5,
  },
  h5: {
    fontSize: defaultTheme.typography.pxToRem(20),
    fontWeight: 600,
  },
  h6: {
    fontSize: defaultTheme.typography.pxToRem(18),
    fontWeight: 600,
  },
  subtitle1: {
    fontSize: defaultTheme.typography.pxToRem(18),
  },
  subtitle2: {
    fontSize: defaultTheme.typography.pxToRem(14),
    fontWeight: 500,
  },
  body1: {
    fontSize: defaultTheme.typography.pxToRem(14),
  },
  body2: {
    fontSize: defaultTheme.typography.pxToRem(14),
    fontWeight: 400,
  },
  caption: {
    fontSize: defaultTheme.typography.pxToRem(12),
    fontWeight: 400,
  },
};

export const shape = {
  borderRadius: 8,
};

const defaultShadows = [
  'none',
  'var(--template-palette-baseShadow)',
  ...defaultTheme.shadows.slice(2),
];

export const shadows = defaultShadows;