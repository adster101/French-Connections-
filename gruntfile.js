module.exports = function(grunt) {



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
          }

        ]
      }
    },
    less: {
      development: {
        options: {
          compress: true //minifying the result
        },
        files: {
          //compiling frontend.less into frontend.css
          "media/fc/assets/css/styles.css": "templates/fcv4/assets/less/styles.less",
          "media/fc/assets/css/<%= opts.date %>.styles.min.css": "templates/fcv4/assets/less/styles.less"

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
          'bower_components/bootstrap/js/scrollspy.js',
          'bower_components/bootstrap/js/modal.js',
          'bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js',
          'media/system/js/core-uncompressed.js',
          'media/fc/js/libs/jquery.flexslider.js',
          'media/fc/js/general.js',
          'media/fc/js/search.js',
          'media/fc/js/property.js'
        ],
        dest: 'media/fc/assets/js/scripts.js'
      }
    },
    uglify: {
      build: {
        src: 'media/fc/assets/js/scripts.js',
        dest: 'media/fc/assets/js/<%= opts.date %>.scripts.min.js',
        options: {
          mangle: false
        }
      }
    },
    watch: {
      woot: {
        files: ['templates/fcv4/assets/less/*.less'], //watched files
        tasks: ['less'], //tasks to run
        options: {
          livereload: true                        //reloads the browser
        }
      },
      site: {
        files: ['templates/fcv4/*.php'],
        options: {
          livereload: true
        }
      },
      script: {
        files: ['media/fc/js/*.js'],
        tasks: ['concat', 'uglify'],
        options: {
          livereload: true
        }
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

            src: ['templates/fcv4/assets.tmp.php'],
            dest: 'templates/fcv4/assets.php'

          }
        ]
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

  // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
  // Task definition
  grunt.registerTask('init', ['less', 'concat', 'uglify', 'copy', 'replace']);
  grunt.registerTask('default', ['init']);
};
