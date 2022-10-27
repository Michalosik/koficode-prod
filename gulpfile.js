const {
    src,
    dest,
    series,
    parallel,
    watch
} = require('gulp');
const sass = require('gulp-sass');
sass.compiler = require('node-sass');
const cssnano = require('gulp-cssnano');
const autoprefixer = require('gulp-autoprefixer');
const rename = require('gulp-rename');
const babel = require('gulp-babel');
const uglify = require('gulp-uglify');
const imagemin = require('gulp-imagemin');
const clean = require('gulp-clean');
const kit = require('gulp-kit');
const sourcemaps = require('gulp-sourcemaps');
const browserSync = require('browser-sync').create();
const reload = browserSync.reload;


const paths = {
    html: './html/**/*.kit',
    sass: './src/sass/**/*.scss',
    sassDest: './public/assets/css',
    js: './src/js/**/*.js',
    jsDest: './public/assets/js',
    img: './src/img/*',
    imgDest: './public/assets/img',
    dist: './public'
}

function sassCompiler(cb) {
    src(paths.sass)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(cssnano())
        .pipe(rename({
            suffix: ".min"
        }))
        .pipe(sourcemaps.write())
        .pipe(dest(paths.sassDest));
    cb()
}

function javaScript(cb) {
    src(paths.js)
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: ['@babel/env']
        }))
        .pipe(uglify())
        .pipe(rename({
            suffix: ".min"
        }))
        .pipe(sourcemaps.write())
        .pipe(dest(paths.jsDest));
    cb()
}

function imageMin(cb) {
    src(paths.img)
        .pipe(imagemin())
        .pipe(dest(paths.imgDest));
    cb()
}

function cleanStuff(cb) {
    src(paths.dist, {
            read: false
        })
        .pipe(clean());
    cb()
}

function handleKits(cb) {
    src(paths.html)
        .pipe(kit())
        .pipe(dest('./'))
    cb()
}

function startBrowserSync(cb) {
    browserSync.init({
        proxy: "localhost",
        port: 80
        // server: {
        //     baseDir: "./"
        // 
    })
    cb()
}

function watchForCahnges(cb) {
    watch('./*.html').on("change", reload);
    watch([paths.sass, paths.js, paths.html], parallel(handleKits, sassCompiler, javaScript)).on("change", reload);
    watch(paths.img, imageMin).on("change", reload);
    cb()
}
const mainFunctions = parallel(handleKits, sassCompiler, javaScript, imageMin)
exports.cleanStuff = cleanStuff
exports.default = series(mainFunctions, startBrowserSync, watchForCahnges)