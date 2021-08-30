/**
 * Grunt Tasks JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent
 * @author     wpeka <https://club.wpeka.com>
 */

module.exports = function (grunt) {

	'use strict';

	// Project configuration.
	grunt.initConfig(
		{

			pkg: grunt.file.readJSON( 'package.json' ),
			clean: {
				build: ['release/<%= pkg.version %>']
			},
			uglify: {
				options: {

				},
				admin: {
					files: [{
						expand: true,
						cwd: 'release/<%= pkg.version %>/admin/js/',
						src: [
							'*.js',
							'!*.min.js'
						],
						dest: 'release/<%= pkg.version %>/admin/js/',
						ext: '.min.js'
					}]
				},
				adminm: {
					files: [{
						expand: true,
						cwd: 'release/<%= pkg.version %>/admin/modules/cookie-custom/assets/js/',
						src: [
						'*.js',
						'!*.min.js'
						],
						dest: 'release/<%= pkg.version %>/admin/modules/cookie-custom/assets/js/',
						ext: '.min.js'
					}]
				},
				frontend: {
					files: [{
						expand: true,
						cwd: 'release/<%= pkg.version %>/public/js/',
						src: [
						'*.js',
						'!*.min.js',
						'!*.bundle.js'
						],
						dest: 'release/<%= pkg.version %>/public/js/',
						ext: '.min.js'
					}]
				},
			},
			cssmin: {
				options: {

				},
				admin: {
					files: [{
						expand: true,
						cwd: 'release/<%= pkg.version %>/admin/css/',
						src: [
							'*.css',
							'!*.min.css'
						],
						dest: 'release/<%= pkg.version %>/admin/css/',
						ext: '.min.css'
					}]
				},
				frontend: {
					files: [{
						expand: true,
						cwd: 'release/<%= pkg.version %>/public/css/',
						src: [
							'*.css',
							'!*.min.css'
						],
						dest: 'release/<%= pkg.version %>/public/css/',
						ext: '.min.css'
					}]
				},
			},
			copy: {
				build: {
					options: {
						mode: true,
						expand: true,
					},
					src: [
					'**',
					'!vendor/**',
					'vendor/composer/**',
					'vendor/altorouter/**',
					'vendor/asm89/**',
					'vendor/symfony/**',
					'vendor/timber/**',
					'vendor/twig/**',
					'vendor/upstatement/**',
					'vendor/symfony/**',
					'vendor/myclabs/**',
					'vendor/yoast/**',
					'vendor/autoload.php',
					'!node_modules/**',
					'!release/**',
					'!bin/**',
					'!tests/**',
					'!build/**',
					'!tests/**',
					'!.git/**',
					'!.github/**',
					'!bin/**',
					'!Gruntfile.js',
					'!README.md',
					'!package.json',
					'!package-lock.json',
					'!.gitignore',
					'!.gitmodules',
					'!*.yml',
					'!*.xml',
					'!*.config.*',
					'!composer.lock',
					'!composer.json',
					'!*.yml',
					'!*.xml',
					'!*.config.*'
					],
					dest: 'release/<%= pkg.version %>/'
				}
			},
			compress: {
				build: {
					options: {
						mode: 'zip',
						archive: './release/<%= pkg.name %>.<%= pkg.version %>.zip'
					},
					expand: true,
					cwd: 'release/<%= pkg.version %>/',
					src: ['**/*'],
					dest: '<%= pkg.name %>'
				}
			},

			addtextdomain: {
				options: {
					textdomain: 'gdpr-cookie-consent',
				},
				update_all_domains: {
					options: {
						updateDomains: true
					},
					src: ['*.php', '**/*.php', '!\.git/**/*', '!bin/**/*', '!node_modules/**/*', '!tests/**/*', '!vendor/**/*', '!analytics/**/*']
				}
			},

			wp_readme_to_markdown: {
				your_target: {
					files: {
						'README.md': 'readme.txt'
					}
				},
			},

			makepot: {
				target: {
					options: {
						domainPath: '/languages',
						exclude: ['\.git/*', 'bin/*', 'node_modules/*', 'tests/*', 'vendor/**/*', 'analytics/**/*'],
						mainFile: 'gdpr-cookie-consent.php',
						potFilename: 'gdpr-cookie-consent.pot',
						potHeaders: {
							poedit: true,
							'x-poedit-keywordslist': true
						},
						type: 'wp-plugin',
						updateTimestamp: true
					}
				}
			},
		}
	);

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.registerTask( 'default', ['i18n', 'readme'] );
	grunt.registerTask( 'i18n', ['addtextdomain', 'makepot'] );
	grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );
	grunt.registerTask( 'build', ['clean:build', 'copy:build', 'uglify:admin', 'uglify:adminm', 'uglify:frontend', 'cssmin:admin', 'cssmin:frontend', 'compress:build'] );

	grunt.util.linefeed = '\n';

};
