/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php', // Scanner les fichiers Blade
        './resources/js/**/*.js', // Scanner les fichiers JS si nécessaire
    ],
    theme: {
        extend: {},
    },
    plugins: [require('daisyui')],
};