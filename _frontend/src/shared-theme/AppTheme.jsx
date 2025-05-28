import * as React from 'react';
import PropTypes from 'prop-types';
import { ThemeProvider, createTheme } from '@mui/material/styles';

import { inputsCustomizations } from './customizations/inputs';
import { dataDisplayCustomizations } from './customizations/dataDisplay';
import { feedbackCustomizations } from './customizations/feedback';
import { navigationCustomizations } from './customizations/navigation';
import { surfacesCustomizations } from './customizations/surfaces';
import { colorSchemes, typography, shadows, shape } from './themePrimitives';

function AppTheme(props) {
  const { children, disableCustomTheme, themeComponents } = props;
  const theme = React.useMemo(() => {
    return disableCustomTheme
      ? {}
      : createTheme({
        
          // For more details about CSS variables configuration, see https://mui.com/material-ui/customization/css-theme-variables/configuration/
          cssVariables: {
            colorSchemeSelector: 'data-mui-color-scheme',
            cssVarPrefix: 'template',
          },
          colorSchemes, // Recently added in v6 for building light & dark mode app, see https://mui.com/material-ui/customization/palette/#color-schemes
          typography,
          shadows,
          shape,
          components: {
            ...inputsCustomizations,
            ...dataDisplayCustomizations,
            ...feedbackCustomizations,
            ...navigationCustomizations,
            ...surfacesCustomizations,
            ...themeComponents,
            MuiCssBaseline: {
              styleOverrides: `
                *,
                *::before,
                *::after {
                  transition: background-color 1s ease;
                }
              `,
            },
          },
          transitions: {
            duration: {
              shortest: 150,
              shorter: 200,
              short: 250,
              // most basic recommended timing
              standard: 300,
              // this is to be used in complex animations
              complex: 375,
              // recommended when something is entering screen
              enteringScreen: 225,
              // recommended when something is leaving screen
              leavingScreen: 195,
            },
            easing: {
              // This is the most common easing curve.
              easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)',
              // Objects enter the screen at full velocity from off-screen and
              // slowly decelerate to a resting point.
              easeOut: 'cubic-bezier(0.0, 0, 0.2, 1)',
              // Objects leave the screen at full velocity. They do not decelerate when off-screen.
              easeIn: 'cubic-bezier(0.4, 0, 1, 1)',
              // The sharp curve is used by objects that may return to the screen at any time.
              sharp: 'cubic-bezier(0.4, 0, 0.6, 1)',
            },
          },
        });
  }, [disableCustomTheme, themeComponents]);
  if (disableCustomTheme) {
    return <React.Fragment>{children}</React.Fragment>;
  }
  return (
    <ThemeProvider theme={theme} >
      {children}
    </ThemeProvider>
  );
}

AppTheme.propTypes = {
  children: PropTypes.node,
  /**
   * This is for the docs site. You can ignore it or remove it.
   */
  disableCustomTheme: PropTypes.bool,
  themeComponents: PropTypes.object,
};

export default AppTheme;
