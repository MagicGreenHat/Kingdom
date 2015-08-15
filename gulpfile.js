'use strict';

var gulp = require('gulp'),
    prefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify'),
    sourcemaps = require('gulp-sourcemaps'),
    rigger = require('gulp-rigger'),
    cssmin = require('gulp-minify-css'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename');

var path = {
    build: {
        js: 'web/js',
        css: 'web/css'
    },
    src: {
        js: 'web/js/src/**/*.js',
        css: 'web/css/src/**/*.css'
    },
    destination: {
        js: 'client.js',
        css: 'style.css'
    }
};

gulp.task('js:build', function () {
    gulp.src(path.src.js)
        .pipe(concat(path.build.js))
        .pipe(rigger())
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write())
        .pipe(rename(path.destination.js))
        .pipe(gulp.dest(path.build.js));
});

gulp.task('css:build', function () {
    gulp.src(path.src.css)
        .pipe(concat(path.build.css))
        .pipe(sourcemaps.init())
        .pipe(prefixer())
        .pipe(cssmin())
        .pipe(sourcemaps.write())
        .pipe(rename(path.destination.css))
        .pipe(gulp.dest(path.build.css));
});

gulp.task('build', [
    'js:build',
    'css:build'
]);
