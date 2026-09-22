<?php

add_action( 'cs_dynamic_content_setup', function() {
  cornerstone_dynamic_content_register_group([
    'name'  => 'element',
    'label' => __('Element'),
  ]);

  // Field
  cornerstone_dynamic_content_register_field([
    'name'  => 'field',
    'group' => 'element',
    'label' => 'Field',
    'controls' => [
      [
        'key'     => 'key',
        'type'    => 'inspector-data-select',
        'label'   => 'Key',
      ],
    ],
    'twig_format' => '{{element.field.$key}}',
    'deep' => true,
  ]);

}, 1000);

// Filter to grab element data
add_filter('cs_dynamic_content_element', function($result, $field, $args = []) {
  $rendereringElement = apply_filters("cs_current_renderering_element", []);

  if (empty($rendereringElement)) {
    trigger_error('Could not find rendering element for Element DC');
    return '';
  }

  switch ($field) {
    case 'field':
      $key = cs_get_array_value($args, 'key');

      if (empty($key)) {
        $result = $rendereringElement;
      } else {
        $result = cs_get_array_value($rendereringElement, $key, '');
      }
      break;
  }

  return $result;
}, 10, 3);
