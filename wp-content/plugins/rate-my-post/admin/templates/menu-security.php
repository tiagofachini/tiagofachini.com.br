<?php

/**
 * Admin template
 *
 * @link       http://wordpress.org/plugins/rate-my-post/
 * @since      2.4.0
 *
 * @package    Rate_My_Post
 * @subpackage Rate_My_Post/admin/partials
 */
?>

<?php
  if ( ! defined( 'WPINC' ) ) {
  	die;
  }
?>

<?php $rmp_security = get_option( 'rmp_security' ); ?>

<div class="rmp-tab-content js-rmp-tab-content js-rmp-tab-content--3">

  <h2 class="rmp-tab-content__title">
    <?php echo ( esc_html__( 'Privileges', 'rate-my-post' ) ); ?>
  </h2>

  <label class="rmp-tab-content__label">
    <?php echo ( esc_html__( 'Ratings manipulation requires role of', 'rate-my-post' ) ); ?>
  </label>
  <select class="rmp-tab-content__select js-rmp-security" data-key="privileges">
    <option value="1" <?php echo ($rmp_security['privileges'] === 1) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'Author', 'rate-my-post' ) ); ?>
    </option>
    <option value="2" <?php echo ($rmp_security['privileges'] === 2) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'Editor', 'rate-my-post' ) ); ?>
    </option>
    <option value="3" <?php echo ($rmp_security['privileges'] === 3) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'Admin', 'rate-my-post' ) ); ?>
    </option>
  </select>
  <p class="rmp-tab-content__notice">
    <?php echo ( esc_html__( 'Select which role is required to manipulate ratings in the backend. Authors can only manipulate ratings of their own posts.', 'rate-my-post' ) ); ?>.
  </p>

  <label class="rmp-tab-content__label">
    <?php echo ( esc_html__( 'Who can rate posts', 'rate-my-post' ) ); ?>
  </label>
  <select class="rmp-tab-content__select js-rmp-security" data-key="votingPriv">
    <option value="1" <?php echo ($rmp_security['votingPriv'] === 1) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'Everybody', 'rate-my-post' ) ); ?>
    </option>
    <option value="2" <?php echo ($rmp_security['votingPriv'] === 2) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'Logged in users', 'rate-my-post' ) ); ?>
    </option>
  </select>
  <p class="rmp-tab-content__notice">
    <?php echo ( esc_html__( 'Select who can rate your posts. By default every visitor can rate posts.', 'rate-my-post' ) ); ?>.
  </p>

  <hr class="rmp-tab-content__divider" />

  <h2 class="rmp-tab-content__title">
    <?php echo ( esc_html__( 'Tracking', 'rate-my-post' ) ); ?>
  </h2>

  <label class="rmp-tab-content__label">
    <?php echo ( esc_html__( 'Track IP addresses', 'rate-my-post' ) ); ?>
  </label>
  <select class="rmp-tab-content__select js-rmp-security js-rmp-track-ip" data-key="ipTracking">
    <option value="1" <?php echo ($rmp_security['ipTracking'] === 1) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'No', 'rate-my-post' ) ); ?>
    </option>
    <option value="2" <?php echo ($rmp_security['ipTracking'] === 2) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'Yes', 'rate-my-post' ) ); ?>
    </option>
  </select>
  <p class="rmp-tab-content__notice">
    <?php echo ( esc_html__( 'Track IP addresses of users who rate your posts or leave you feedback! This feature is NOT COMPLIANT WITH GDPR!', 'rate-my-post' ) ); ?>
  </p>

  <label class="rmp-tab-content__label">
    <?php echo ( esc_html__( 'Prevent double votes via IP', 'rate-my-post' ) ); ?>
  </label>
  <select class="rmp-tab-content__select js-rmp-security js-rmp-ip-double-vote" data-key="ipDoubleVote">
    <option value="1" <?php echo ($rmp_security['ipDoubleVote'] === 1) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'No', 'rate-my-post' ) ); ?>
    </option>
    <option value="2" <?php echo ($rmp_security['ipDoubleVote'] === 2) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'Yes', 'rate-my-post' ) ); ?>
    </option>
  </select>
  <p class="rmp-tab-content__notice">
    <?php echo ( esc_html__( 'Prevent double votes by checking via IP if user already rated that post. This feature will only work if "Track IP addresses" is also enabled and is by default disabled for admins', 'rate-my-post' ) ); ?>.
  </p>

  <label class="rmp-tab-content__label">
    <?php echo ( esc_html__( 'Track users', 'rate-my-post' ) ); ?>
  </label>
  <select class="rmp-tab-content__select js-rmp-security" data-key="userTracking">
    <option value="1" <?php echo ($rmp_security['userTracking'] === 1) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'No', 'rate-my-post' ) ); ?>
    </option>
    <option value="2" <?php echo ($rmp_security['userTracking'] === 2) ? 'selected="selected"':""; ?>>
      <?php echo ( esc_html__( 'Yes', 'rate-my-post' ) ); ?>
    </option>
  </select>
  <p class="rmp-tab-content__notice">
    <?php echo ( esc_html__( 'This feature enables you to see who rated your posts and left you feedback!', 'rate-my-post' ) ); ?>
  </p>

  <hr class="rmp-tab-content__divider" />

  <h2 class="rmp-tab-content__title">
    <?php echo ( esc_html__( 'Spam Protection - Google reCAPTCHA v3', 'rate-my-post' ) ); ?>
  </h2>
  <p class="rmp-tab-content__tip-text">
    <?php echo sprintf ( ( esc_html__( 'The plugin supports Google reCAPTCHA v3 which verifies if an interaction is legitimate without any user interaction. You will need reCAPTCHA v3 site key and secret key to use this option. The service is free of charge. You can create keys on the %sGoogle reCAPTCHA website%s - choose the reCAPTCHA v3 type. ReCaptcha library adds a badge to your website with terms of use and privacy policy. You can hide it with custom css %s. However, note that you have to include a link to the terms of use and privacy policy according to the reCAPTCHA v3 Terms of Use.', 'rate-my-post' ) ), '<a href="https://www.google.com/recaptcha/admin/create" target="_blank">', '</a>', '.grecaptcha-badge {display: none;}' ); ?>
  </p>

  <label class="rmp-tab-content__label" for="rmp-site-key">
    <?php echo ( esc_html__( 'reCAPTCHA v3 Site Key', 'rate-my-post' ) ); ?>:
  </label>
  <input
    type="text"
    class="rmp-tab-content__input js-rmp-security"
    id="rmp-site-key"
    data-key="siteKey"
    value="<?php echo esc_html( $rmp_security['siteKey'] ); ?>"
  >
  <p class="rmp-tab-content__notice">
    <?php echo ( esc_html__( 'Insert Google reCAPTCHA v3 site key', 'rate-my-post' ) ); ?>.
  </p>

  <label class="rmp-tab-content__label" for="rmp-secret-key">
    <?php echo ( esc_html__( 'reCAPTCHA v3 Secret Key', 'rate-my-post' ) ); ?>:
  </label>
  <input
    type="password"
    class="rmp-tab-content__input js-rmp-security"
    id="rmp-secret-key"
    data-key="secretKey"
    value="<?php echo esc_html( $rmp_security['secretKey'] ); ?>"
  >
  <p class="rmp-tab-content__notice">
    *<?php echo ( esc_html__( 'Insert Google reCAPTCHA v3 secret key', 'rate-my-post' ) ); ?>.
  </p>

  <table class="rmp-tab-content__table">
    <tr>
      <td>
        <input
          id="rmp-recaptcha"
          class="rmp-tab-content__input-checkbox js-rmp-security"
          data-key="recaptcha"
          type="checkbox"
          <?php echo ($rmp_security['recaptcha'] === 2) ? 'checked':""; ?>
        >
        <label class="rmp-tab-content__label" for="rmp-recaptcha">
          <?php echo ( esc_html__( 'Enable reCAPTCHA v3', 'rate-my-post' ) ); ?>
        </label>
        <p class="rmp-tab-content__notice">
          <?php echo ( esc_html__( 'Automatically verifies if interactions are legitimate. Unlike the reCAPTCHA v2, it does not require any interaction from the visitor', 'rate-my-post' ) ); ?>.
        </p>
      </td>
    </tr>
  </table>

  <hr class="rmp-tab-content__divider" />

  <h2 class="rmp-tab-content__title">
      <?php echo ( esc_html__( 'Spam Protection - Cloudflare Turnstile', 'rate-my-post' ) ); ?>
  </h2>
  <p class="rmp-tab-content__tip-text">
    <?php echo sprintf ( ( esc_html__( 'The plugin supports Cloudflare Turnstile which verifies if an interaction is legitimate. You will need Turnstile site key and secret key to use this option. The service is free of charge. You can create keys on the %sCloudflare Turnstile website%s.', 'rate-my-post' ) ), '<a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">', '</a>' ); ?>
  </p>

  <label class="rmp-tab-content__label" for="rmp-turnstile-site-key">
    <?php echo ( esc_html__( 'Turnstile Site Key', 'rate-my-post' ) ); ?>:
  </label>
  <input
    type="text"
    class="rmp-tab-content__input js-rmp-security"
    id="rmp-turnstile-site-key"
    data-key="turnstileSiteKey"
    value="<?php echo esc_html( $rmp_security['turnstileSiteKey'] ); ?>"
  >
  <p class="rmp-tab-content__notice">
    <?php echo ( esc_html__( 'Insert Cloudflare Turnstile site key', 'rate-my-post' ) ); ?>.
  </p>

  <label class="rmp-tab-content__label" for="rmp-turnstile-secret-key">
    <?php echo ( esc_html__( 'Turnstile Secret Key', 'rate-my-post' ) ); ?>:
  </label>
  <input
    type="password"
    class="rmp-tab-content__input js-rmp-security"
    id="rmp-turnstile-secret-key"
    data-key="turnstileSecretKey"
    value="<?php echo esc_html( $rmp_security['turnstileSecretKey'] ); ?>"
  >
  <p class="rmp-tab-content__notice">
    *<?php echo ( esc_html__( 'Insert Cloudflare Turnstile secret key', 'rate-my-post' ) ); ?>.
  </p>

  <label class="rmp-tab-content__label" for="rmp-turnstile-size">
    <?php echo ( esc_html__( 'Turnstile Size', 'rate-my-post' ) ); ?>:
  </label>
  <select
    class="rmp-tab-content__input js-rmp-security"
    id="rmp-turnstile-size"
    data-key="turnstileSize"
  >
    <option value="normal" <?php echo (isset($rmp_security['turnstileSize']) && $rmp_security['turnstileSize'] === 'normal') ? 'selected' : ''; ?>>
      <?php echo ( esc_html__( 'Normal', 'rate-my-post' ) ); ?>
    </option>
    <option value="compact" <?php echo (isset($rmp_security['turnstileSize']) && $rmp_security['turnstileSize'] === 'compact') ? 'selected' : ''; ?>>
      <?php echo ( esc_html__( 'Compact', 'rate-my-post' ) ); ?>
    </option>
  </select>
  <p class="rmp-tab-content__notice">
    <?php echo ( esc_html__( 'Select the size for the Turnstile widget', 'rate-my-post' ) ); ?>.
  </p>

  <label class="rmp-tab-content__label" for="rmp-turnstile-theme">
    <?php echo ( esc_html__( 'Turnstile Theme', 'rate-my-post' ) ); ?>:
  </label>
  <select
    class="rmp-tab-content__input js-rmp-security"
    id="rmp-turnstile-theme"
    data-key="turnstileTheme"
  >
  <option value="auto" <?php echo (isset($rmp_security['turnstileTheme']) && $rmp_security['turnstileTheme'] === 'auto') ? 'selected' : ''; ?>>
      <?php echo ( esc_html__( 'Auto', 'rate-my-post' ) ); ?>
    </option>
    <option value="light" <?php echo (isset($rmp_security['turnstileTheme']) && $rmp_security['turnstileTheme'] === 'light') ? 'selected' : ''; ?>>
      <?php echo ( esc_html__( 'Light', 'rate-my-post' ) ); ?>
    </option>
    <option value="dark" <?php echo (isset($rmp_security['turnstileTheme']) && $rmp_security['turnstileTheme'] === 'dark') ? 'selected' : ''; ?>>
      <?php echo ( esc_html__( 'Dark', 'rate-my-post' ) ); ?>
    </option>
  </select>
  <p class="rmp-tab-content__notice">
    <?php echo ( esc_html__( 'Select the theme for the Turnstile widget', 'rate-my-post' ) ); ?>.
  </p>

  <table class="rmp-tab-content__table">
    <tr>
      <td>
        <input
          id="rmp-turnstile"
          class="rmp-tab-content__input-checkbox js-rmp-security"
          data-key="turnstile"
          type="checkbox"
          <?php echo ($rmp_security['turnstile'] === 2) ? 'checked':""; ?>
        >
        <label class="rmp-tab-content__label" for="rmp-turnstile">
          <?php echo ( esc_html__( 'Enable Turnstile', 'rate-my-post' ) ); ?>
        </label>
      </td>
    </tr>
  </table>

  <hr class="rmp-tab-content__divider" />

  <button id="js-rmp-security-waypoint" type="button" class="rmp-btn js-rmp-save-security">
    <?php echo ( esc_html__( 'Save Security Options', 'rate-my-post' ) ); ?>
  </button>

  <p class="rmp-tab-content__action-msg js-rmp-security-msg"></p>

  <div class="rmp-tab-content__sticky-save js-rmp-security-sticky">
    <button type="button" class="rmp-btn js-rmp-save-security">
      <?php echo ( esc_html__( 'Save Security Options', 'rate-my-post' ) ); ?>
    </button>
  </div>

</div>
