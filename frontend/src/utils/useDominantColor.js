import { useState, useEffect } from 'react';
import { Vibrant } from 'node-vibrant/browser';

export function useDominantColor(imageUrl) {
  const [colors, setColors] = useState(null);

  useEffect(() => {
    if (!imageUrl) return;
    Vibrant.from(imageUrl).getPalette()
      .then(palette => {
        console.log(palette)
        console.log(palette.Vibrant.hex)
        setColors({
          primary:   {
            main: palette.Vibrant.hex,
            light: palette.LightVibrant.hex,
            dark: palette.DarkVibrant.hex,
          },
          secondary: {
            main: palette.Muted.hex,
            light: palette.LightMuted.hex,
            dark: palette.DarkMuted.hex,
          },
        });
      })
      .catch(err => console.error(err));
  }, [imageUrl]);
  console.log(colors)
  return colors;
}