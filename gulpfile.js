'use strict';

const gulp = require('gulp');
const php = require('gulp-connect-php');
const browser = require('browser-sync');

gulp.task('server', () => {
    return php.server({
        hostname: '127.0.0.1',
        port: 8000,
        keepalive: true
    }, () => {
        browser({
            proxy: '127.0.0.1:8000',
            port: 3000,
            open: true,
            notify: false,
            reloadOnRestart: true
        });
    });
});

gulp.task('default', ['server'], () => {
    gulp.watch(['index.php', 'src/**/*']).on('change', () => {
        browser.reload();
    });
    gulp.watch('gulpfile.js').on('change', () =>
        process.exit(0)
    );
});
