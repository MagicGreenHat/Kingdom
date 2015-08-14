'use strict';

var gulp = require('gulp'),
    prefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify'),
    sourcemaps = require('gulp-sourcemaps'),
    rigger = require('gulp-rigger'),
    cssmin = require('gulp-minify-css'),
    rename = require('gulp-rename');

var path = {
    build: {
        js: 'web/js',
        css: 'web/css'
    },
    src: {
        js: 'web/js/main.js',
        css: 'web/css/main.css'
    }
};

gulp.task('js:build', function () {
    gulp.src(path.src.js)
        .pipe(rigger())
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write())
        .pipe(rename('client.js'))
        .pipe(gulp.dest(path.build.js));
});

gulp.task('css:build', function () {
    gulp.src(path.src.css)
        .pipe(sourcemaps.init())
        .pipe(prefixer())
        .pipe(cssmin())
        .pipe(sourcemaps.write())
        .pipe(rename('style.css'))
        .pipe(gulp.dest(path.build.css));
});

gulp.task('build', [
    'js:build',
    'css:build'
]);
