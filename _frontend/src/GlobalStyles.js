// src/GlobalStyles.js
import { Global, css } from '@emotion/react';
import RavenThinOtf from './assets/fonts/Ravenholm-Thin.otf';
import RavenThinTtf from './assets/fonts/Ravenholm-Thin.ttf';
import RavenBoldOtf from './assets/fonts/Ravenholm-Bold.otf';
import RavenBoldTtf from './assets/fonts/Ravenholm-Bold.ttf';
// … et ainsi de suite

export function GlobalStyles() {
  return (
    <Global
      styles={css`
        @font-face {
          font-family: 'Ravenholm';
          font-weight: 300;
          src:
            url(${RavenThinOtf}) format('opentype'),
            url(${RavenThinTtf}) format('truetype');
          font-display: swap;
        }
        @font-face {
          font-family: 'Ravenholm';
          font-weight: 700;
          src:
            url(${RavenBoldOtf}) format('opentype'),
            url(${RavenBoldTtf}) format('truetype');
          font-display: swap;
        }
        /* … Inline et Color de la même façon */
      `}
    />
  );
}
