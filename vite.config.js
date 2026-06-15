import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import compression from 'vite-plugin-compression' // 👈 ESTA LÍNEA FALTABA

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/sass/app.scss',
        'resources/js/app.js',
      ],
      refresh: true,
    }),
    compression() // 👈 ahora sí funciona correctamente
  ],
  esbuild: {
    drop: ['console', 'debugger'],
  },
})