<?php
/**
 * Plugin Name: Block API
 * Description: Example block scaffolded with Create Block tool.
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Version: 1.0.0
 * Author: Dariusz Zielonka
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: block-api
 *
 * @package create-block
 */
class Block_API_Plugin {
	/**
	 * Initializes the plugin.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'update_transient', array( $this, 'update_transient_cb' ) );
		register_activation_hook( __FILE__, array( $this, 'cron_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'cron_deactivation' ) );
		add_filter( 'cron_schedules', array( $this, 'block_api_cron_schedules' ) );

		if ( $this->is_gutenberg_supported() ) {
			// Check if Gutenberg is supported based on WordPress version and active theme.
			add_action( 'init', array( $this, 'register_block_type' ) );
		} else {
			require_once __DIR__ . '/includes/class-block-api-widget.php';
		}
	}

	/**
	 * Checks if Gutenberg is supported.
	 *
	 * @return bool Whether Gutenberg is supported or not.
	 */
	public function is_gutenberg_supported() {
		// Check WordPress version.
		global $wp_version;
		$required_version = '5.0';
		if ( version_compare( $wp_version, $required_version, '<' ) ) {
			return false;
		}

		// Check if the active theme supports Gutenberg.
		if ( function_exists( 'current_theme_supports' ) && ! current_theme_supports( 'core-block-patterns' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Registers the block type.
	 */
	public function register_block_type() {
		if ( $this->is_gutenberg_supported() ) {
			register_block_type(
				__DIR__ . '/build',
				array(
					'render_callback' => array( $this, 'block_api_render' ),
				)
			);
		}
	}

	/**
	 * Renders the block content.
	 *
	 * @param array $attributes Block attributes.
	 * @return string Block markup.
	 */
	public function block_api_render( $attributes ) {
		$title   = $attributes['title'];
		$content = get_transient( 'block_api_transient' );
		$markup  = '';

		if ( $title ) {
			$markup .= '<h2>' . esc_html( $title ) . '</h2>';
		}

		if ( $content ) {
			$markup .= '<pre>' . esc_html( $content ) . '</pre>';
		} else {
			$markup .= 'There was an error in API response.';
		}

		return $markup;
	}

	/**
	 * Registers REST API routes.
	 */
	public function register_rest_routes() {
		register_rest_route(
			'block-api-block/v1',
			'/transients/(?P<transientName>[a-zA-Z0-9-_]+)',
			array(
				'methods'             => 'GET, POST',
				'callback'            => array( $this, 'block_api_transient_handler' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Handles GET and POST requests for transients.
	 *
	 * @param WP_REST_Request $request REST API request object.
	 * @return array Response data.
	 */
	public function block_api_transient_handler( WP_REST_Request $request ) {
		$transient_name = $request->get_param( 'transientName' );

		if ( $request->get_method() === 'POST' ) {
			$data = $request->get_json_params();

			if ( $data && isset( $data['data'] ) && isset( $data['expiration'] ) ) {
				$transient_data = $data['data'];
				$expiration     = absint( $data['expiration'] );

				set_transient( $transient_name, $transient_data, $expiration );

				return array(
					'success' => true,
					'message' => 'Transient set successfully.',
				);
			}

			return array(
				'success' => false,
				'message' => 'Invalid transient data.',
			);
		}

		$transient_data = get_transient( $transient_name );

		if ( false === $transient_data ) {
			return array(
				'success' => false,
				'message' => 'Transient not found.',
			);
		}

		return array(
			'success' => true,
			'data'    => $transient_data,
		);
	}

	/**
	 * Adds custom cron schedules.
	 *
	 * @param array $schedules Existing cron schedules.
	 * @return array Modified cron schedules.
	 */
	public function block_api_cron_schedules( $schedules ) {
		if ( ! isset( $schedules['5min'] ) ) {
			$schedules['5min'] = array(
				'interval' => 5 * 60,
				'display'  => __( 'Once every 5 minutes' ),
			);
		}

		return $schedules;
	}

	/**
	 * Updates the transient callback.
	 */
	public function update_transient_cb() {
		$api_url  = 'https://httpbin.org/post';
		$api_data = array(
			'title'   => 'Hello from backend :)',
			'content' => 'Lorem ipsum dolor sit amet',
		);

		$response = wp_remote_post(
			$api_url,
			array(
				'body'    => wp_json_encode( $api_data ),
				'headers' => array(
					'Content-Type' => 'application/json',
				),
			)
		);
		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$api_response = wp_remote_retrieve_body( $response );
			set_transient( 'block_api_transient', $api_response, 3600 );
		}
	}

	/**
	 * Handles plugin activation.
	 */
	public function cron_activation() {
		$this->update_transient_cb();

		if ( ! wp_next_scheduled( 'update_transient' ) ) {
			wp_schedule_event( time(), '5min', 'update_transient' );
		}
	}

	/**
	 * Handles plugin deactivation.
	 */
	public function cron_deactivation() {
		wp_clear_scheduled_hook( 'update_transient' );
		delete_transient( 'block_api_transient' );
	}
}

$block_api = new Block_API_Plugin();
$block_api->init();
