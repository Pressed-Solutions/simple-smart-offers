module.exports = function( grunt ) {

    'use strict';
    var banner = '/**\n * <%= pkg.homepage %>\n * Copyright (c) <%= grunt.template.today("yyyy") %>\n * This file is generated automatically. Do not edit.\n */\n';
    // Project configuration
    grunt.initConfig( {

        pkg:    grunt.file.readJSON( 'package.json' ),

        watch: {
        },

        addtextdomain: {
            options: {
                textdomain: 'simple-smart-offers',
            },
            target: {
                files: {
                    src: [ '*.php', '**/*.php', '!node_modules/**', '!php-tests/**', '!bin/**' ]
                }
            }
        },

        wp_readme_to_markdown: {
            your_target: {
                files: {
                    'README.md': 'readme.txt'
                }
            },
            options: {
                screenshot_url: 'http://ps.w.org/woo-customers-robly/assets/{screenshot}.png',
            }
        },

        makepot: {
            target: {
                options: {
                    domainPath: '/languages',
                    mainFile: 'simple-smart-offers.php',
                    potFilename: 'simple-smart-offers.pot',
                    potHeaders: {
                        poedit: true,
                        'x-poedit-keywordslist': true
                    },
                    type: 'wp-plugin',
                    updateTimestamp: true
                }
            }
        },
        browserSync: {
            dev: {
                bsFiles: {
                    src : ['*.css', '**/*.php', '**/*.js', '!node_modules'],
                },
                options: {
                    watchTask: true,
                    proxy: "http://wooshop.wordpress.dev",
                },
            },
        },
    } );

    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks( 'grunt-browser-sync' );
    grunt.registerTask( 'i18n', ['addtextdomain', 'makepot'] );
    grunt.registerTask( 'readme', ['wp_readme_to_markdown']);
    grunt.registerTask('default', [
        'browserSync',
        'watch',
    ]);

    grunt.util.linefeed = '\n';

};
