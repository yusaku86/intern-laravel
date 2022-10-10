const mix = require('laravel-mix');
const glob = require('glob');

// Scssビルド
glob.sync('resources/scss/*.scss').map(function (file) {
    mix.sass(file, 'public/css');
});

// cssビルド
glob.sync('resources/css/*.css').map(function (file) {
    mix.css(file, 'public/css');
});

// jsビルド
glob.sync('resources/js/*.js').map(function (file) {
    mix.js(file, 'public/js');
});

mix.version();

// 子要素のバンドル時のデータ設定
mix.webpackConfig({
    stats: {
        children: true,
    },
});
