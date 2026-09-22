<?php

/**
 * Menu Looper
 */
add_action('after_setup_theme', function() {

  cs_looper_provider_register('menu', [

    'label' => __('Menu', 'cornerstone'),

    'values' => [
      'menu_id' => '',
    ],

    'controls' => [

      // Menu ID
      [
        'key' => 'menu_id',
        'type' => 'select',
        'label' => __('Menu', 'cornerstone'),
        'options' => [
          'choices' => 'dynamic:menu',
        ],
      ],

    ],

    // Uses range() from min and max
    'filter' => function($result, $args = []) {
      $menu_id = cs_get_array_value($args, 'menu_id', 0);

      $items = wp_get_nav_menu_items($menu_id);

      // This function is techincally private, but it adds the 'current' status
      // That we need for certain designs
      if (function_exists('_wp_menu_item_classes_by_context')) {
        _wp_menu_item_classes_by_context($items);
      }

      $items = cs_menu_build_tree($items);

      return $items;
    },
  ]);

});
