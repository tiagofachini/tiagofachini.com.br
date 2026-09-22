<?php

add_filter('cs_condition_contexts', function($contexts) {
  $contexts['controls']['global'][] = [
    'key'    => 'global:is-mobile',
    'label'  => __('Mobile Device', 'cornerstone'),
    'toggle' => [
      'type'   => 'boolean',
      'labels' => [
        __('Yes', 'cornerstone'),
        __('No', 'cornerstone'),
      ],
    ],
    'criteria' => ['type' => 'static'],
  ];

  return $contexts;
});

add_filter('cs_condition_rule_global_is_mobile', function($result = false, $args = []) {
  return wp_is_mobile();
}, 0, 2);
