/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    './vendor/usernotnull/tall-toasts/config/**/*.php',
    './vendor/usernotnull/tall-toasts/resources/views/**/*.blade.php',
  ],
  theme: {
    // namedGroups: ["tooltip", "bar"],
    screens: {
      '2xl/max': { 'max': '1535px' },
      'xl/max': { 'max': '1279px' },
      'lg/max': { 'max': '1023px' },
      'md/max': { 'max': '767px' },
      'sm/max': { 'max': '639px' },
      'xs/max': { 'max': '425px' },
    },
    extend: {
      colors: {
        'violet-25': '#fbfbff',
      },
      borderRadius: {
        '1': '1px',
        '2': '2px',
        '3': '3px',
        '4': '4px',
        '5': '5px',
        '6': '6px',
        '7': '7px',
        '8': '8px',
        '9': '9px',
        '10': '10px',
      },
      flex: {
        '1': '1',
        '2': '2',
        '3': '3',
        '4': '4',
        '5': '5',
        '6': '6',
        '7': '7',
        '8': '8',
        '9': '9',
        '10': '10',
      },
      boxShadow: {
        'custom2pxGreen': '0px 0px 2px 2px rgb(21, 128, 62)',
        'xs/focused(4px-primary)': '0px 1px 2px rgba(16, 24, 40, 0.05), 0px 0px 0px 4px #f5f3ff',
        'xs/focused(2px-primary)': '0px 1px 2px rgba(16, 24, 40, 0.05), 0px 0px 0px 2px #f5f3ff',
      }
    },
  },
  plugins: [
    // require("tailwindcss-named-groups"),
  ],
}
