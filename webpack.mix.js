const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

if (!mix.inProduction()) {
	mix
		.sourceMaps()
		.version();
}

mix
	.autoload({
		jquery: ['$', 'window.jQuery'],
	});

mix
	// App
	.sass('resources/scss/app.scss', 'public/bundles/app')
	.js('resources/scripts/app.js', 'public/bundles/app');
