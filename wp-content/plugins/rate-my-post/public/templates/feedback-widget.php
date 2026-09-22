<?php

/**
 * Feedback widget
 *
 * @link       http://wordpress.org/plugins/rate-my-post/
 * @since      3.0.0
 *
 * @package    Rate_My_Post
 * @subpackage Rate_My_Post/public/partials
 */
?>

<?php
  //retrive necessary data for the template
  $rmp_options = get_option( 'rmp_options' );
  $rmp_custom_strings = $this->custom_strings( $post_id );
?>

<div class="rmp-feedback-widget js-rmp-feedback-widget">

  <p class="rmp-heading rmp-heading--title">
    <?php echo $rmp_custom_strings['feedbackTitle']; ?>
  </p>

  <p class="rmp-heading rmp-heading--subtitle">
    <?php echo $rmp_custom_strings['feedbackSubtitle']; ?>
  </p>

  <?php do_action( 'rmp_before_feedback_form' ); ?>

  <div class="rmp-feedback-widget__container">

    <p class="rmp-feedback-widget__text">
      <?php echo $rmp_custom_strings['feedbackText']; ?>
    </p>

    <textarea class="rmp-feedback-widget__input js-rmp-feedback-input" rows="5" id="feedback-text"></textarea>

    <?php
      $rmp_security = get_option( 'rmp_security' );
      $turnstile_enabled = isset($rmp_security['turnstile']) && absint($rmp_security['turnstile']) === 2;
      $turnstile_site_key = isset($rmp_security['turnstileSiteKey']) ? str_replace(' ', '', $rmp_security['turnstileSiteKey']) : '';
      $turnstile_secret_key = isset($rmp_security['turnstileSecretKey']) ? str_replace(' ', '', $rmp_security['turnstileSecretKey']) : '';
      if ($turnstile_enabled && $turnstile_site_key && $turnstile_secret_key) {
    ?>
      <div id="cf-turnstile-feedback-<?php echo esc_attr( $post_id ); ?>" class="cf-turnstile-feedback"></div>
    <?php } ?>

    <button type="button" class="rmp-feedback-widget__btn rmp-btn rmp-btn--large js-rmp-feedback-button">
      <?php echo $rmp_custom_strings['feedbackButton']; ?>
    </button>

    <div class="rmp-feedback-widget__loader js-rmp-feedback-loader">
      <div></div><div></div><div></div>
    </div>

    <p class="rmp-feedback-widget__msg js-rmp-feedback-msg"></p>

  </div>

  <?php do_action( 'rmp_after_feedback_form' ); ?>

</div>
