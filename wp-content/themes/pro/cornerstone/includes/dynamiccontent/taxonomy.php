<?php

/**
 * Author preview grab with URL as the value
 * for choices in a select
 */
add_action( 'cs_dynamic_content_register', function() {
  cs_dynamic_content_register_dynamic_option('taxonomies', [
    'filter' => function($results, $args) {
      $taxonomies = get_taxonomies([] , 'object');

      $out = [
        [
          'value' => '',
          'label' => __('Select a Taxonomy', 'cornerstone'),
        ],
      ];

      foreach ($taxonomies as $tax) {
        // Setup select value
        $out[] = [
          'value' => $tax->name,
          'label' => $tax->label,
        ];
      }

      return $out;
    },
  ]);
});
