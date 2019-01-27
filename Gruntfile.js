module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
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
        // concat: {
        //     options: {
        //         separator: ';'
        //     },
        //     dist: {
        //         src: ['src/**/*.js'],
        //         dest: 'dist/<%= pkg.name %>.js'
        //     }
        // },
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
        less: {
            options: {
                banner:
                    "/*! <%= pkg.name %> <%= grunt.template.today('yyyy-mm-dd') %>  */"
            },
            dist:  {
                src: ['public/css/*.less'],
                dest: 'public/css/style.css',
            }
        },
        cssmin: {
            options: {
                banner:
                    "/*! <%= pkg.name %> <%= grunt.template.today('yyyy-mm-dd') %>  */"
            },
            dist:  {
                src: ['public/css/style.css'],
                dest: 'public/css/style.min.css',
            }
            // target: {
            //     files: [{
            //         expand: true,
            //         cwd: 'release/css',
            //         src: ['*.css', '!*.min.css'],
            //         dest: 'release/css',
            //         ext: '.min.css'
            //     }]
            // }
        },
        // watch: {
        //     files: ['<%= jshint.files %>'],
        //     tasks: ['jshint', 'qunit', 'less']
        // }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
//    grunt.loadNpmTasks('grunt-contrib-concat');
//    grunt.loadNpmTasks('grunt-contrib-qunit');

//    grunt.registerTask('test', ['jshint', 'qunit']);

//    grunt.registerTask('default', ['jshint', 'concat', 'uglify']);
//    grunt.registerTask('default', ['jshint', 'uglify', 'less']);
    grunt.registerTask('css',     ['less', 'cssmin']);
    grunt.registerTask('js',      ['jshint', 'uglify']);
    grunt.registerTask('default', ['jshint', 'uglify', 'less', 'cssmin']);

};