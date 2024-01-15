import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/css/welcome.css",
        "resources/js/app.js",
        "resources/js/videos/form.js",
        "resources/js/videos/show.js",
        "resources/js/models/form.js",
        "resources/js/lives/form.js",
        "resources/js/lives/show.js",
      ],
      refresh: true,
    }),
  ],
});
