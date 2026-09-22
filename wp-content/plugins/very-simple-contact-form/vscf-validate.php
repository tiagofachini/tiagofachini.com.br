<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// set input value for subject and privacy field
if ( ( $disable_subject != 'yes' ) && isset( $_POST['vscf_subject_'.$rand_suffix.''] ) ) {
	$subject_value = sanitize_text_field( $_POST['vscf_subject_'.$rand_suffix.''] );
} else {
	$subject_value = '';
}
if ( ( $disable_privacy != 'yes' ) && isset( $_POST['vscf_privacy_'.$rand_suffix.''] ) ) {
	$privacy_value = sanitize_key( $_POST['vscf_privacy_'.$rand_suffix.''] );
} else {
	$privacy_value = '';
}

// sanitize input
$post_data = array(
	'form_name' => sanitize_text_field( $_POST['vscf_name_'.$rand_suffix.''] ),
	'form_email' => sanitize_email( $_POST['vscf_email_'.$rand_suffix.''] ),
	'form_subject' => $subject_value,
	'form_sum' => sanitize_text_field( $_POST['vscf_sum_'.$rand_suffix.''] ),
	'form_message' => sanitize_textarea_field( $_POST['vscf_message_'.$rand_suffix.''] ),
	'form_privacy' => $privacy_value,
	'form_first_random' => sanitize_text_field( $_POST['vscf_first_random_'.$rand_suffix.''] ),
	'form_second_random' => sanitize_text_field( $_POST['vscf_second_random_'.$rand_suffix.''] ),
	'form_time' => sanitize_text_field( $_POST['vscf_time_'.$rand_suffix.''] ),
);

// validate name field
$value_name = stripslashes( $post_data['form_name'] );
if ( ! empty( $banned_words_list ) ) {
	$words = explode( ',', $banned_words_list );
	foreach ( $words as $word ) {
		if ( ! empty( $word ) && mb_ereg( "\b".$word."\b", mb_strtolower( $value_name ) ) ) {
			$banned_words = true;
			$banned_words_name_field = true;
		}
	}
}
if ( isset( $banned_words_name_field ) && ( $ignore_submission_banned_words != 'yes' ) ) {
	$error_class['form_name_banned_words'] = true;
	$error = true;
} elseif ( mb_strlen( $value_name ) < 2 ) {
	$error_class['form_name'] = true;
	$error = true;
}
$form_data['form_name'] = $value_name;

// validate email field
$value_email = $post_data['form_email'];
if ( ! empty( $banned_words_list ) ) {
	$words = explode( ',', $banned_words_list );
	foreach ( $words as $word ) {
		if ( ! empty( $word ) && mb_ereg( "\b".$word."\b", mb_strtolower( $value_email ) ) ) {
			$banned_words = true;
			$banned_words_email_field = true;
		}
	}
}
if ( isset( $banned_words_email_field ) && ( $ignore_submission_banned_words != 'yes' ) ) {
	$error_class['form_email_banned_words'] = true;
	$error = true;
} elseif ( empty( $value_email ) ) {
	$error_class['form_email'] = true;
	$error = true;
}
$form_data['form_email'] = $value_email;

// validate subject field
if ( $disable_subject != 'yes' ) {
	$value_subject = stripslashes( $post_data['form_subject'] );
	if ( ! empty( $banned_words_list ) ) {
		$words = explode( ',', $banned_words_list );
		foreach ( $words as $word ) {
			if ( ! empty( $word ) && mb_ereg( "\b".$word."\b", mb_strtolower( $value_subject ) ) ) {
				$banned_words = true;
				$banned_words_subject_field = true;
			}
		}
	}
	if ( isset( $banned_words_subject_field ) && ( $ignore_submission_banned_words != 'yes' ) ) {
		$error_class['form_subject_banned_words'] = true;
		$error = true;
	} elseif ( mb_strlen( $value_subject ) < 2 ) {
		$error_class['form_subject'] = true;
		$error = true;
	}
	$form_data['form_subject'] = $value_subject;
}

