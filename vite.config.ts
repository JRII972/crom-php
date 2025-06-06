import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react-swc'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
  ],
  publicDir: false, // Désactive la copie automatique du dossier public
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: './src/main.css'
      },
      output: {
        // Customize asset file names
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === "main.css") {
            return "module.css"; // Directement à la racine du dist
          }
          return "[name]-[hash][extname]"; // Default for other assets
        },
      },
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@import "./public/assets/scss/index.scss";`
      }
    }
  }
})
