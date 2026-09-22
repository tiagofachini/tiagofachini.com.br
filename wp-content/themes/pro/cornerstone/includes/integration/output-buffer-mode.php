<?php

cs_dashboard_register_options([
  'values' => [
    'cs_global_output_buffer_mode' => false,
  ],
]);

add_filter('cs_dashboard_advanced_controls', function($controls) {
  $controls[] = [
    'type' => 'toggle',
    'key' => 'cs_global_output_buffer_mode',
    'label' => __('Output Buffer Mode', 'cornerstone'),
    'description' => __('Will run entire output in an output buffer. Allows you to do more complex header manipulations in Cornerstone.', 'cornerstone'),
  ];

  return $controls;
});

if (!get_option('cs_global_output_buffer_mode', false)) {
  return;
}

cs_output_buffer_mode();