// validate sum field
if ( empty( $_SESSION[$session_name[1]] ) || empty( $_SESSION[$session_name[2]] ) ) {
	$error_class['form_session'] = true;
	$error = true;
} else {
	if ( $disable_sum == 'yes' ) {
		$value_sum = $post_data['form_sum'];
		$value_session = wp_hash( $random_number_one + $random_number_two );
		if ( $value_sum != $value_session ) {
			$error_class['form_sum_hidden'] = true;
			$error = true;
		}
	} else {
		$value_sum = $post_data['form_sum'];
		$value_session = $random_number_one + $random_number_two;
		if ( $value_sum != $value_session ) {
			$error_class['form_sum'] = true;
			$error = true;
		}
		$form_data['form_sum'] = $value_sum;
	}
}

// validate message field
$value_message = stripslashes( $post_data['form_message'] );
$value_message_clean = preg_replace( '/\s+/u', ' ', $value_message );
$message_array = explode( ' ', $value_message_clean );
if ( ! empty( $banned_words_list ) ) {
	$words = explode( ',', $banned_words_list );
	foreach ( $words as $word ) {
		if ( ! empty( $word ) && mb_ereg( "\b".$word."\b", mb_strtolower( $value_message_clean ) ) ) {
			$banned_words = true;
			$banned_words_message_field = true;
		}
	}
}
if ( $allow_links == 'disallow' ) {
	 $allowed_links = 0;
} elseif ( $allow_links == 'one' ) {
	$allowed_links = 1;
} else {
	$allowed_links = 100;
}
$count_links = 0;
foreach ( $message_array as $message_array_value ) {
	$array_for_link_detection = preg_replace( '#[^0-9a-z]*$#i', '', $message_array_value );
	if ( preg_match( "/[A-Za-z0-9-]+\.[A-Za-z]/", $array_for_link_detection ) && ! is_email( $array_for_link_detection ) ) {
		$count_links++;
	}
}
if ( $count_links > $allowed_links ) {
	$message_has_links = true;
}
if ( $allow_email == 'disallow' ) {
	foreach ( $message_array as $message_array_value ) {
		$array_for_email_detection = preg_replace( '#[^0-9a-z]*$#i', '', $message_array_value );
		if ( is_email( $array_for_email_detection ) ) {
			$message_has_email = true;
		}
	}
}
if ( isset( $banned_words_message_field ) && ( $ignore_submission_banned_words != 'yes' ) ) {
	$error_class['form_message_banned_words'] = true;
	$error = true;
} elseif ( mb_strlen( $value_message ) < 10 ) {
	$error_class['form_message'] = true;
	$error = true;
} elseif ( isset( $message_has_links ) && ( $ignore_submission_links != 'yes' ) ) {
	$error_class['form_message_has_links'] = true;
	$error = true;
} elseif ( isset( $message_has_email ) && ( $ignore_submission_email != 'yes' ) ) {
	$error_class['form_message_has_email'] = true;
	$error = true;
}
$form_data['form_message'] = $value_message;

// validate first honeypot field
$value_first_random = $post_data['form_first_random'];
if ( mb_strlen( $value_first_random ) > 0 ) {
	$error_class['form_first_random'] = true;
	if ( $display_errors == 'yes' ) {
		$error = true;
	}
}
$form_data['form_first_random'] = $value_first_random;

// validate second honeypot field
$value_second_random = $post_data['form_second_random'];
if ( mb_strlen( $value_second_random ) > 0 ) {
	$error_class['form_second_random'] = true;
	if ( $display_errors == 'yes' ) {
		$error = true;
	}
}
$form_data['form_second_random'] = $value_second_random;

// validate time field
$value_time = is_numeric( $post_data['form_time'] ) ? $post_data['form_time'] : time();
$form_seconds = time() - $value_time;
if ( $form_seconds < $minimum_seconds ) {
	$error_class['form_time'] = true;
	if ( $display_errors == 'yes' ) {
		$error = true;
	}
}

// validate privacy field
if ( $disable_privacy != 'yes' ) {
	$value_privacy = $post_data['form_privacy'];
	if ( $value_privacy != 'yes' ) {
		$error_class['form_privacy'] = true;
		$error = true;
	}
	$form_data['form_privacy'] = $value_privacy;
}
