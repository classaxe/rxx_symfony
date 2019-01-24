module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            options: {
                separator: ';'
            },
            dist: {
                src: ['src/**/*.js'],
                dest: 'dist/<%= pkg.name %>.js'
            }
        },
        uglify: {
            options: {
                banner:
                    "/*! <%= pkg.name %> <%= grunt.template.today('yyyy-mm-dd') %>  */"
            },
            dist: {
                src: ['public/js/functions.js'],
                dest: 'public/js/functions.min.js',
                // files: {
                //     'public/js/<%= pkg.name %>.min.js': ['<%= concat.dist.dest %>']
                // }
            }
        },
        jshint: {
            files: ['Gruntfile.js', 'public/js/functions.js'],
            options: {
                // options here to override JSHint defaults
                globals: {
                    jQuery: true,
                    console: true,
                    module: true,
                    document: true
                }
            }
        },
        watch: {
            files: ['<%= jshint.files %>'],
            tasks: ['jshint', 'qunit']
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-qunit');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');

    grunt.registerTask('test', ['jshint', 'qunit']);

//    grunt.registerTask('default', ['jshint', 'concat', 'uglify']);
    grunt.registerTask('default', ['jshint', 'uglify']);

};