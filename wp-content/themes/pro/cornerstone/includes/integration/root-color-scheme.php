<?php

/**
 * This adds a CSS color-scheme property to :root which signals to the browser
 * the supported color schemes for the site, affecting native UI elements like
 * scrollbars, form controls, and the Canvas/ButtonFace system colors.
 * It's a theme option and added to the design section.
 */

namespace Cornerstone\RootColorScheme;


// Main add control filter
function addControls($controls) {
  $controls[] = [
    'key' => 'root_color_scheme',
    'type' => 'select',
    'label' => __('Color Scheme', 'cornerstone'),
    'description' => __('Sets the CSS color-scheme property on :root, signaling to the browser which color schemes are supported. This affects native UI elements like scrollbars and form controls.', 'cornerstone'),
    'options' => [
      'choices' => [
        [ 'value' => 'normal',       'label' => __('Normal', 'cornerstone') ],
        [ 'value' => 'light',        'label' => __('Light', 'cornerstone') ],
        [ 'value' => 'dark',         'label' => __('Dark', 'cornerstone') ],
        [ 'value' => 'light dark',   'label' => __('Light Dark (prefer light)', 'cornerstone') ],
        [ 'value' => 'dark light',   'label' => __('Dark Light (prefer dark)', 'cornerstone') ],
        [ 'value' => 'only light',   'label' => __('Only Light', 'cornerstone') ],
        [ 'value' => 'only dark',    'label' => __('Only Dark', 'cornerstone') ],
      ],
    ],
  ];

  return $controls;
}


// Controls
add_filter('cs_theme_options_layout_and_design_controls', __NAMESPACE__ . '\addControls');

add_filter('cs_standalone_theme_options_design_controls', __NAMESPACE__ . '\addControls');


// Output :root { color-scheme: ... } in <head>
add_action('wp_head', function() {
  $value = cs_stack_get_value('root_color_scheme');

  // Default 'normal' needs no explicit declaration
  if (empty($value) || $value === 'normal') {
    return;
  }

  echo "<style>:root{color-scheme:" . esc_attr($value) . "}</style>";
});


// Register options
cs_stack_register_options([
  'root_color_scheme' => 'normal',
]);
