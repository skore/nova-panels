let mix = require('laravel-mix')

mix.setPublicPath('dist')
  .js('resources/js/fields.js', 'js')
  // .sass('resources/scss/fields.scss', 'css')
  // .postCss('dist/css/fields.css', 'css', [
  //   require("tailwindcss"),
  // ])

// if (mix.inProduction()) {
//   mix.options({
//     clearConsole: true
//   });
// }