// Gulp
var gulp         = require('gulp'),
    gutil        = require('gulp-util'),
    clean        = require('gulp-clean'),
    size         = require('gulp-size'),
    rename       = require('gulp-rename'),
    notify       = require('gulp-notify'),
    watch        = require('gulp-watch'),
    connect      = require('gulp-connect'),
    livereload   = require('gulp-livereload'),
    lr           = require('tiny-lr'),
    server       = lr(),
    path         = require('path'),
    // Scripts [coffee, js]
    coffee       = require('gulp-coffee'),
    coffeelint   = require('gulp-coffeelint'),
    uglify       = require('gulp-uglify'),
    concat       = require('gulp-concat'),
    // Styles [sass, css]
    sass         = require('gulp-ruby-sass'),
    minifycss    = require('gulp-minify-css'),
    csso         = require('gulp-csso'),
    autoprefixer = require('gulp-autoprefixer'),
    // Images and static assets
    imagemin     = require('gulp-imagemin'),
    __ports      = {
        server:     1340,
        livereload: 35733
    };

// Styles
gulp.task('styles', function () {
    return gulp.src(['styles/{,*/}*.{scss,sass}', '!styles/vendor/{,*/}*.*', '!styles/{,*/}*_*.{scss,sass}', '!styles/{,*/}*.min*'])
        .pipe(sass({
            style: 'expanded',
            quiet: true,
            trace: true,
            loadPath: []
        }))
        .on('error', gutil.log)
        .pipe(autoprefixer('last 1 version'))
        .on('error', gutil.log)
        .pipe(size())
        .pipe(csso())
        .pipe(minifycss())
        .pipe(size())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('styles'))
        .pipe(livereload(server))
        .pipe(notify({
            message: 'Styles task completed @ <%= options.date %>',
            templateOptions: {
                date: new Date()
            }
        }));
});

// Scripts
gulp.task('scripts', function () {
    return gulp.src(['scripts/{,*/}*.{coffee,coffee.md}', '!scripts/vendor/{,*/}*.*', '!scripts/{,*/}*_*.{coffee,coffee.md}', '!scripts/{,*/}*.min*'])
        .pipe(coffee({
            bare: true
        }))
        .pipe(coffeelint())
        .pipe(coffeelint.reporter())
        .on('error', gutil.log)
        .pipe(size())
        .pipe(uglify())
        .pipe(size())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('scripts'))
        .pipe(livereload(server))
        .pipe(notify({
            message: 'Scripts task completed @ <%= options.date %>',
            templateOptions: {
                date: new Date()
            }
        }));
});

// Images
gulp.task('images', function () {
    return gulp.src('images/**/*.{jpg,gif,png}')
        .pipe(imagemin({
            optimizationLevel: 3,
            progressive: true,
            interlaced: true
        }))
        .on('error', gutil.log)
        .pipe(size())
        .pipe(gulp.dest('images'))
        .pipe(livereload(server))
        .pipe(notify({
            message: 'Images task completed @ <%= options.date %>',
            templateOptions: {
                date: new Date()
            }
        }));
});

// Connect & livereload
gulp.task('connect', connect.server({
    root: __dirname,
    port: __ports.server,
    livereload: true
}));

// Watch
gulp.task('watch', function () {
    server.listen(__ports.livereload, function (error) {
        if (error) {
            return console.error(error);
        }

        // Gulpfile
        gulp.watch('gulpfile.js', ['assets']);

        // Watch .scss files
        gulp.watch('styles/{,*/}*.{scss,sass}', ['styles']);

        // Watch .coffee files
        gulp.watch('scripts/{,*/}*.{coffee,coffee.md}', ['scripts']);
    });
});

gulp.task('assets', ['styles', 'scripts', 'images']);

gulp.task('serve', ['assets'], function () {
    gulp.start('connect', 'watch');
});

gulp.task('default', ['serve']);
