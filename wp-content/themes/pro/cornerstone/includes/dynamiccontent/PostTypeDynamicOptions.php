<?php

/**
 * Dynamic options
 */
add_action( 'cs_dynamic_content_register', function() {
  cs_dynamic_content_register_dynamic_option('post_type', [
    'filter' => function($results, $args) {
      return cornerstone( 'Locator' )->get_post_type_options();
    },
  ]);
});
