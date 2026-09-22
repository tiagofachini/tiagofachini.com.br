<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class vscf_widget extends WP_Widget {
	// constructor
	public function __construct() {
		$widget_ops = array( 'classname' => 'vscf-widget', 'description' => __( 'Display your contact form in a widget.', 'very-simple-contact-form' ) );
		parent::__construct( 'vscf_widget', __( 'VS Contact Form', 'very-simple-contact-form' ), $widget_ops );
	}

	// set widget in dashboard
	function form( $instance ) {
		$instance = wp_parse_args( $instance, array(
			'vscf_title' => '',
			'vscf_text' => '',
			'vscf_attributes' => '',
		) );
		$vscf_title = ! empty( $instance['vscf_title'] ) ? $instance['vscf_title'] : __( 'VS Contact Form', 'very-simple-contact-form' );
		$vscf_text = $instance['vscf_text'];
		$vscf_attributes = $instance['vscf_attributes'];
		// widget input fields
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'vscf_title' ) ); ?>"><?php esc_html_e( 'Title', 'very-simple-contact-form' ); ?>:</label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vscf_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vscf_title' ) ); ?>" type="text" value="<?php echo esc_attr( $vscf_title ); ?>"></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'vscf_text' ) ); ?>"><?php esc_html_e( 'Text above form', 'very-simple-contact-form' ); ?>:</label>
		<textarea class="widefat monospace" rows="6" cols="20" id="<?php echo esc_attr( $this->get_field_id( 'vscf_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vscf_text' ) ); ?>"><?php echo wp_kses_post( $vscf_text ); ?></textarea></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'vscf_attributes' ) ); ?>"><?php esc_html_e( 'Attributes', 'very-simple-contact-form' ); ?>:</label>
		<textarea class="widefat monospace" rows="3" cols="20" id="<?php echo esc_attr( $this->get_field_id( 'vscf_attributes' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vscf_attributes' ) ); ?>" placeholder="<?php esc_attr_e( 'Example', 'very-simple-contact-form' ); ?>: email_to=&quot;info@example.com&quot;"><?php echo esc_textarea( $vscf_attributes ); ?></textarea></p>
		<p><?php esc_html_e( 'For info and available attributes', 'very-simple-contact-form' ); ?> <?php echo '<a href="https://wordpress.org/plugins/very-simple-contact-form" rel="noopener noreferrer" target="_blank">'.esc_html__( 'click here', 'very-simple-contact-form' ).'</a>'; ?>.</p>
		<?php
	}

	// update widget
	function update( $new_instance, $old_instance ) {
		$instance = array();
		// sanitize input
		$instance['vscf_title'] = sanitize_text_field( $new_instance['vscf_title'] );
		$instance['vscf_text'] = wp_kses_post( $new_instance['vscf_text'] );
		$instance['vscf_attributes'] = sanitize_text_field( $new_instance['vscf_attributes'] );
		return $instance;
	}

	// display widget with form in frontend
	function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $instance['vscf_title'] ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( apply_filters( 'widget_title', $instance['vscf_title'] ) ) . wp_kses_post( $args['after_title'] );
		}
		if ( ! empty( $instance['vscf_text'] ) ) {
			echo '<div class="vscf-widget-text">'.wp_kses_post( wpautop( $instance['vscf_text'] ) ).'</div>';
		}
		$content = '[contact-widget';
		if ( ! empty( $instance['vscf_attributes'] ) ) {
			$content .= ' '.wp_strip_all_tags( $instance['vscf_attributes'], true );
		}
		$content .= ']';
		echo do_shortcode( $content );
		echo wp_kses_post( $args['after_widget'] );
	}
}
