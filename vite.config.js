import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path"; // 👈 necesario para el alias

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"), // 👈 el alias que usás con "@/..."
        },
        extensions: [".js", ".vue", ".json"], // 👈 permite omitir ".vue" en imports
    },
});
