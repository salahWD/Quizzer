import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/js/app.js",
        "resources/js/quiz.js",
        "resources/js/case-study.js",
        "resources/sass/home.scss",
        "resources/sass/features.scss",
        "resources/sass/reports.css",
        "resources/sass/case-study.scss",
        "resources/sass/create-tables.scss",
        "resources/sass/manage-tables.scss",
        "resources/sass/app.scss",
        "resources/sass/dashboard.scss",
        "resources/sass/admin.scss",
        "resources/sass/templates-for-nonuser.css",
        "resources/sass/quiz.scss",
        "resources/sass/ui.scss",
      ],
      refresh: true,
    }),
  ],
});
