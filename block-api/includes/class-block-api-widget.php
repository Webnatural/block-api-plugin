<?php

/**
 * API Block Widget class.
 *
 * @package create-widget
 */
class API_Block_Widget extends WP_Widget {

	/**
	 * Constructs the widget.
	 */
	public function __construct() {
		parent::__construct(
			'block_api_widget',
			__( 'API Block Widget', 'api-block-widget' ),
			array(
				'description' => __( 'A widget to display API response as in API Block.', 'api-block-widget' ),
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$title   = apply_filters( 'widget_title', $instance['title'] );
		$content = get_transient( 'block_api_transient' );
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if ( $content ) {
			echo '<pre>' . esc_attr( $content ) . '</pre>';
		} else {
			echo esc_attr__( 'There was an error in API response.', 'block-api' );
		}

		echo $args['after_widget'];
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'block-api' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
				$content = ! empty( $instance['content'] ) ? $instance['content'] : '';
		?>
				<p>
					<input id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" type="hidden"
						value="<?php echo esc_attr( $content ); ?>" />
				</p>
				<?php
	}

	/**
	 * Handles updating the widget settings.
	 *
	 * @param array $new_instance New settings.
	 * @param array $old_instance Old settings.
	 * @return array Updated settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance            = array();
		$instance['title']   = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['content'] = ! empty( $new_instance['content'] ) ? sanitize_text_field( $new_instance['content'] ) : '';

		return $instance;
	}

	/**
	 * Register the API_Block_Widget widget.
	 */
	// public static function register_block_api_widget() {
	// register_widget( 'API_Block_Widget' );
	// }

}


function register_block_api_widget() {
	register_widget( 'API_Block_Widget' );
}
add_action( 'widgets_init', 'register_block_api_widget' );
