<?php

/**
 * Dynamic options
 */
add_action( 'cs_dynamic_content_register', function() {
  cs_dynamic_content_register_dynamic_option('post_status', [
    'filter' => function($results, $args) {
      $statuses = get_post_stati([], 'objects');
      return array_values(array_map(function($status) {
        return [
          'value' => $status->name,
          'label' => $status->label,
        ];
      }, $statuses));
    },
  ]);
});
