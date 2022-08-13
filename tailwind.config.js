/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    screens: {
      '2xl/max': { 'max': '1535px' },
      'xl/max': { 'max': '1279px' },
      'lg/max': { 'max': '1023px' },
      'md/max': { 'max': '767px' },
      'sm/max': { 'max': '639px' },
      'xs/max': { 'max': '425px' },

      '2xl': { 'max': '1535px' },
      'xl': { 'max': '1279px' },
      'lg': { 'max': '1023px' },
      'md': { 'max': '767px' },
      'sm': { 'max': '639px' },
    },
    extend: {
      boxShadow: {
        'custom2pxGreen' : '0px 0px 2px 2px rgb(21, 128, 62)'
      }
    },
  },
  plugins: [
  ],
}