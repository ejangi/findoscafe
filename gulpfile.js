// ## Globals
/*global $:true*/
var $           = require('gulp-load-plugins')();
var argv        = require('yargs').argv;
var browserSync = require('browser-sync');
var reload      = browserSync.reload;
var gulp        = require('gulp');
var lazypipe    = require('lazypipe');
var merge       = require('merge-stream');
var htmlmin     = require('gulp-html-minifier');
var swig        = require('gulp-swig');
var data        = require('gulp-data');
var revReplace  = require('gulp-rev-replace');
var runSequence = require('run-sequence');
var ghPages     = require('gulp-gh-pages');

// See https://github.com/austinpray/asset-builder
var manifest = require('asset-builder')('./src/manifest.json');

// `path` - Paths to base asset directories. With trailing slashes.
// - `path.source` - Path to the source files. Default: `assets/`
// - `path.dist` - Path to the build directory. Default: `dist/`
var path = manifest.paths;

// `config` - Store arbitrary configuration values here.
var config = manifest.config || {};

// `globs` - These ultimately end up in their respective `gulp.src`.
// - `globs.js` - Array of asset-builder JS dependency objects. Example:
//   ```
//   {type: 'js', name: 'main.js', globs: []}
//   ```
// - `globs.css` - Array of asset-builder CSS dependency objects. Example:
//   ```
//   {type: 'css', name: 'main.css', globs: []}
//   ```
// - `globs.fonts` - Array of font path globs.
// - `globs.images` - Array of image path globs.
// - `globs.bower` - Array of all the main Bower files.
var globs = manifest.globs;

// `project` - paths to first-party assets.
// - `project.js` - Array of first-party JS assets.
// - `project.css` - Array of first-party CSS assets.
var project = manifest.getProjectGlobs();

// CLI options
var enabled = {
  // Enable static asset revisioning when `--production`
  // rev: argv.production,
  rev: false,
  // Disable source maps when `--production`
  maps: !argv.production,
  // Fail styles task on error when `--production`
  failStyleTask: argv.production
};

// Path to the compiled assets manifest in the dist directory
var revManifest = path.dist + 'assets.json';
// @@TODO — use https://github.com/jamesknelson/gulp-rev-replace when publishing production

var site = require('./site.json');

// ## Reusable Pipelines
// See https://github.com/OverZealous/lazypipe

// ### CSS processing pipeline
// Example
// ```
// gulp.src(cssFiles)
//   .pipe(cssTasks('main.css')
//   .pipe(gulp.dest(path.dist + 'styles'))
// ```
var cssTasks = function(filename) {
  return lazypipe()
    .pipe(function() {
      return $.if(!enabled.failStyleTask, $.plumber());
    })
    .pipe(function() {
      return $.if(enabled.maps, $.sourcemaps.init());
    })
      .pipe(function() {
        return $.if('*.less', $.less());
      })
      .pipe($.concat, filename)
      .pipe($.pleeease, {
        autoprefixer: {
          browsers: [
            'last 2 versions', 'ie 8', 'ie 9', 'android 2.3', 'android 4',
            'opera 12', 'firefox 20'
          ]
        }
      })
    .pipe(function() {
      return $.if(enabled.rev, $.rev());
    })
    .pipe(function() {
      return $.if(enabled.maps, $.sourcemaps.write('.'));
    })();
};

// ### JS processing pipeline
// Example
// ```
// gulp.src(jsFiles)
//   .pipe(jsTasks('main.js')
//   .pipe(gulp.dest(path.dist + 'scripts'))
// ```
var jsTasks = function(filename) {
  return lazypipe()
    .pipe(function() {
      return $.if(enabled.maps, $.sourcemaps.init());
    })
    .pipe($.concat, filename)
    .pipe($.uglify)
    .pipe(function() {
      return $.if(enabled.rev, $.rev());
    })
    .pipe(function() {
      return $.if(enabled.maps, $.sourcemaps.write('.'));
    })();
};

