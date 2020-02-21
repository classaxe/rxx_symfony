module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        banner:
            '/*\n' +
            ' * Project:    <%= pkg.description %>\n' +
            ' * Homepage:   <%= pkg.homepage %>\n' +
            ' * Version:    <%= githash.main.tag %>\n' +
            ' * Date:       <%= grunt.template.today("yyyy-mm-dd") %>\n' +
            ' * Licence:    <%= pkg.license %>\n' +
            ' * Copyright:  <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>\n' +
            ' */',

        concat: {
            options: {
                banner: '<%= banner %>\n',
                separator: ';\n\n'
            },
            dist: {
                src: ['src/js/*.js'],
                dest: 'public/js/functions.js'
            }
        },

        cssmin: {
            options: {
                banner: '<%= banner %>'
            },
            dist:  {
                src: ['public/css/style.css'],
                dest: 'public/css/style.min.css',
            }
        },

        githash: {
            main: {
                options: {},
            }
        },

        jshint: {
            files: ['Gruntfile.js', 'js/*.js'],
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
        less: {
            options: {
                banner: '<%= banner %>'
            },
            dist:  {
                src:  'src/css/style.less',
                dest: 'public/css/style.css'
            },
        },
        uglify: {
            options: {
                banner: '<%= banner %>',
            },
            dist: {
                src: ['public/js/functions.js'],
                dest: 'public/js/functions.min.js',
            }
        },
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-githash');

    grunt.registerTask('css',     ['githash', 'less', 'cssmin']);
    grunt.registerTask('js',      ['githash', 'jshint', 'concat', 'uglify']);
    grunt.registerTask('default', ['githash', 'jshint', 'concat', 'uglify', 'less', 'cssmin']);
};