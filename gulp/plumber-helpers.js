const notify = require('gulp-notify');

exports.plumberErrorHandler = {
	errorHandler: notify.onError({
		title: 'Gulp - Error',
		message: 'Error: <%= error.message %>'
	})
};
