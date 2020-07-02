// --------------------------------
// Gulp Plugins
// --------------------------------

/**
 * Gulp - Duh
 */
const gulp = require('gulp');

/**
 * Translation Related
 */
const wpPot = require('gulp-wp-pot');

/**
 * SVG
 */
const svgo = require('gulp-svgo');

/**
 * Other
 */
const fs = require('fs');
const del = require('del');
const zip = require('gulp-zip');
const browserSync = require('browser-sync').create();
const rename = require('gulp-rename');

/**
 * Sass
 * @see gulp/process-scss.js
 */
const processSCSS = require('./gulp/process-scss').init(browserSync);

/**
 * Javascript
 * @see gulp/process-js.js
 */
const processJS = require('./gulp/process-js');

// --------------------------------
// Globals
// --------------------------------

/**
 * Let's us access the contents of package.json as an object.
 * @type {Object}
 */
const packagejson = JSON.parse(fs.readFileSync('./package.json'));

/**
 * The host you'd like to use while working locally.
 * @type {String}
 */
const host = 'conleyforcongress.local';
const protocol = 'https';

/**
 * Enables syncing from /build/acf-json/* to /src/acf-json/*.json.
 *
 * Useful for local development when symlinking the /build/ directory
 * to your local environment's working theme directory, so that changes
 * in the ACF UI in the admin will automatically be copied back to the
 * repo's src folder for version control.
 *
 * @type {Boolean}
 */
const enableACFsync = true;

/**
 * Paths to files in /src directory.
 * @type {Object}
 */
const src = {
	root: 'src',
};

src.scss = `${src.root}/scss`;
src.js = `${src.root}/js`;
src.images = `${src.root}/images`;
src.icons = `${src.images}/icons`;
src.acfJson = `${src.root}/acf-json`;

/**
 * Paths to files in /build directory.
 * @type {Object}
 */
const build = {
	root: 'build',
};

build.css = `${build.root}/css`;
build.js = `${build.root}/js`;
build.images = `${build.root}/images`;
build.icons = `${build.images}/icons`;
build.acfJson = `${build.root}/acf-json`;
build.languages = `${build.root}/languages`;

/**
 * Reusable file matching globs.
 * @type {Object}
 */
const globs = {
	src: {
		js: [
			`${src.js}/**/*.js`,
		],
		scss: [
			`${src.scss}/**/*.scss`,
		],
		svg: [
			`${src.images}/**/*.svg`,
		],
		icons: [
			`${src.icons}/**/*.svg`,
		],
		other: [
			`${src.root}/**`,
			`!${src.scss}{,/**}`,
			`!${src.js}{,/**}`,
			`!${src.images}/**/*.svg`,
			`!${src.icons}{,/**}`,
			`!${src.acfJson}{,/**}`,
		],
		acfJson: [
			`${src.acfJson}/**/*`,
		],
		phpFiles: [
			`${src.root}/**/*.php`,
		],
	},
	build: {
		js: [
			`${build.js}/**/*.js`,
		],
		css: [
			`${build.css}/**/*.css`,
		],
		svg: [
			`${build.images}/**/*.svg`,
		],
		icons: [
			`${build.icons}/**/*.svg`,
		],
		other: [
			`${build.root}/**`,
			`!${build.root}`,
			`!${build.css}/**`,
			`!${build.js}/**`,
			`!${build.images}`,
			`!${build.images}/**/*.svg`,
			`!${build.icons}`,
			`!${build.icons}/**/*`,
			`!${build.root}/maps/**`,
			`!${build.acfJson}/**`,
			`!${build.languages}/**`,
		],
		acfJson: [
			`${build.acfJson}/**/*`,
		],
	},
};

/**
 * Delete all files, except js, css, and svg, from build directory.
 */
gulp.task('clean', () => del(globs.build.other));

/**
 * Move Files into Build Folder
 */
gulp.task('copy', () => {
	console.log('## Move all files (except .scss, .js, and .svg) into the build directory ##');
	return gulp.src(globs.src.other)
		.pipe(gulp.dest(build.root));
});

/**
 * Compress Build Files into Zip
 * Dependent on the build task completing
 *
 * @link https://www.npmjs.com/package/gulp-zip
 */
gulp.task('zip', () => {
	console.log('## Pack up our files into a zip into the dist directory ##');
	return gulp.src(`${build.root}/**`)
		.pipe(zip(`${packagejson.name}.zip`))
		.pipe(gulp.dest('dist'));
});

gulp.task('dist', gulp.series('clean', 'copy', 'zip'));

/**
 * CSS
 *
 * @see gulp/process-scss.js
 */

gulp.task('css:clean', () => del(globs.build.css));

gulp.task('css:process', () => processSCSS(globs.src.scss, build.css));

