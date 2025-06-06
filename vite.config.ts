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
    outDir: 'public/assets/css',
    rollupOptions: {
      input: {
        main: './src/main.css'
      },
      output: {
        // Customize asset file names
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === "main.css") {
            return "module.css"; // Specify your desired CSS file name
          }
          return "assets/[name]-[hash][extname]"; // Default for other assets
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
