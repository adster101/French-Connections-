module.exports = function (grunt) {

  // 1. All configuration goes here 
  grunt.initConfig({
    // Add a 'global' opts object that we can access in each task...
    opts: {
      date: grunt.template.today('yyyymmddHMss')
    },
    pkg: grunt.file.readJSON('package.json'),
    copy: {
      main: {
        files: [
          // includes files within path
          {
            expand: true,
            src: ['bower_components/bootstrap/fonts/*', 'templates/fcv4/assets/fonts/*'],
            dest: 'media/fc/assets/fonts/',
            flatten: true
          },
          {
            expand: true,
            src: ['bower_components/bootstrap/fonts/*', 'templates/fcv4/assets/fonts/*'],
            dest: 'media/fc/fonts/',
            flatten: true
          }
        ]
      }
    },
    less: {
      development: {
        options: {
          compress: false //minifying the result
        },
        files: {
          //compiling frontend.less into frontend.css
          'media/fc/css/styles.css': 'templates/fcv4/assets/less/styles.less',
          'media/fc/css/critical.css':
                  ['media/fc/css/critical-homepage.css', 'media/fc/css/critical-search.css', 'media/fc/css/critical-property.css'],
          'administrator/templates/fcadmin/css/template.css': 'administrator/templates/fcadmin/less/template.less'
        }
      }
    },
    cssmin: {
      options: {
        shorthandCompacting: false,
        roundingPrecision: -1
      },
      target: {
        files: {
          // Minify the styles into the assets folder
          'media/fc/assets/css/styles.css':
                  ['media/fc/css/styles.css'],
          'media/fc/assets/css/<%= opts.date %>.styles.min.css':
                  ['media/fc/css/styles.css'],
          'media/fc/assets/css/critical.php':
                  ['media/fc/css/critical.css']
        }
      }
    },
    concat: {
      dist: {
        src: [
          'bower_components/jquery/dist/jquery.js',
          'bower_components/bootstrap/js/carousel.js',
          'bower_components/bootstrap/js/tab.js',
          'bower_components/bootstrap/js/transition.js',
          'bower_components/bootstrap/js/collapse.js',
          'bower_components/bootstrap/js/dropdown.js',
          'bower_components/bootstrap/js/tooltip.js',
          'media/fc/js/libs/bootstrap3-typeahead.js',
          'bower_components/bootstrap/js/popover.js',
          'bower_components/bootstrap/js/affix.js',
          'bower_components/bootstrap/js/modal.js',
          'bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js',
          'bower_components/js-marker-clusterer/src/markerclusterer_compiled.js',
          'bower_components/mustache.js/mustache.js',
          'bower_components/slick-carousel/slick/slick.min.js',
          'media/system/js/core-uncompressed.js',
          'bower_components/overthrow/dist/overthrow.sidescroller.min.js',
          'media/jui/js/cookies.jquery.min.js',
          'media/fc/js/general.js',
          'media/fc/js/search.js',
          'media/fc/js/property.js',
          'media/fc/js/atleisure.pay.js'
        ],
        dest: 'media/fc/js/scripts.js'
      },
      admin: {
        src: [
          'media/system/js/core.js',
          'media/fc/js/general.js',
          'media/fc/js/jquery-ui-1.8.23.custom.min.js',
          'media/fc/js/date-range.js',
          'administrator/components/com_rental/js/availability.js',
          'media/fc/js/locate.js',
          'media/fc/js/tariffs.js'
        ],
        dest: 'media/fc/js/admin.scripts.js'
      },
      images: {
        src: [
          'media/fc/js/libs/blueimp/vendor/jquery.ui.widget.js',
          'media/fc/js/libs/blueimp/tmpl.min.js',
          'media/fc/js/libs/blueimp/load-image.min.js',
          'media/fc/js/libs/blueimp/canvas-to-blob.min.js',
          'media/fc/js/libs/blueimp/jquery.iframe-transport.js',
          'media/fc/js/libs/blueimp/jquery.fileupload.js',
          'media/fc/js/libs/blueimp/jquery.fileupload-process.js',
          'media/fc/js/libs/blueimp/jquery.fileupload-image.js',
          'media/fc/js/libs/blueimp/jquery.fileupload-validate.js',
          'media/fc/js/libs/blueimp/jquery.fileupload-ui.js',
          'media/fc/js/main.js'
        ],
        dest: 'media/fc/js/images.admin.scripts.js'
      }
    },
    uglify: {
      build: {
        src: 'media/fc/js/scripts.js',
        dest: 'media/fc/assets/js/<%= opts.date %>.scripts.min.js',
        options: {
          mangle: true
        }
      },
      admin: {
        src: 'media/fc/js/admin.scripts.js',
        dest: 'media/fc/assets/js/<%= opts.date %>.admin.scripts.min.js',
        options: {
          mangle: false
        }
      },
      images: {
        src: 'media/fc/js/images.admin.scripts.js',
        dest: 'media/fc/assets/js/<%= opts.date %>.images.admin.scripts.min.js',
        options: {
          mangle: false
        }
      }
    },
    watch: {
      woot: {
        files: ['templates/fcv4/assets/less/*.less'], //watched files
        tasks: ['less'] //tasks to run
      },
      site: {
        files: ['templates/fcv4/*.php']
      },
      livereload: {
        files: ['media/fc/assets/css/*'],
        options: {livereload: false}
      }
    },
    replace: {
// Copies the 'assets' include file and replaces the timestamp string
// Just need to remember to upload the latest asset files...
      woot: {
        options: {
          patterns: [
            {
              match: 'timestamp',
              replacement: '<%= opts.date %>'
            }
          ]
        },
        files: [
          {
            src: ['templates/fcv4/inc/assets.tmp.php'],
            dest: 'templates/fcv4/inc/assets.php'
          },
          {
            src: ['administrator/templates/fcadmin/assets.tmp.php'],
            dest: 'administrator/templates/fcadmin/assets.php'
          },
          {
            src: ['templates/fcv4/inc/styles.tmp.php'],
            dest: 'templates/fcv4/inc/styles.php'
          }
        ]
      }
    },
    penthouse: {
      homepage: {
        outfile: 'media/fc/css/critical-homepage.css',
        css: 'media/fc/css/styles.css',
        url: 'http://dev.frenchconnections.co.uk',
        width: 350,
        height: 300
      },
      search: {
        outfile: 'media/fc/css/critical-search.css',
        css: 'media/fc/css/styles.css',
        url: 'http://dev.frenchconnections.co.uk/accommodation/france',
        width: 350,
        height: 300
      },
      property: {
        outfile: 'media/fc/css/critical-property.css',
        css: 'media/fc/css/styles.css',
        url: 'http://dev.frenchconnections.co.uk/listing/106693?unit_id=106694',
        width: 350,
        height: 300
      }
    }
  });
  // 3. Where we tell Grunt we plan to use this plug-in.
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-replace');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-penthouse');
  // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
  // Task definition
  grunt.registerTask('init', ['less', 'concat', 'uglify', 'replace', 'cssmin']);

  grunt.registerTask('default', ['init']);
};
