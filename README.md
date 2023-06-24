# WordPress Development Environment (https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/)
Contributors: Dariusz Zielonka
Tags: development, environment, debug, plugins

## Description

The WordPress Development Environment is a local development setup using the `.wp-env` configuration file. It allows you to run WordPress with specific debugging settings and pre-installed plugins for development purposes.

## Installation

To set up the WordPress Development Environment:

1. Make sure you have Docker installed on your local machine.
2. Open a terminal or command prompt and navigate to your WordPress project's root directory.
3. Run the command `wp-env start` to start the development environment.
4. Access your WordPress site by visiting `http://localhost:8888` in your web browser.

The development environment is now ready for use with the specified debugging settings and pre-installed plugins.

## Configuration

The `.wp-env` configuration file includes the following settings:

- `WP_DEBUG`: Enables or disables the WordPress debugging mode.
- `SCRIPT_DEBUG`: Enables or disables the debugging of JavaScript and CSS files.
- `WP_DEBUG_DISPLAY`: Controls whether WordPress error messages are displayed or not.
- `ALTERNATE_WP_CRON`: Enables or disables the alternate WordPress cron system (it is crucial for the plugin to work properly in localhost).

You can modify these settings based on your development needs by updating the corresponding values in the `.wp-env` file.

You can add or remove plugins from the list by modifying the `plugins` section in the `.wp-env` file.

## Plugin Mapping

The `.wp-env` file also includes a mapping configuration to link a specific plugin directory to a location within your WordPress project. This allows you to develop a plugin directly in your project directory without having to move or copy files.

The current mapping is as follows:

- Plugin Directory: `wp-content/plugins/plugin-block-api`
- Project Directory: `./block-api` (a pluggin scaffolded with https://www.npmjs.com/package/@wordpress/create-block)

Any changes made in the `./block-api` directory will be reflected in the `plugin-block-api` directory within the WordPress development environment.

You can update the mapping configuration by modifying the `mappings` section in the `.wp-env` file.
