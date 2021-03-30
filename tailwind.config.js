module.exports = {
  purge: [
    "resources/js/components/**/*.vue",
    "resources/js/components/**/*.js"
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
  },
  variants: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms')
  ],
}
