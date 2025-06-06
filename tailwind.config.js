/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './App/templates/**/*.blade.php', // Scanner les fichiers Blade
        // './public/assets/js/**/*.js', // Scanner les fichiers JS
        // './public/**/*.php', // Scanner les fichiers PHP
        // './App/**/*.php', // Scanner les fichiers PHP de l'app
    ],
    theme: {
        extend: {},
    },
    plugins: [require('daisyui')],
};