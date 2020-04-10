// /* eslint-env es6 */
// /* eslint no-undef: "off" */
"use strict";

/**
 * TODO: Minify JS, remove unused packages
 */

const gulp = require("gulp");
const sass = require("gulp-sass");
const rename = require("gulp-rename");
const path = require("path");
const postcss = require("gulp-postcss");
const autoprefixer = require("autoprefixer");
const cssnano = require("cssnano");
const fs = require("fs");
// const plumber       = require('gulp-plumber');
const del = require("del");
const babel = require("gulp-babel");
const browserSync = require("browser-sync").create();
const cache = require("gulp-cache");
const processors = [
  //postcss plugins go here
  autoprefixer({ grid: true }),
  cssnano,
];

/**
 * SCSS
 */
const adminSCSS = () => {
  return gulp
    .src("./admin/css/sass/*.scss")
    .pipe(sass().on("error", sass.logError))
    .pipe(postcss(processors))
    .pipe(
      rename(function (path) {
        path.basename += ".min";
      })
    )
    .pipe(gulp.dest("./admin/css"))
    .pipe(browserSync.stream());
};

const publicSCSS = () => {
  return gulp
    .src("./public/css/sass/*.scss")
    .pipe(sass().on("error", sass.logError))
    .pipe(postcss(processors))
    .pipe(
      rename(function (path) {
        path.basename += ".min";
      })
    )
    .pipe(gulp.dest("./public/css"))
    .pipe(browserSync.stream());
};

/**
 * JS
 */
const adminJS = () => {
  return gulp
    .src("./admin/js/src/*.js")
    .pipe(babel())
    .pipe(
      rename(function (path) {
        path.basename += ".es5";
      })
    )
    .pipe(gulp.dest("./admin/js/"))
    .pipe(browserSync.stream());
};

const publicJS = () => {
  return gulp
    .src("./public/js/src/*.js")
    .pipe(babel())
    .pipe(
      rename(function (path) {
        path.basename += ".es5";
      })
    )
    .pipe(gulp.dest("./public/js/"))
    .pipe(browserSync.stream());
};

// Browser sync
const serve = () => {
  browserSync.init({
    proxy: "http://localhost/jtron-plugin-dev",
    // notify: false,
  });

  gulp.watch(
    ["admin/css/sass/*.scss", "public/css/sass/*.scss"],
    gulp.series(adminSCSS, publicSCSS)
  );
  gulp
    .watch(
      ["admin/js/src/*.js", "public/js/src/*.js"],
      gulp.series(adminJS, publicJS)
    )
    .on("change", browserSync.reload);
  gulp.watch("./**/*.php").on("change", browserSync.reload);
};

const defaultTasks = gulp.series(serve);

exports.adminSCSS = adminSCSS;
exports.publicSCSS = publicSCSS;
exports.adminJS = adminJS;

exports.default = defaultTasks;
