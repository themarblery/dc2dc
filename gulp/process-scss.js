const gulp = require('gulp');
const sass = require('gulp-sass');
const postcss = require('gulp-postcss');
const plumber = require('gulp-plumber');
const sasslint = require('gulp-sass-lint');
const bulkSass = require('gulp-sass-glob-import');
const autoprefixer = require('autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const Fiber = require('fibers');
const plumberErrorHandler = require('./plumber-helpers').plumberErrorHandler;

/**
 * Sass Compiler.
 */
sass.compiler = require('dart-sass');

/**
 * Wrapper function for the SCSS task that allows you to pass an instance
 * of a BrowserSync object. It should be the same instance created in your
 * gulpfile.js in order for CSS injection to work properly.
 *
 * @since  1.0.0
 *
 * @param  {Object}   browserSync Instance of BrowserSync created in gulpfile.js
 * @return {Function}             The function that processes SCSS.
 */
exports.init = (browserSync) => {
	/**
	 * Helper function to handle sass linting, vendor prefixes, sass compilation, and sourcemaps.
	 *
	 * @since 1.0.0
	 *
	 * @param  {Array|String} 	src  Pattern to match
	 * @param  {String} 		dest Folder to output to
	 */
	return (src, dest) => {
		// Post CSS Processors
		const processors = [
			autoprefixer(),
		];

		return gulp.src(src)
			.pipe(plumber(plumberErrorHandler))
			.pipe(sasslint({
				configFile: '.sass-lint.yml'
			}))
			.pipe(sasslint.format())
			.pipe(sasslint.failOnError())
			.pipe(sourcemaps.init())
			.pipe(bulkSass())
			.pipe(sass({
				outputStyle: 'compressed',
				fiber: Fiber,
			}).on('error', sass.logError))
			.pipe(postcss(processors))
			.pipe(sourcemaps.write('../maps'))
			.pipe(gulp.dest(dest))
			.pipe(browserSync.reload({
				stream: true
			}));
	}
}
