<?php

/**
 * Author preview grab with URL as the value
 * for choices in a select
 */
add_action( 'cs_dynamic_content_register', function() {
  cs_dynamic_content_register_dynamic_option('terms', [
    'filter' => function($results, $args) {
      $termsArgs = [];

      // Dynamic argument to filter out by taxonomies
      if (!empty($args['taxonomies'])) {
        $termsArgs['taxonomies'] = $args['taxonomies'];
      }

      $terms = cornerstone('Locator')->find_terms($termsArgs);

      $out = [
        [
          'value' => '',
          'label' => __('Select a Term', 'cornerstone'),
        ],
      ];

      foreach ($terms as $term) {
        // Setup select value
        $out[] = [
          'value' => $term->term_id,
          'label' => $term->name,
        ];
      }

      return $out;
    },
  ]);
});
