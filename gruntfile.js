module.exports = function(grunt) {

  // 1. All configuration goes here 
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
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
    }


  });

  // 3. Where we tell Grunt we plan to use this plug-in.
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
  grunt.registerTask('default', ['concat', 'uglify']);

};
