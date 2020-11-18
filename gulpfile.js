var gulp = require('gulp');
var gulpMinify = require('gulp-minify');
var wpPot = require('gulp-wp-pot');

function minify () {

  return gulp.src(['plugin/scripts/*.js'])
    .pipe(gulpMinify({
      ignoreFiles: ['*-min.js']
    }))
    .pipe(gulp.dest('plugin/scripts'));

}

function pot () {
  return gulp.src('plugin/*.php')
    .pipe(wpPot({
      domain: 'count-the-words',
      package: 'Count the Words'
    }))
    .pipe(gulp.dest('plugin/languages/count-the-words.pot'));
}

exports.minify = minify;
exports.pot = pot;
exports.default = gulp.parallel(pot, minify);
