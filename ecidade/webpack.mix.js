const mix = require('laravel-mix');
const webpack = require('unplugin-vue-components/webpack');
const { PrimeVueResolver } = require('unplugin-vue-components/resolvers');
const LiveReloadPlugin = require('webpack-livereload-plugin');
const EncodingPlugin = require("encoding-plugin");
const path = require('path');

/**
 * LEGADO
 */
mix.js('resources/assets/js/app.js', 'public/js')
    .js(
        'resources/assets/js/extension/package/desktop/views/desktop/index.js',
        'public/extension/package/desktop/views/desktop'
    )
    .js(
        'resources/vue/modulo/rh/recadastramento.js',
        'public/vue/modulo/rh'
    )
    .sass('resources/assets/sass/app.scss', 'public/css');

mix.styles([
    'node_modules/primeicons/primeicons.css',
    'node_modules/primevue/resources/themes/fluent-light/theme.css',
    'node_modules/primevue/resources/primevue.min.css',
    'node_modules/primeflex/primeflex.min.css'
], 'public/primevue.css');

mix.copyDirectory('node_modules/primeicons/fonts', 'public/fonts');

mix.sass(
    'resources/vue/assets/sass/app.scss',
    'public/vue/css/style.css',
);

mix.autoload({
    jquery: ['$', 'jQuery', 'window.jQuery'],
});

mix.alias({
    '@modules': path.join(__dirname, 'resources/vue/modules'),
    '@utils': path.join(__dirname, 'resources/vue/utils')
})

mix.copy('node_modules/trumbowyg/dist/ui/icons.svg','public/trumbowyg/ui/icons.svg')

mix.js(
    'resources/vue/App.js',
    'public/vue/js/app.js'
).webpackConfig({
    plugins: [
        webpack({
            resolvers: [
                PrimeVueResolver()
            ]
        }),
        new LiveReloadPlugin(),
        new EncodingPlugin({
            encoding: 'iso-8859-1',
            include: [
                /vue\/modulo/
            ]
        }),
    ]
}).vue({ version: 3 });

mix.version();
