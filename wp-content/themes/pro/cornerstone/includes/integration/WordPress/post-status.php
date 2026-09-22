<?php

/**
 * Displays "Cornerstone" next to posts that use cornerstone
 */
add_filter( 'display_post_states', function($post_states, $post) {

  if (cs_uses_cornerstone($post)) {
    $post_states[] = __('Cornerstone', 'cornerstone');
  }

  return $post_states;
}, 10, 2 );
