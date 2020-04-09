// /* eslint-env es6 */
// /* eslint no-undef: "off" */
'use strict';


/**
 * TODO: Minify JS, Add browsersync for live reload in dev,
 * remove unused packages
 */

const gulp          = require('gulp');
const sass          = require('gulp-sass');
const rename        = require('gulp-rename');
const path          = require('path');
const postcss       = require('gulp-postcss');
const autoprefixer  = require('autoprefixer');
const cssnano       = require('cssnano');
const fs            = require('fs');
// const plumber       = require('gulp-plumber');
const del           = require('del');
const babel         = require('gulp-babel');
const processors = [
    //postcss plugins go here
    autoprefixer({grid: true}),
    cssnano
];


/**
 * SCSS
 */
const adminSCSS = () => {
    return gulp.src("./admin/css/sass/*.scss")
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss(processors))
        .pipe(rename(function(path){
            path.basename += '.min'
        }))
        .pipe(gulp.dest("./admin/css"))
}

const publicSCSS = () => {
    return gulp.src("./public/css/sass/*.scss")
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss(processors))
        .pipe(rename(function(path){
            path.basename += '.min'
        }))
        .pipe(gulp.dest("./public/css"))
}

/**
 * JS
 */
const adminJS = () => {
    return gulp.src("./admin/js/src/*.js")
    .pipe( babel() )
    .pipe(rename(function(path){
        path.basename += '.es5'
    }))
    .pipe( gulp.dest("./admin/js/") )
}

const watchSCSS = () => {
    gulp.watch(['admin/css/sass/*.scss', 'public/css/sass/*.scss'], gulp.series(exports.adminSCSS, exports.publicSCSS));
}

const watchJS = () => {
    gulp.watch(['admin/js/src/*.js'], gulp.series(exports.adminJS));
}

const watchAll = () => {
    // Watch the lot of 'em
    gulp.watch(['admin/css/sass/*.scss', 'public/css/sass/*.scss', 'admin/js/src/*.js'], gulp.series(adminSCSS, publicSCSS, adminJS));
}

const defaultTasks = gulp.series(adminSCSS, publicSCSS, adminJS, watchAll);


exports.adminSCSS = adminSCSS;
exports.publicSCSS = publicSCSS;
exports.adminJS = adminJS;
exports.watchSCSS = watchSCSS;
exports.watchJS = watchJS;
exports.watchAll = watchAll;

exports.default = defaultTasks;