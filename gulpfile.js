var gulp            = require('gulp'),
    sass            = require('gulp-sass'),
    autoprefixer    = require('gulp-autoprefixer'),
    cssnano         = require('gulp-cssnano'),
    rename          = require('gulp-rename'),
    uglify          = require('gulp-uglify'),
    concat          = require('gulp-concat'),
    cleancss        = require('gulp-clean-css'),
    flatten         = require('gulp-flatten'),
    plumber         = require('gulp-plumber'),
    svgstore        = require('gulp-svgstore'),
    svgmin          = require('gulp-svgmin'),
    path            = require('path');

const svg = function (theme) {
    return gulp.src(['src/'+theme+'/icons/*.svg'])
        .pipe(gulp.dest('web/wp-content/themes/'+theme+'/icons'))
        .pipe(svgmin(function (file) {
            var prefix = path.basename(file.relative, path.extname(file.relative));
            return {
                plugins: [{
                    cleanupIDs: {
                        prefix: prefix + '-',
                        minify: true
                    }
                }]
            }
        }))
        .pipe(rename({prefix: 'icon-'}))
        .pipe(svgstore({
            inlineSvg: true
        }))
        .pipe(gulp.dest('web/wp-content/themes/'+theme+'/styles/svg'));
}

const styles = function (theme) {
    return gulp.src(['src/'+theme+'/scss/*.scss'])
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer('last 2 version'))
        .pipe(rename({suffix: '.min'}))
        .pipe(cssnano({ zindex: false }))
        .pipe(gulp.dest('web/wp-content/themes/'+theme+'/styles'));
}

const scripts = function (theme) {
    return gulp.src(['src/'+theme+'/scripts/*.js'])
        .pipe(plumber())
        .pipe(concat('scripts.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('web/wp-content/themes/'+theme+'/js'));
}

const blockscripts = function (theme) {
    return gulp.src(['src/'+theme+'/blocks/scripts/*.js', 'src/'+theme+'/blocks/scripts/**/*.js'])
        .pipe(plumber())
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('web/wp-content/themes/'+theme+'/blocks/js'));
}

const assigntasks = function (theme) {
    gulp.task('svg-'+theme, function () {
        return svg(theme);
    });
    gulp.task('styles-'+theme, function () {
        return styles(theme);
    });
    gulp.task('scripts-'+theme, function () {
        return scripts(theme);
    });
    gulp.task('blockscripts-'+theme, function () {
        return blockscripts(theme);
    });
    gulp.task('watch-'+theme, function() {
        gulp.watch('src/'+theme+'/icons/*.svg', gulp.parallel('svg-'+theme));
        gulp.watch('src/'+theme+'/scss/**/*.scss', gulp.parallel('styles-'+theme));
        gulp.watch('src/'+theme+'/scripts/**/*.js', gulp.parallel('scripts-'+theme));
        gulp.watch('src/'+theme+'/blocks/scripts/**/*.js', gulp.parallel('blockscripts-'+theme));
    });
    tasks.push('watch-'+theme)
    tasks.push('styles-'+theme)
    tasks.push('scripts-'+theme)
    tasks.push('svg-'+theme)
    tasks.push('blockscripts-'+theme)
}

const themes = ['demo-rest-api']; // Liste des thèmes
const tasks = [];
themes.forEach(function(theme) {
    assigntasks(theme)
});

gulp.task('default', gulp.parallel(tasks));
