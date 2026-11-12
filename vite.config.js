import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  build: {
    emptyOutDir: true,
    rollupOptions: {
      // Supplied by the control panel's import map, so the panel runs on the
      // control panel's own Vue instance.
      external: ['vue', '@craftcms/cp'],
    },
  },
  plugins: [
    laravel({
      input: ['resources/js/cp.js'],
      publicDirectory: 'public',
      buildDirectory: 'build',
      hotFile: 'public/hot',
      refresh: false,
    }),
    vue({
      template: {
        compilerOptions: {
          // The control panel's web components, such as `craft-indicator`
          isCustomElement: (tag) => tag.startsWith('craft-'),
        },
      },
    }),
  ],
});
