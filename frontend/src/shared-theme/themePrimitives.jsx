import { createTheme, alpha } from '@mui/material/styles';

const defaultTheme = createTheme();

const customShadows = [...defaultTheme.shadows];

export const brand = {
  50: 'hsl(300, 70%, 95%)',
  100: 'hsl(300, 70%, 92%)',
  200: 'hsl(300, 70%, 80%)',
  300: 'hsl(300, 70%, 65%)',
  400: 'hsl(300, 98%, 48%)',
  500: 'hsl(300, 98%, 42%)',
  600: 'hsl(300, 98%, 55%)',
  700: 'hsl(300, 70%, 35%)',
  800: 'hsl(300, 70%, 16%)',
  900: 'hsl(300, 70%, 21%)',
};

export const brandAnalogousPurple = {
  50: '#f4e5f6',
  100: '#e4bfe9',
  200: '#d394db',
  300: '#c069cc',
  400: '#b248c1',
  500: '#a427b6',
  600: '#9624b0',
  700: '#831fa8',
  800: '#711ba0',
  900: '#511493',
};

export const brandAnalogousRed = {
  50: '#fbe4ec',
  100: '#f6bbcf',
  200: '#f08faf',
  300: '#eb6390',
  400: '#e64378',
  500: '#e22761',
  600: '#d1245e',
  700: '#bb2059',
  800: '#a61d55',
  900: '#81174c',
};

export const silver = {
  50: '#fff9ec',
  100: '#fdf3e7',
  200: '#f6ece0',
  300: '#e7ded2',
  400: '#c4bbaf',
  500: '#a59c90',
  600: '#7b7368',
  700: '#675f55',
  800: '#474036',
  900: '#251f16',
};


export const gray = {
  50: 'hsl(220, 35%, 97%)',
  100: 'hsl(220, 30%, 94%)',
  200: 'hsl(220, 20%, 88%)',
  300: 'hsl(220, 20%, 80%)',
  400: 'hsl(220, 20%, 65%)',
  500: 'hsl(220, 20%, 42%)',
  600: 'hsl(220, 20%, 35%)',
  700: 'hsl(220, 20%, 25%)',
  800: 'hsl(220, 30%, 6%)',
  900: 'hsl(220, 35%, 3%)',
};

export const green = {
  50: 'hsl(120, 80%, 98%)',
  100: 'hsl(120, 75%, 94%)',
  200: 'hsl(120, 75%, 87%)',
  300: 'hsl(120, 61%, 77%)',
  400: 'hsl(120, 44%, 53%)',
  500: 'hsl(120, 59%, 30%)',
  600: 'hsl(120, 70%, 25%)',
  700: 'hsl(120, 75%, 16%)',
  800: 'hsl(120, 84%, 10%)',
  900: 'hsl(120, 87%, 6%)',
};

export const orange = {
  50: 'hsl(45, 100%, 97%)',
  100: 'hsl(45, 92%, 90%)',
  200: 'hsl(45, 94%, 80%)',
  300: 'hsl(45, 90%, 65%)',
  400: 'hsl(45, 90%, 40%)',
  500: 'hsl(45, 90%, 35%)',
  600: 'hsl(45, 91%, 25%)',
  700: 'hsl(45, 94%, 20%)',
  800: 'hsl(45, 95%, 16%)',
  900: 'hsl(45, 93%, 12%)',
};

export const red = {
  50: 'hsl(0, 100%, 97%)',
  100: 'hsl(0, 92%, 90%)',
  200: 'hsl(0, 94%, 80%)',
  300: 'hsl(0, 90%, 65%)',
  400: 'hsl(0, 90%, 40%)',
  500: 'hsl(0, 90%, 30%)',
  600: 'hsl(0, 91%, 25%)',
  700: 'hsl(0, 94%, 18%)',
  800: 'hsl(0, 95%, 12%)',
  900: 'hsl(0, 93%, 6%)',
};

export const colorSchemes = {
  light: {
    palette: {
      primary: {
        light: brand[200],
        main: brand[400],
        dark: brand[700],
        contrastText: brand[50],
      },
      info: {
        light: brand[100],
        main: brand[300],
        dark: brand[600],
        contrastText: gray[50],
      },
      warning: {
        light: orange[300],
        main: orange[400],
        dark: orange[800],
      },
      error: {
        light: red[300],
        main: red[400],
        dark: red[800],
      },
      success: {
        light: green[300],
        main: green[400],
        dark: green[800],
      },
      grey: {
        ...gray,
      },
      divider: alpha(gray[300], 0.4),
      background: {
        default: 'hsl(0, 0%, 99%)',
        lightRed: brandAnalogousRed[50],
        surface: 'hsl(0, 0%, 99%)',
        card: `${alpha(gray[50], 0.9)}`,
        paper: brand[50], //#F9F5FA
      },
      text: {
        primary: gray[800],
        secondary: gray[600],
        warning: orange[400],
      },
      action: {
        hover: alpha(gray[200], 0.2),
        selected: `${alpha(gray[200], 0.3)}`,
      },
      baseShadow:
        'hsla(220, 30%, 5%, 0.07) 0px 4px 16px 0px, hsla(220, 25%, 10%, 0.07) 0px 8px 16px -5px',
    },
  },
  dark: {
    palette: {
      primary: {
        contrastText: brand[50],
        light: brand[300],
        main: brand[400],
        dark: brand[700],
      },
      info: {
        contrastText: brand[300],
        light: brand[500],
        main: brand[700],
        dark: brand[900],
      },
      warning: {
        light: orange[400],
        main: orange[500],
        dark: orange[700],
      },
      error: {
        light: red[400],
        main: red[500],
        dark: red[700],
      },
      success: {
        light: green[400],
        main: green[500],
        dark: green[700],
      },
      grey: {
        ...gray,
      },
      divider: alpha(gray[700], 0.6),
      background: {
        default: gray[900],
        surface: gray[900],        
        card: `${alpha('#2C323C', 0.9)}`,
        paper: gray[800],
      },
      text: {
        primary: 'hsl(0, 0%, 100%)',
        secondary: gray[400],
      },
      action: {
        hover: alpha(gray[600], 0.2),
        selected: alpha(gray[600], 0.3),
      },
      baseShadow:
        'hsla(220, 30%, 5%, 0.7) 0px 4px 16px 0px, hsla(220, 25%, 10%, 0.8) 0px 8px 16px -5px',
    },
  },
};

export const typography = {
  fontFamily: 'Inter, sans-serif',
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