gulp.task('css', gulp.series('css:clean', 'css:process'));

/**
 * JavaScript
 *
 * @see gulp/process-js.js
 */
gulp.task('js:clean', () => del(globs.build.js));

gulp.task('js:process', (done) => {
	processJS.processDirectory(src.js, build.js);
	return done();
});

gulp.task('js', gulp.series('js:clean', 'js:process'));

/**
 * SVG Tasks
 *
 * @ref: https://www.npmjs.com/package/gulp-svgo
 */
gulp.task('svg:clean', () => del(globs.build.svg));

gulp.task('svg:copy', () => {
	console.log('## Move all .svg files to the build directory.');
	return gulp.src(globs.src.svg)
		.pipe(svgo({
			pretty: true,
			plugins: [
				{
					removeViewBox: false,
				},
				{
					removeDimensions: true,
				},
				{
					cleanupIDs: false,
				},
				{
					mergePaths: false,
				},
			],
		}))
		.pipe(rename((path) => {
			let filename = path.basename;
			filename = filename.toLowerCase();
			filename = filename.replace(/[\W_]+/g, '-');
			filename = filename.replace(/icon-([^-]+)-/, 'icon-');

			path.basename = filename;
		}))
		.pipe(gulp.dest(build.images));
});

gulp.task('svg', gulp.series('svg:clean', 'svg:copy'));

/**
 * Generate .pot file to hand off to translators
 *
 * @link https://www.npmjs.com/package/gulp-wp-pot
 * @link https://premium.wpmudev.org/blog/how-to-translate-a-wordpress-theme/
 */
gulp.task('i18n', () => {
	console.log('## Update translations.pot file ##');
	return gulp.src(globs.src.phpFiles)
		.pipe(wpPot({
			domain: packagejson.name,
			package: 'dc2dc',
		}))
		.pipe(gulp.dest(`${build.languages}/${packagejson.name}.pot`));
});

// --------------------------------
// Server Tasks
// --------------------------------

gulp.task('serve', (done) => {
	browserSync.init(
		{
			proxy: 'https://y4q.238.myftpupload.com/',
			files: ['build/**'],
			serveStatic: ['build'],
			rewriteRules: [
				{
					match: 'wp-content/themes/dc2dc/css/main.css?ver=1.0.0',
					replace: '/css/main.css',
				},
			],
		},
		() => {
			console.log('SITE WATCHING FOR CHANGES');
			return done();
		},
	);
});

/**
 * ACF local json sync tasks.
 */
gulp.task('acf-json:clean-build', () => del(globs.build.acfJson));

gulp.task('acf-json:copy-from-src', () => {
	console.log('## Move src acf-json files to build. ##');
	return gulp.src(globs.src.acfJson)
		.pipe(gulp.dest(build.acfJson));
});

gulp.task('acf-json:clean-src', () => del(globs.src.acfJson));

gulp.task('acf-json:copy-from-build', () => {
	console.log('## Move build acf-json files back to src. ##');
	return gulp.src(globs.build.acfJson)
		.pipe(gulp.dest(src.acfJson));
});

// The initial acf task called by default `gulp`. Copies src/acf-json to build/acf-json.
gulp.task('acf', gulp.series('acf-json:clean-build', 'acf-json:copy-from-src'));

// The watch task called conditionally if syncing is enabled; copies build changes back into src.
gulp.task('acf-sync', gulp.series('acf-json:clean-src', 'acf-json:copy-from-build'));

// --------------------------------
// Watch Tasks
// --------------------------------

gulp.task('watch', (done) => {
	// General Watcher
	gulp.watch(globs.src.other, gulp.series('dist'));

	// Sass Watcher
	gulp.watch(globs.src.scss, gulp.series('css', 'zip'));

	// JavaScript Watcher
	gulp.watch(globs.src.js, gulp.series('js', 'zip'));

	// SVG Watcher
	// Not running the 'zip' task because this task triggers
	// the 'css' task, which runs the 'zip' task afterwards.
	gulp.watch(globs.src.svg, gulp.series('svg'));

	if (enableACFsync) {
		// ACF Watcher
		gulp.watch(globs.build.acfJson, gulp.series('acf-sync'));
	}

	// Translation Strings Watcher
	gulp.watch(globs.src.phpFiles, gulp.series('i18n', 'zip'));

	return done();
});

// --------------------------------
// Build Task
// --------------------------------

gulp.task('build', gulp.series(
	'clean',
	'copy',
	'svg',
	'css',
	'js',
	'i18n',
	'acf',
	'zip',
));

// --------------------------------
// Default Task
// --------------------------------

gulp.task('default', gulp.series(
	'build',
	'serve',
	'watch',
));
