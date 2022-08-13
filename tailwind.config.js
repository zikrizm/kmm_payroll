/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      boxShadow: {
        'custom2pxGreen' : '0px 0px 2px 2px rgb(21, 128, 62)'
      }
    },
  },
  plugins: [
  ],
}