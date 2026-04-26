import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";

export default defineConfig({
    plugins: [
        symfonyPlugin(),
    ],

    build: {
        rollupOptions: {
            input: {
                toolbar: './src/assets/js/toolbar.ts',
            },
            output: {
                entryFileNames: `[name].js`,
                chunkFileNames: `[name].js`,
                assetFileNames: `[name].[ext]`,
            },
        },
        outDir: "./bundle/Resources/public/assets/"
    },
});
