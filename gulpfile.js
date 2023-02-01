const gulp = require('gulp');
const pug = require('gulp-pug');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const browserSync = require('browser-sync').create();
const del = require('del');
const autoprefixer = require('gulp-autoprefixer');
const minify = require('gulp-minify');
const cleanCSS = require('gulp-clean-css');
const imagemin = require('gulp-imagemin');
// Clean style when build
function clean() {
    return del(['./dist/assets/css/'], ['./dist/**/*.html']);
}

//compile scss into css
function style() {
    return gulp.src('scss/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(sourcemaps.write({ includeContent: false }))
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./dist/assets/css'))
        .pipe(browserSync.stream());
}

function css() {
    return gulp.src('styles/**/*.css')
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest('./dist/assets/css'))
        .pipe(browserSync.stream());
}

//compile jade into html
function html() {
    return gulp.src(['pug/**/*.pug', '!pug/_layout/*.pug', '!pug/_modules/*.pug', '!pug/_mixins/*.pug'])
        .pipe(pug({
            doctype: 'html',
            pretty: true
        }))
        .pipe(gulp.dest('./dist/'));
}

function watch() {
    browserSync.init({
        server: {
            baseDir: "./dist"
        },
        port: 4000,
        open:false
    });
    gulp.watch('scss/**/*.scss', { usePolling: true }, style);
    gulp.watch('pug/**/*.pug', { usePolling: true }, html);
    gulp.watch('pug/**/*.pug', { usePolling: true }).on('change', browserSync.reload);
    gulp.watch('styles/**/*.css', { usePolling: true }).on('change', browserSync.reload);
    gulp.watch('dist/**/*.html', { usePolling: true }).on('change', browserSync.reload);
    gulp.watch('assets/js/**/*.js', { usePolling: true }).on('change', browserSync.reload);
    gulp.watch('assets/fonts/', { usePolling: true }).on('change', browserSync.reload);
    gulp.watch('assets/images/*', { usePolling: true }).on('change', browserSync.reload);
}

function copyFont(){
    return gulp.src('assets/font/*')
    .pipe(gulp.dest('./dist/assets/fonts'))
}

function js(){
    return gulp.src('assets/js/*')
    .pipe(minify({
        ignoreFiles: ['.combo.js', '-min.js'],
        noSource:true
    }))
    .pipe(gulp.dest('./dist/assets/js'))
}


function images(){
    return gulp.src('assets/images/*')
    .pipe(imagemin([
        imagemin.gifsicle({interlaced: true}),
        imagemin.mozjpeg({quality: 75, progressive: true}),
        imagemin.optipng({optimizationLevel: 5}),
        imagemin.svgo({
            plugins: [
                {removeViewBox: true},
                {cleanupIDs: false}
            ]
        })
    ]))
    .pipe(gulp.dest('./dist/assets/images'))
}

// define complex tasks
const build = gulp.series(clean,js,copyFont,images, html,style,css);
// export tasks
exports.style = style;
exports.html = html;
exports.images = images;
exports.copyFont = copyFont;
exports.css = css;
exports.js = js;
exports.build = build;
exports.watch = watch;
exports.default = watch;