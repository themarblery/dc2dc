const gulp = require('gulp');
const uglify = require('gulp-uglify');
const browserify = require('browserify');
const babelify = require('babelify');
const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
const sourcemaps = require('gulp-sourcemaps');
const fs = require('fs');
const fsHelpers = require('./fs-helpers');

/**
 * Process a single JS entry point.
 * @param  {String} entry    Path to JS entry point.
 * @param  {String} filename Desired filename.
 * @param  {String} dest     Path to output the file.
 */
const processFile = (entry, filename, dest) => browserify({
	entries: entry,
	debug: true,
})
	.transform(babelify)
	.bundle()
	.on('error', function (err) {
		console.error(err);
		this.emit('end');
	})
	.pipe(source(filename))
	.pipe(buffer())
	.pipe(sourcemaps.init({ loadMaps: true }))
	.pipe(uglify())
	.pipe(sourcemaps.write('../maps'))
	.pipe(gulp.dest(dest));

/**
 * Process all files in the root of the provided path.
 * @param  {String} path Path to the directory you want to process.
 * @param  {String} dest Path to the output destination for the compiled files.
 */
const processDirectory = (path, dest) => {
	if (fs.existsSync(path)) {
		const entries = fsHelpers.getFiles(path);
		if (entries.length > 0) {
			entries.forEach((entry) => {
				if (entry.endsWith('.js')) {
					const isWin = process.platform === 'win32';
					let pathPieces = entry.split('/');

					if (isWin) {
						pathPieces = entry.split('\\');
					}

					const filename = pathPieces[pathPieces.length - 1];

					processFile(entry, filename, dest);
				}
			});
		}
	}
};

exports.processFile = processFile;
exports.processDirectory = processDirectory;
