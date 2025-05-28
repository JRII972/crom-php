import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    sourcemap: true, // Keep source maps enabled for your own code
    rollupOptions: {
      onwarn(warning, warn) {
        // Suppress source map warnings from node_modules
        if (warning.code === 'SOURCEMAP_ERROR' && warning.loc?.file?.includes('node_modules')) {
          return;
        }
        warn(warning); // Log other warnings
      },
    },
  },
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:80',
        changeOrigin: true,
        secure: false,
      },
    },
  },
});
