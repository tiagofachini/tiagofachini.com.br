<?php

/**
 * Arbirtary Range Looper
 */
add_action('after_setup_theme', function() {

  cs_looper_provider_register('range', [

    'label' => __('Range', 'cornerstone'),

    'values' => [
      'min' => 1,
      'max' => 10,
      'steps' => 1,
    ],

    'controls' => [

      // Min
      [
        'key' => 'min',
        'type' => 'text',
        'label' => __('Min', 'cornerstone'),
        'description' => __('The start of the range. Can be a letter like a or A as well', 'cornerstone'),
      ],

      // Max
      [
        'key' => 'max',
        'type' => 'text',
        'label' => __('Max', 'cornerstone'),
        'description' => __('The end of the range. Can be a letter like z or Z as well', 'cornerstone'),
      ],

      // Steps
      [
        'key' => 'steps',
        'type' => 'text',
        'label' => __('Steps', 'cornerstone'),
        'description' => __('The amount to increment the number by when going from min to max. If you are using letters you will not be able to use decimals', 'cornerstone'),
      ],

    ],

    // Uses range() from min and max
    'filter' => function($result, $args = []) {
      $min = cs_get_array_value($args, 'min', 1);
      $max = cs_get_array_value($args, 'max', 10);
      $steps = (float)cs_get_array_value($args, 'steps', 1);

      // Character based ranges have to use int
      if (!is_numeric($min) || !is_numeric($max)) {
        $steps = (int) $steps;
      }

      // Cannot use 0 or an infinite value
      if (empty($steps) || $steps <= 0) {
        return [];
      }

      // Return the range
      $items = range($min, $max, $steps);

      return $items;
    },
  ]);

});
