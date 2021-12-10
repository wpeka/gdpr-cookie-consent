/**
 * Config file for webpack.
 *
 * @link  https://club.wpeka.com
 * @since 1.0.0
 *
 * @package Gdpr_Cookie_Consent
 */
 const path = require('path');
 const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");
 
 var vueconfig = {
     entry: {
         main: './src/vue-settings.js',
         
     },
     output: {
         path: path.resolve(__dirname, 'admin/js/vue'),
         filename: 'gdpr-cookie-consent-admin-[name].js'
     },
     mode: 'development',
     resolve: {
         alias: {
             'vue$': 'vue/dist/vue.esm.js'
         }
     },
     module: {
         rules: [
             {
                 test: /\.css$/,
                 use: ['style-loader', 'css-loader']
             }
         ]
     }
 }

 module.exports = [
     vueconfig      
 ];