module.exports = function (grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		// Watches files for changes and runs tasks based on the changed files
		watch: {
			bower: {
				files: ['bower.json']
			},
			composer: {
				files: ['composer.json'],
				tasks: ['composer:dev:update']
			},
			compass: {
				files: ['./sass/{,*/}*.{scss,sass}'],
				tasks: ['compass:dev']
			},
			gruntfile: {
				files: ['Gruntfile.js']
			}
		},
		compass: {
			options: {
				sassDir: 'sass/',
				cssDir: 'views/default/css/',
				imagesDir: 'graphics/',
				force: true
			},
			dist: {
				outputStyle: 'compact',
				environment: 'production',
			},
			dev: {
				outputStyle: 'expanded',
				environment: 'development',
				debugInfo: true,
			}
		},
		bower: {
			install: {
				dev: {
					bowerOptions: {
						production: false
					}
				},
				dist: {
					bowerOptions: {
						production: true
					}
				}
			}
		},
		composer: {
			dev: {
				options: {}
			},
			dist: {
				options: {
					flags: ['no-dev']
				}
			}
		},
		// Compress the release folder into an upload-ready zip file
		compress: {
			release: {
				options: {
					archive: '../../releases/<%= pkg.name %>/<%= pkg.name %>-<%= pkg.version %>.zip'
				},
				src: ['**',
					'!node_modules/**',
					'!Gruntfile.js',
					'!package.json',
					'!composer.*',
					'!bower.*',
					'!.*',
					'!sass/**',
					'!tests/**',
					'!phpunit.xml',
					'!nbproject/**',
					'!config.rb',
					'!release/**',
					'!releases/**'
				],
				dest: '<%= pkg.name %>/'
			}
		},
		phpunit: {
			test: {
				color: true,
				verbose: true,
				logJson: './tests/logs/log.json',
				coverageClover: './tests/coverage/clover.xml'
			}
		}
	});
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-version');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-composer');
	grunt.loadNpmTasks('grunt-bower-task');
	grunt.loadNpmTasks('grunt-phpunit');
	grunt.registerTask('createPluginManifest', function () {
		var pkg = grunt.file.readJSON('package.json')
		var js2xmlparser = require('js2xmlparser');
		var manifest = {
			"@": {
				"xmlns": "http://www.elgg.org/plugin_manifest/1.8",
			},
			'id': pkg.name || null,
			'name': pkg.name || null,
			'author': pkg.author.name + '(' + pkg.author.email + ')' || null,
			'version': pkg.version || null,
			'description': pkg.description || null,
			'license': pkg.license || nul,
			'copyright': pkg.copyright || null,
		};
		var config = pkg.config.plugin_manifest;
		for (var index in config) {
			if (config.hasOwnProperty(index)) {
				var attr = config[index];
				manifest[index] = attr;
			}
		}

		var xml = js2xmlparser('plugin_manifest', manifest);
		return grunt.file.write('manifest.xml', xml, {'encoding': 'utf8'});
	});
	grunt.registerTask('dev', [
		'createPluginManifest',
		'bower:install:dev',
		'composer:dev:install',
		'compass:dev'
	]);
	grunt.registerTask('dist', [
		'createPluginManifest',
		'bower:install:dist',
		'composer:dist:update',
		'compass:dist'
	]);
	grunt.registerTask('default', ['dev']);
	grunt.registerTask('release', [
		'dist',
		'compress:release'
	]);
	grunt.registerTask('test', ['dev', 'phpunit']);
};