var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addPlugin(new CopyWebpackPlugin([
        { from: './assets/fonts', to: 'fonts' }
    ]))
    
    .addEntry('app', './assets/js/app.js')
    .addEntry('conversation', './assets/js/conversation.js')

    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
