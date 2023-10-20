/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./node_modules/flowbite/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                darkblue: '#151a25',
                lightblue: '#4173d2',
                spred: '#ae0000'
            }
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('flowbite/plugin'),
    ],
}
