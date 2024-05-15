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
         tcf: './public/js/tcf/src/index.js',
         main: './src/vue-settings.js',
         dashboard: './src/vue-dashboard.js'
         
     },
     output: {
         path: path.resolve(__dirname, 'admin/js/vue'),
         filename: 'gdpr-cookie-consent-admin-[name].js'
     },
     mode: 'production',
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
             },
             {
                test: /\.(gif|svg|jpg|png)$/,
                use: ['file-loader']
             }
         ]
     },
 }

 module.exports = [
     vueconfig      
 ];