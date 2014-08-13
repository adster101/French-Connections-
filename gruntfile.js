module.exports = function(grunt) {

  // 1. All configuration goes here 
  grunt.initConfig({
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
          compress: false //minifying the result
        },
        files: {
          //compiling frontend.less into frontend.css
          "media/fc/assets/css/styles.css": "templates/fcv4/assets/less/styles.less"
        }
      }
    },
    concat: {
      dist: {
        src: [
        'media/fc/js/libs/blueimp/*.js', // All JS in the libs folder
        'media/fc/main.js'  // This specific file
        ],
        dest: 'media/fc/js/build/production.js'
      }
    },
    uglify: {
      build: {
        src: 'media/fc/js/build/production.js',
        dest: 'media/fc/js/build/production.min.js'
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
      }
    }
  });

  // 3. Where we tell Grunt we plan to use this plug-in.
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-copy');

  // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
  // Task definition
  grunt.registerTask('init', ['less', 'concat', 'uglify', 'copy']);
  grunt.registerTask('default', ['watch']);
};
