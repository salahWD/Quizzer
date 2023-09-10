import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/sass/app.scss",
        "resources/js/app.js",
        "resources/js/quiz.js",
        "resources/sass/home.scss",
        "resources/sass/dashboard.scss",
        "resources/sass/create-tables.scss",
        "resources/sass/manage-tables.scss",
        "resources/sass/quiz.scss",
        "resources/sass/admin.scss",
        "resources/sass/reports.css",
        "resources/sass/templates-for-nonuser.css",
      ],
      refresh: true,
    }),
  ],
});
