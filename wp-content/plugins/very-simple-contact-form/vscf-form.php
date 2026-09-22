<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// contact form
$email_form = '<form name="vscf_'.$rand_suffix.'" id="vscf-form" method="post">
	<div class="form-group vscf-name-group">
		<label for="vscf_name" class="'.( ( $hide_labels == 'yes' ) ? "vscf-hide" : "vscf-label" ).'">'.esc_html( $name_label ).' </label>
		<input type="text" name="vscf_name_'.$rand_suffix.'" id="vscf_name" '.( ( isset( $error_class['form_name'] ) || isset( $error_class['form_name_banned_words'] ) ) ? ' class="form-control vscf-error"' : ' class="form-control"' ).' placeholder="'.esc_attr( $name_placeholder ).'" value="'.esc_attr( $form_data['form_name'] ).'" minlength="2" maxlength="'.esc_attr( $input_max_length ).'" aria-required="true" />
		'.( isset( $error_class['form_name'] ) ? '<span class="vscf-error">'.esc_html( $error_name_label ).'</span>' : '' ).( isset( $error_class['form_name_banned_words'] ) ? '<span class="vscf-error">'.esc_html( $error_banned_words_label ).'</span>' : '' ).'
	</div>
	<div class="form-group vscf-email-group">
		<label for="vscf_email" class="'.( ( $hide_labels == 'yes' ) ? "vscf-hide" : "vscf-label" ).'">'.esc_html( $email_label ).' </label>
		<input type="email" name="vscf_email_'.$rand_suffix.'" id="vscf_email" '.( ( isset( $error_class['form_email'] ) || isset( $error_class['form_email_banned_words'] ) ) ? ' class="form-control vscf-error"' : ' class="form-control"' ).' placeholder="'.esc_attr( $email_placeholder ).'" value="'.esc_attr( $form_data['form_email'] ).'" maxlength="'.esc_attr( $input_max_length ).'" aria-required="true" />
		'.( isset( $error_class['form_email'] ) ? '<span class="vscf-error">'.esc_html( $error_email_label ).'</span>' : '' ).( isset( $error_class['form_email_banned_words'] ) ? '<span class="vscf-error">'.esc_html( $error_banned_words_label ).'</span>' : '' ).'
	</div>
	'.( ( $disable_subject != 'yes' ) ? '
		<div class="form-group vscf-subject-group">
			<label for="vscf_subject" class="'.( ( $hide_labels == 'yes' ) ? "vscf-hide" : "vscf-label" ).'">'.esc_html( $subject_label ).' </label>
			<input type="text" name="vscf_subject_'.$rand_suffix.'" id="vscf_subject" '. ( ( isset( $error_class['form_subject'] ) || isset( $error_class['form_subject_banned_words'] ) ) ? ' class="form-control vscf-error"' : ' class="form-control"' ).' placeholder="'.esc_attr( $subject_placeholder ).'" value="'.esc_attr( $form_data['form_subject'] ).'" minlength="2" maxlength="'.esc_attr( $input_max_length ).'" aria-required="true" />
			'.( isset( $error_class['form_subject'] ) ? '<span class="vscf-error">'.esc_html( $error_subject_label ).'</span>' : '' ).( isset( $error_class['form_subject_banned_words'] ) ? '<span class="vscf-error">'.esc_html( $error_banned_words_label ).'</span>' : '' ).'
		</div>
	' : '' ).'
	'.( ( $disable_sum == 'yes' ) ? '
		<div class="form-group vscf-display-none">
			<input type="hidden" name="vscf_sum_'.$rand_suffix.'" id="vscf_sum" class="form-control" value="'.esc_attr( $hidden_sum ).'" />
		</div>
	' : '
		<div class="form-group vscf-sum-group">
			<label for="vscf_sum" class="'.( ( $hide_labels == 'yes' ) ? "vscf-hide" : "vscf-label" ).'">'.esc_html( $random_number_one ).' + '.esc_html( $random_number_two ).' =</label>
			<input type="text" name="vscf_sum_'.$rand_suffix.'" id="vscf_sum" '.( isset( $error_class['form_sum'] ) ? ' class="form-control vscf-error"' : ' class="form-control"' ).' placeholder="'.esc_attr( $sum_placeholder ).'" value="'.esc_attr( $form_data['form_sum'] ).'" pattern="[0-9]{1,2}" maxlength="'.esc_attr( $input_max_length ).'" aria-required="true" />
			'.( isset( $error_class['form_sum'] ) ? '<span class="vscf-error">'.esc_html( $error_sum_label ).'</span>' : '' ).'
		</div>
	' ).'
	<div class="form-group vscf-hide">
		<label for="vscf_first_random">'.esc_html__( 'Please ignore this field', 'very-simple-contact-form' ).'</label>
		<input type="text" name="vscf_first_random_'.$rand_suffix.'" id="vscf_first_random" class="form-control" tabindex="-1" value="'.esc_attr( $form_data['form_first_random'] ).'" maxlength="'.esc_attr( $input_max_length ).'" />
	</div>
	<div class="form-group vscf-hide">
		<label for="vscf_second_random">'.esc_html__( 'Please ignore this field', 'very-simple-contact-form' ).'</label>
		<input type="text" name="vscf_second_random_'.$rand_suffix.'" id="vscf_second_random" class="form-control" tabindex="-1" value="'.esc_attr( $form_data['form_second_random'] ).'" maxlength="'.esc_attr( $input_max_length ).'" />
	</div>
	<div class="form-group vscf-message-group">
		<label for="vscf_message" class="'.( ( $hide_labels == 'yes' ) ? "vscf-hide" : "vscf-label" ).'">'.esc_html( $message_label ).' </label>
		<textarea name="vscf_message_'.$rand_suffix.'" id="vscf_message" rows="10" '.( ( isset( $error_class['form_message'] ) || isset( $error_class['form_message_banned_words'] ) || isset( $error_class['form_message_has_links'] ) || isset( $error_class['form_message_has_email'] ) ) ? ' class="form-control vscf-error"' : ' class="form-control"' ).' placeholder="'.esc_attr( $message_placeholder ).'" minlength="10" maxlength="'.esc_attr( $textarea_max_length ).'" aria-required="true">'.esc_textarea( $form_data['form_message'] ).'</textarea>
		'.( ( isset( $error_class['form_message'] ) || isset( $error_class['form_message_banned_words'] ) || isset( $error_class['form_message_has_links'] ) || isset( $error_class['form_message_has_email'] ) ) ? '
			<span class="vscf-error">'.( isset( $error_class['form_message'] ) ? esc_html( $error_message_label) : '' ).( isset( $error_class['form_message_banned_words'] ) ? esc_html( $error_banned_words_label) : '' ).( isset( $error_class['form_message_has_links'] ) ? esc_html( $error_message_has_links_label) : '' ).( isset( $error_class['form_message_has_email'] ) ? esc_html( $error_message_has_email_label) : '' ).'</span>
		' : '' ).'
	</div>
	<div class="form-group vscf-display-none">
		<input type="hidden" name="vscf_time_'.$rand_suffix.'" id="vscf_time" class="form-control" value="'.esc_attr( $time_field ).'" />
	</div>
	'.( ( $disable_privacy != 'yes' ) ? '
		<div class="form-group vscf-privacy-group">
			<input type="hidden" name="vscf_privacy_'.$rand_suffix.'" id="vscf_privacy_hidden" value="no" />
			<input type="checkbox" name="vscf_privacy_'.$rand_suffix.'" id="vscf_privacy" class="custom-control-input" value="yes" '.checked( esc_attr( $form_data['form_privacy'] ), "yes", false ).' aria-required="true" />
			<label for="vscf_privacy">'.wp_kses_post( $privacy_label ).'</label>
			'.( isset( $error_class['form_privacy'] ) ? '<span class="vscf-error">'.esc_html( $error_privacy_label ).'</span>' : '' ).'
		</div>
	' : '' ).'
	<div class="form-group vscf-display-none">
		'.$form_nonce_field.'
	</div>
	<div class="form-group vscf-submit-group">
		<button type="submit" name="'.esc_attr( $submit_name ).'" id="'.esc_attr( $submit_id ).'" class="btn btn-primary">'.esc_html( $submit_label ).'</button>
	</div>
	'.( ( $display_errors == 'yes' ) ?
	( ( isset( $error_class['form_first_random'] ) || isset( $error_class['form_second_random'] ) ) ? '<span class="vscf-error" >'.esc_html__( 'Error: hidden field', 'very-simple-contact-form' ).'</span>' : '' ).
	( isset( $error_class['form_time'] ) ? '<span class="vscf-error" >'.esc_html__( 'Error: time', 'very-simple-contact-form' ).'</span>' : '' ).
	( isset( $error_class['form_nonce'] ) ? '<span class="vscf-error" >'.esc_html__( 'Error: nonce', 'very-simple-contact-form' ).'</span>' : '' ).
	( isset( $error_class['form_sum_hidden'] ) ? '<span class="vscf-error" >'.esc_html__( 'Error: sum', 'very-simple-contact-form' ).'</span>' : '' ).
	( isset( $error_class['form_session'] ) ? '<span class="vscf-error" >'.esc_html__( 'Error: session', 'very-simple-contact-form' ).'</span>' : '' )
	: '' ).'
</form>';
