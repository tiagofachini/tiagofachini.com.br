<?php

// variables
$post_id            = ($post_id) ? $post_id : get_the_id();
$rmp_options        = get_option('rmp_options');
$rmp_custom_strings = $this->custom_strings($post_id);
$rating_icon_type   = self::icon_type();
$avg_rating         = Rate_My_Post_Common::get_average_rating($post_id);
$vote_count         = Rate_My_Post_Common::get_vote_count($post_id);
$icon_classes       = self::icons_classes($post_id, true);
$results_text       = $this->rating_widget_results_text($rmp_options, $avg_rating, $vote_count, $post_id);
$max_rating         = Rate_My_Post_Common::max_rating();
$custom_class       = $this->custom_class($post_id);
?>

<!-- FeedbackWP Plugin -->
<div
        class="rmp-widgets-container rmp-wp-plugin rmp-main-container js-rmp-widgets-container <?php echo esc_attr('js-rmp-widgets-container--' . $post_id); ?> <?php echo esc_attr($custom_class); ?>"
        data-post-id="<?php echo esc_attr($post_id); ?>"
>
    <?php do_action('rmp_before_all_widgets'); ?>
    <!-- Rating widget -->
    <div class="rmp-rating-widget js-rmp-rating-widget" role="group" aria-labelledby="rmp-title-<?php echo esc_attr($post_id); ?>">

        <?php if (str_replace(' ', '', $rmp_custom_strings['rateTitle'])): ?>
            <p class="rmp-heading rmp-heading--title" id="rmp-title-<?php echo esc_attr($post_id); ?>">
                <?php echo esc_html($rmp_custom_strings['rateTitle']); ?>
            </p>
        <?php endif; ?>

        <?php if (str_replace(' ', '', $rmp_custom_strings['rateSubtitle'])): ?>
            <p class="rmp-heading rmp-heading--subtitle">
                <?php echo esc_html($rmp_custom_strings['rateSubtitle']); ?>
            </p>
        <?php endif; ?>

        <div class="rmp-rating-widget__icons">
            <ul class="rmp-rating-widget__icons-list js-rmp-rating-icons-list" role="radiogroup" aria-label="<?php echo esc_attr(sprintf(__('Rating from 1 to %d', 'rate-my-post'), $max_rating)); ?>">
                <?php for ($icons_count = 0; $icons_count < $max_rating; $icons_count++): ?>
                    <?php
                    $star_value = $icons_count + 1;
                    $star_label = $rmp_custom_strings['star' . $star_value];
                    ?>
                    <li
                            class="rmp-rating-widget__icons-list__icon js-rmp-rating-item"
                            data-descriptive-rating="<?php echo esc_attr($star_label); ?>"
                            data-value="<?php echo esc_attr($star_value); ?>"
                            role="radio"
                            aria-checked="false"
                            aria-label="<?php echo esc_attr(sprintf(__('%1$d out of %2$d: %3$s', 'rate-my-post'), $star_value, $max_rating, $star_label)); ?>"
                            tabindex="<?php echo ($star_value === 1) ? '0' : '-1'; ?>"
                    >
                        <i class="js-rmp-rating-icon <?php echo esc_attr($rating_icon_type); ?> <?php echo esc_attr($icon_classes[$icons_count]); ?>" aria-hidden="true"></i>
                    </li>
                <?php endfor; ?>
            </ul>
        </div>

        <p class="rmp-rating-widget__hover-text js-rmp-hover-text" aria-live="polite"></p>

        <?php
        $rmp_security         = get_option('rmp_security');
        $turnstile_enabled    = isset($rmp_security['turnstile']) && absint($rmp_security['turnstile']) === 2;
        $turnstile_site_key   = isset($rmp_security['turnstileSiteKey']) ? str_replace(' ', '', $rmp_security['turnstileSiteKey']) : '';
        $turnstile_secret_key = isset($rmp_security['turnstileSecretKey']) ? str_replace(' ', '', $rmp_security['turnstileSecretKey']) : '';
        if ($turnstile_enabled && $turnstile_site_key && $turnstile_secret_key) {
            ?>
            <div id="cf-turnstile-rating-<?php echo esc_attr($post_id); ?>" class="cf-turnstile-rating"></div>
        <?php } ?>

        <button class="rmp-rating-widget__submit-btn rmp-btn js-submit-rating-btn">
            <?php echo esc_html($rmp_custom_strings['submitButtonText']); ?>
        </button>

        <div aria-live="polite" aria-atomic="true">
            <p class="rmp-rating-widget__results js-rmp-results <?php echo ! $avg_rating ? 'rmp-rating-widget__results--hidden' : ''; ?>">
                <?php echo $results_text; ?>
            </p>

            <p class="rmp-rating-widget__not-rated js-rmp-not-rated <?php echo $avg_rating ? 'rmp-rating-widget__not-rated--hidden' : ''; ?>">
                <?php echo $rmp_options['notShowRating'] == 1 ? esc_html($rmp_custom_strings['noRating']) : ''; ?>
            </p>

            <p class="rmp-rating-widget__msg js-rmp-msg" role="status"></p>
        </div>

    </div>

    <!--Structured data -->
    <?php echo $this->structured_data($post_id, $vote_count); ?>

    <?php if ($rmp_options['social'] === 2): ?>
        <!-- Social widget -->
        <?php echo $this->social_widget($post_id); ?>
    <?php endif; ?>

    <?php if ($rmp_options['feedback'] === 2): ?>
        <!-- Feedback widget -->
        <?php echo $this->feedback_widget($post_id); ?>
    <?php endif; ?>
    <?php do_action('rmp_after_all_widgets'); ?>
</div>