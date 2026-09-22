<?php

/**
 * DB Options dynamic options
 */
add_action( 'cs_dynamic_content_register', function() {
  cs_dynamic_content_register_dynamic_option('db_options', [
    'filter' => function($results, $args) {
      $options = wp_load_alloptions();

      $options = array_keys($options);
      $options = cs_array_as_choices($options);

      $options[] = [
        'value' => '__custom__',
        'label' => __('Custom Key', 'cornerstone'),
      ];

      return $options;
    },
  ]);
});
