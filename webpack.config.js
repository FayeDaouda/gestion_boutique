// webpack.config.js
const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/js/app.js') // Assurez-vous que le chemin vers votre fichier JavaScript est correct
    .enablePostCssLoader()
    .enableSingleRuntimeChunk();

module.exports = Encore.getWebpackConfig();
