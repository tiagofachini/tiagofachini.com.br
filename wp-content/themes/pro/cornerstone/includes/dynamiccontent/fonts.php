<?php

namespace Cornerstone\DynamicContent\Fonts;

// For reference in option fields
const DC_TYPE = 'globalfont';


// Register Theme option DC UI
add_action('cs_dynamic_content_setup', function() {
  // Global Color
  cornerstone_dynamic_content_register_field([
    'name'  => 'font',
    'group' => 'global',
    'type' => 'mixed',
    'label' => __( 'Font', CS_LOCALIZE ),
    'controls' => [

      // Type
      [
        'key' => 'id',
        'type' => 'select',
        'label' => __('Font', CS_LOCALIZE),
        'options' => [
          'choices' => 'dynamic:' . DC_TYPE,
          'placeholder' => __('Enter Font ID', CS_LOCALIZE),
        ],
      ]

    ],
    'deep' => true,
  ]);

  // Dynamic Option for Global Color
  cs_dynamic_content_register_dynamic_option(DC_TYPE, [
    'key' => DC_TYPE,
    'type' => 'select',
    'label' => __('Global Font', CS_LOCALIZE),
    'options' => [
      'choices' => 'dynamic:' . DC_TYPE,
      'placeholder' => __('Enter Font ID', CS_LOCALIZE),
    ],
  ]);

}, 200);

/**
 * Dynamic Option for all colors
 */
add_filter('cs_dynamic_content_register', function($output = []) {

  cs_dynamic_content_register_dynamic_option('globalfont', [
    'filter' => function($results, $args) {
      $fonts = cs_fonts_get_all();

      foreach ($fonts as $font) {
        $out[] = [
          'value' => $font['_id'],
          'label' => $font['title'],
        ];
      }

      return $out;
    },
  ]);
});

// Filter to use ID arg to either family processing
// or return the raw ID for TSS
add_filter( 'cs_dynamic_content_global_font', function($result, $args = []) {
  if (empty($args['id'])) {
    return 'inherit';
  }

  // If running from TSS, return ID because it will be passed to the TSS function
  // global-ff which just needs the ID
  if (apply_filters('cs_dynamic_content_parameters_as_css', false)) {
    return $args['id'];
  }

  $family = cs_fonts_process($args['id']);

  return $family;
}, 0, 2 );
