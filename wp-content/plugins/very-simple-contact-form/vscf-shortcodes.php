<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// shortcode for page
function vscf_shortcode( $vscf_atts ) {
	// include variables
	include 'vscf-variables.php';

	// set form nonce
	$form_nonce_field = wp_nonce_field( 'vscf_nonce_action', 'vscf_nonce', true, false );

	// set name and id of submit button
	$submit_name = 'vscf_send_'.$rand_suffix.'';
	$submit_id = 'vscf_send';

	// set form class
	if ( empty( $vscf_atts['class'] ) ) {
		$custom_class = '';
	} else {
		$custom_class = ' '.$vscf_atts['class'];
	}
	$form_class = 'vscf-shortcode'.$custom_class.'';

	// processing form
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && ( $_SERVER['REQUEST_METHOD'] == 'POST' ) && isset( $_POST['vscf_send_'.$rand_suffix.''] ) ) {
		// validate form nonce
		if ( ! isset( $_POST['vscf_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['vscf_nonce'] ), 'vscf_nonce_action' ) ) {
			$error_class['form_nonce'] = true;
			$error = true;
		}

		// include input sanitization and validation
		include 'vscf-validate.php';

		// include sending and saving form submission
		include 'vscf-submission.php';
	}

	// include form
	include 'vscf-form.php';

	// after form validation
	if ( $sent == true ) {
		vscf_delete_session();
		return '<script>window.location="'.vscf_redirect_success().'"</script>';
	} elseif ( $fail == true ) {
		vscf_delete_session();
		return '<script>window.location="'.vscf_redirect_error().'"</script>';
	}

	// display form or the result of submission
	if ( isset( $_GET['vscf-sh'] ) ) {
		if ( sanitize_key( $_GET['vscf-sh'] ) == 'success' ) {
			return '<div class="vscf-info">' . $anchor_begin . wp_kses_post( wpautop( $thank_you_message ) ) . $anchor_end . '</div>';
		} elseif ( sanitize_key( $_GET['vscf-sh'] ) == 'fail' ) {
			return '<div class="vscf-info">' . $anchor_begin . esc_html__( 'Error: could not send form', 'very-simple-contact-form' ) . $anchor_end . '</div>';
		}
	} else {
		if ( $error == true ) {
			return '<div id="vscf" class="'.esc_attr( $form_class ).'">' . $anchor_begin . $email_form . $anchor_end . '</div>';
		} else {
			return '<div id="vscf" class="'.esc_attr( $form_class ).'">' . $email_form . '</div>';
		}
	}
}
add_shortcode( 'contact', 'vscf_shortcode' );

// shortcode for widget
function vscf_widget_shortcode( $vscf_atts ) {
	// include variables
	include 'vscf-variables.php';

	// set form nonce
	$form_nonce_field = wp_nonce_field( 'vscf_nonce_action', 'vscf_widget_nonce', true, false );

	// set name and id of submit button
	$submit_name = 'vscf_widget_send_'.$rand_suffix.'';
	$submit_id = 'vscf_widget_send';

	// set form class
	if ( empty( $vscf_atts['class'] ) ) {
		$custom_class = '';
	} else {
		$custom_class = ' '.$vscf_atts['class'];
	}
	$form_class = 'vscf-widget'.$custom_class.'';

	// processing form
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && ( $_SERVER['REQUEST_METHOD'] == 'POST' ) && isset( $_POST['vscf_widget_send_'.$rand_suffix.''] ) ) {
		// validate form nonce
		if ( ! isset( $_POST['vscf_widget_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['vscf_widget_nonce'] ), 'vscf_nonce_action' ) ) {
			$error_class['form_nonce'] = true;
			$error = true;
		}

		// include input sanitization and validation
		include 'vscf-validate.php';

		// include sending and saving form submission
		include 'vscf-submission.php';
	}

	// include form
	include 'vscf-form.php';

	// after form validation
	if ( $sent == true ) {
		vscf_delete_session();
		return '<script>window.location="'.vscf_widget_redirect_success().'"</script>';
	} elseif ( $fail == true ) {
		vscf_delete_session();
		return '<script>window.location="'.vscf_widget_redirect_error().'"</script>';
	}

	// display form or the result of submission
	if ( isset( $_GET['vscf-wi'] ) ) {
		if ( sanitize_key( $_GET['vscf-wi'] ) == 'success' ) {
			return '<div class="vscf-info">' . $anchor_begin . wp_kses_post( wpautop( $thank_you_message ) ) . $anchor_end . '</div>';
		} elseif ( sanitize_key( $_GET['vscf-wi'] ) == 'fail' ) {
			return '<div class="vscf-info">' . $anchor_begin . esc_html__( 'Error: could not send form', 'very-simple-contact-form' ) . $anchor_end . '</div>';
		}
	} else {
		if ( $error == true ) {
			return '<div id="vscf" class="'.esc_attr( $form_class ).'">' . $anchor_begin . $email_form . $anchor_end . '</div>';
		} else {
			return '<div id="vscf" class="'.esc_attr( $form_class ).'">' . $email_form . '</div>';
		}
	}
}
add_shortcode( 'contact-widget', 'vscf_widget_shortcode' );
