// /* eslint-env es6 */
// /* eslint no-undef: "off" */
'use strict';

const gulp          = require('gulp');
const rename        = require('gulp-rename');
const path          = require('path');
const postcss       = require('gulp-postcss');
const autoprefixer  = require('autoprefixer');
const cssnano       = require('cssnano');
const nested        = require('postcss-nested');
const fs            = require('fs');
const plumber       = require('gulp-plumber');
const del           = require('del');
const babel         = require('gulp-babel');
const processors = [
    //postcss plugins will go here
    nested,
    autoprefixer({grid: true}),
    cssnano
]

exports.startUp     = startUp;
exports.adminCSS    = adminCSS;
exports.publicCSS   = publicCSS;
exports.watchCSS    = watchCSS;
exports.adminJS     = adminJS;
exports.watchJS     = watchJS;
exports.watchAll     = watchAll;


function startUp(cb) {
    const src = '/src';
    const css = '/css';
    const js = '/js';
    const base = ['./admin', './public'];

    base.forEach(dir => {
        let cssDir = dir + css;
        let jsDir = dir + js;
        if (!fs.existsSync(cssDir + src)) {
            fs.mkdirSync(cssDir + src)
            let files = `${cssDir}/*.css`;
            gulp.src(files)
                .pipe(gulp.dest(cssDir + src))
                .on('end', function(){
                    del(files);
                });
        } else {
            console.log(`The directory ${cssDir + src} already exists`);
        }
        if (!fs.existsSync(jsDir + src)) {
            fs.mkdirSync(jsDir + src)
            gulp.src( `${jsDir}/*.js`)
                .pipe(gulp.dest(jsDir + src));
        } else {
            console.log(`The directory ${jsDir + src} already exists`);
        }
    });
    cb();
}

// ////
// CSS
// ////
function adminCSS() {
    return gulp.src("./admin/css/src/*.css")
        .pipe(postcss(processors))
        .pipe(rename(function(path){
            path.basename += '.min'
        }))
        .pipe(gulp.dest("./admin/css"))
}

function publicCSS() {
    return gulp.src("./public/css/src/*.css")
        .pipe(postcss(processors))
        .pipe(rename(function(path){
            path.basename += '.min'
        }))
        .pipe(gulp.dest("./public/css"))
}


/**
 * JS
 */
function adminJS() {
    return gulp.src("./admin/js/src/*.js")
    .pipe( babel() )
    .pipe(rename(function(path){
        path.basename += '.es5'
    }))
    .pipe( gulp.dest("./admin/js/") )
}

function watchCSS() {
    gulp.watch(['admin/css/src/*.css', 'public/css/src/*.css'], gulp.series(exports.adminCSS, exports.publicCSS));
};

function watchJS() {
    gulp.watch(['admin/js/src/*.js'], gulp.series(exports.adminJS));
};

function watchAll() {
    // Watch the lot of 'em
    gulp.watch(['admin/css/src/*.css', 'public/css/src/*.css', 'admin/js/src/*.js'], gulp.series(exports.adminCSS, exports.publicCSS, exports.adminJS));

}