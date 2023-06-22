<?php
/**
 * Plugin Name:       Block Api
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Dariusz Zielonka
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       block-api
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_block_api_block_init() {
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'render_frontend',
		)
	);
}
	add_action( 'init', 'create_block_block_api_block_init' );



// Add custom REST API endpoint for managing transients
function block_api_register_rest_routes() {
	register_rest_route(
		'block-api-block/v1',
		'/transients/(?P<transientName>[a-zA-Z0-9-_]+)',
		array(
			'methods'             => 'GET, POST',
			'callback'            => 'block_api_transient_handler',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'block_api_register_rest_routes' );

// Handler for the custom REST API endpoint
function block_api_transient_handler( WP_REST_Request $request ) {
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

	// GET request, retrieve the transient data
	$transient_data = get_transient( $transient_name );

	if ( $transient_data === false ) {
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

class API_Block_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'block_api_widget',
			__( 'API Block Widget', 'api-block-widget' ),
			array(
				'description' => __( 'A widget to display API response as in API Block.', 'api-block-widget' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$api_response = get_transient( 'block_api_transient' );
		if ( $api_response ) {
			echo '<pre>' . esc_html( $api_response ) . '</pre>';
		} else {
			// Fetch API response if transient doesn't exist
			$api_url  = 'https://httpbin.org/post';
			$api_data = array(
				'title'   => 'Title 3',
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
				set_transient( 'block_api_transient', $api_response, MINUTE_IN_SECONDS );
				echo '<pre>' . esc_html( $api_response ) . '</pre>';
			} else {
				echo '<p>No API response available.</p>';
			}
		}

		echo $args['after_widget'];
	}

}

// Register the widget
function register_block_api_widget() {
	register_widget( 'API_Block_Widget' );
}
add_action( 'widgets_init', 'register_block_api_widget' );