// ### Write to rev manifest
// If there are any revved files then write them to the rev manifest.
// See https://github.com/sindresorhus/gulp-rev
var writeToManifest = function(directory) {
  return lazypipe()
    .pipe(gulp.dest, path.dist + directory)
    .pipe(function() {
      return $.if('**/*.{js,css}', browserSync.reload({stream:true}));
    })
    .pipe($.rev.manifest, revManifest, {
      base: path.dist,
      merge: true
    })
    .pipe(gulp.dest, path.dist)();
};

// ## Gulp tasks
// Run `gulp -T` for a task summary

// ### Styles
// `gulp styles` - Compiles, combines, and optimizes Bower CSS and project CSS.
// By default this task will only log a warning if a precompiler error is
// raised. If the `--production` flag is set: this task will fail outright.
gulp.task('styles', function() {
  var merged = merge();
  manifest.forEachDependency('css', function(dep) {
    var cssTasksInstance = cssTasks(dep.name);
    if (!enabled.failStyleTask) {
      cssTasksInstance.on('error', function(err) {
        console.error(err.message);
        this.emit('end');
      });
    }
    merged.add(gulp.src(dep.globs, {base: 'styles'})
      .pipe(cssTasksInstance));
  });
  return merged
    .pipe(writeToManifest('styles'));
});

// ### Scripts
// `gulp scripts` - Compiles, combines, and optimizes Bower JS
// and project JS.
gulp.task('scripts', function() {
  var merged = merge();
  manifest.forEachDependency('js', function(dep) {
    merged.add(
      gulp.src(dep.globs, {base: 'scripts'})
        .pipe(jsTasks(dep.name))
    );
  });
  return merged
    .pipe(writeToManifest('scripts'));
});

// ### Images
// `gulp images` - Run lossless compression on all the images.
gulp.task('images', function() {
  return gulp.src(globs.images)
    .pipe($.imagemin({
      progressive: true,
      interlaced: true,
      svgoPlugins: [{removeUnknownsAndDefaults: false}]
    }))
    .pipe(gulp.dest(path.dist + 'images'));
});


gulp.task('html', function() {
  gulp.src(manifest.dependencies.html.files)
    .pipe(data(site))
    .pipe(swig())
    .pipe(htmlmin({collapseWhitespace: true}))
    .pipe(gulp.dest(path.dist));
});

gulp.task('cname', function() {
  gulp.src(path.source + "CNAME")
    .pipe(gulp.dest(path.dist));
});

gulp.task("revreplace", function(){
  var mani = gulp.src(revManifest);
  return gulp.src(path.dist + "/index.html")
    .pipe(revReplace({manifest: mani}))
    .pipe(gulp.dest(path.dist));
});

// ### Clean
// `gulp clean` - Deletes the build folder entirely.
gulp.task('clean', require('del').bind(null, [path.dist]));

// ### Watch
// `gulp watch` - Use BrowserSync to proxy your dev server and synchronize code
// changes across devices. Specify the hostname of your dev server at
// `manifest.config.devUrl`. When a modification is made to an asset, run the
// build step for that asset and inject the changes into the page.
// See: http://www.browsersync.io
gulp.task('watch', function() {
  browserSync({
    proxy: config.devUrl,
    startPath: config.devPath
  });
  gulp.watch([path.source + 'styles/**/*'], ['styles']);
  gulp.watch([path.source + 'scripts/**/*'], ['scripts']);
  gulp.watch([path.source + 'images/**/*'], ['images']);
  gulp.watch([path.source + '**/*.html', 'site.json'], ['html']);
});

// ### Build
// `gulp build` - Run all the build tasks but don't clean up beforehand.
// Generally you should be running `gulp` instead of `gulp build`.
// gulp.task('build', ['styles', 'scripts', 'fonts', 'images', 'html']);
gulp.task('build', function(callback) {
  runSequence(
      ['scripts', 'styles', 'images', 'cname'],
      'html',
      'revreplace',
      callback);
});

// Deploy to github pages:
gulp.task('deploy', function() {
  return gulp.src('./dist/**/*')
    .pipe(ghPages());
});


// ### Gulp
// `gulp` - Run a complete build. To compile for production run `gulp --production`.
gulp.task('default', ['clean'], function() {
  gulp.start('build');
});
