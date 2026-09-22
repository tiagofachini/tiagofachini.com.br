<?php

/**
 * Menus dynamic content choices
 */

add_action('cs_dynamic_content_setup', function() {
  cs_dynamic_content_register_dynamic_option('menu', [
    'filter' => function($results, $args) {
      $menus = wp_get_nav_menus();

      $out = [
        [
          'value' => '',
          'label' => __('Select a menu', 'cornerstone'),
        ],
      ];

      foreach($menus as $menu) {
        $out[] = [
          'value' => $menu->term_id,
          'label' => $menu->name,
        ];
      }

      return $out;
    },
  ]);
});
