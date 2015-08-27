'use strict';

var gulp = require('gulp'),
    prefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify'),
    sourcemaps = require('gulp-sourcemaps'),
    cssmin = require('gulp-minify-css'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename');

var path = {
    build: {
        js: 'web/js/',
        css: 'web/css/'
    },
    src: {
        jsLib: [
            'frontend/Library/**/*.js'
        ],
        js: [
            'frontend/namespace.js',        // Пространства имен
            'frontend/app.js',              // Скрипт инициализации приложения
            'frontend/Model/**/*.js',       // Модели
            'frontend/Controller/**/*.js'   // Контроллеры
        ],
        css: 'frontend/View/css/**/*.css'
    },
    destination: {
        jsLib: 'libraries.js',
        js: 'scripts.js',
        css: 'style.css'
    }
};

gulp.task('jsLib:build', function () {
    gulp.src(path.src.jsLib)
        .pipe(concat(path.destination.jsLib))
        .pipe(gulp.dest(path.build.js));
});

gulp.task('js:build', function () {
    gulp.src(path.src.js)
        .pipe(concat(path.destination.js))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(path.build.js));
});

gulp.task('css:build', function () {
    gulp.src(path.src.css)
        .pipe(concat(path.destination.css))
        .pipe(sourcemaps.init())
        .pipe(prefixer())
        .pipe(cssmin())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(path.build.css));
});

gulp.task('build', [
    'jsLib:build',
    'js:build',
    'css:build'
]);
