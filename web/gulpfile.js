'use strict';

var gulp = require('gulp'),
//  watch = require('gulp-watch'),
    prefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify'),
// sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    rigger = require('gulp-rigger'),
    cssmin = require('gulp-minify-css'),
// imagemin = require('gulp-imagemin'),
// pngquant = require('imagemin-pngquant'),
// rimraf = require('rimraf'),
    rename = require("gulp-rename"); // для переименования файлов


var path = {
    build: { // укажем куда складывать готовые после сборки файлы
        //html: 'build/',
        js: 'js/websocket',
        css: 'css'
        //img: 'build/img/',
        //fonts: 'build/fonts/'
    },
    src: { //Пути откуда брать исходники
        //html: 'src/*.html', //Синтаксис src/*.html говорит gulp что мы хотим взять все файлы с расширением .html
        js: 'js/main.js',//В стилях и скриптах нам понадобятся только main файлы
        css: 'css/main.css'
        //img: 'src/img/**/*.*', //Синтаксис img/**/*.* означает - взять все файлы всех расширений из папки и из вложенных каталогов
        //fonts: 'src/fonts/**/*.*'
    },
    //watch: { //Тут мы укажем, за изменением каких файлов мы хотим наблюдать
    //    html: 'src/**/*.html',
    //    js: 'src/js/**/*.js',
    //    style: 'src/style/**/*.css',
    //    img: 'src/img/**/*.*',
    //    fonts: 'src/fonts/**/*.*'
    //},
    clean: './build'
};

/*gulp.task('html:build', function () {
    gulp.src(path.src.html) //Выберем файлы по нужному пути
        .pipe(rigger()) //Прогоним через rigger
        .pipe(gulp.dest(path.build.html)); //Выплюнем их в папку build
    // .pipe(reload({stream: true})); //И перезагрузим наш сервер для обновлений
});*/

gulp.task('js:build', function () {
    gulp.src(path.src.js) //Найдем наш main файл
        .pipe(rigger()) //Прогоним через rigger
        .pipe(sourcemaps.init()) //Инициализируем sourcemap
        .pipe(uglify()) //Сожмем наш js
        .pipe(sourcemaps.write()) //Пропишем карты
        .pipe(rename('client.js'))
        .pipe(gulp.dest(path.build.js)); //Выплюнем готовый файл в build
    //.pipe(reload({stream: true})); //И перезагрузим сервер
});

gulp.task('css:build', function () {
    gulp.src(path.src.css) //Выберем наш main.css
        .pipe(sourcemaps.init()) //То же самое что и с js
        //.pipe(sass()) //Скомпилируем
        .pipe(prefixer()) //Добавим вендорные префиксы
        .pipe(cssmin()) //Сожмем
        .pipe(sourcemaps.write())
        .pipe(rename('style.css'))
        .pipe(gulp.dest(path.build.css)); //И в build
    // .pipe(reload({stream: true}));
});

/*gulp.task('img:build', function() {
    gulp.src(path.src.img)
        .pipe(gulp.dest(path.build.img))
});

gulp.task('fonts:build', function() {
    gulp.src(path.src.fonts)
        .pipe(gulp.dest(path.build.fonts))
});*/

gulp.task('build', [
    //'html:build',
    'js:build',
    'css:build'
    //'fonts:build',
    //'img:build'
]);