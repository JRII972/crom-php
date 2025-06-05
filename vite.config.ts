import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react-swc'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
  ],
  build: {
    rollupOptions: {
      output: {
        // Customize asset file names
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === "index.css") {
            return "assets/module.css"; // Specify your desired CSS file name
          }
          return "assets/[name]-[hash][extname]"; // Default for other assets
        },
      },
    },
  },
})
