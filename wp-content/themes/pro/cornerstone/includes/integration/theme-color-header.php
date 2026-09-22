<?php

/**
 * This adds a meta tag 'theme-color' which will change the browser color on Safari and maybe others
 * It's a theme option and added to the design section
 */

namespace Cornerstone\ThemeColorHeader;


// Main add control filter
function addControls($controls) {
  $controls[] = [
    'key' => 'theme_color_header_enabled',
    'type' => 'sub-group',
    'label' => __('Theme Color', 'cornerstone'),
    'description' => __('This will add a meta tag that will change the browser header color. This is used on mobile platforms and certain browsers. HTTPS is required.', 'cornerstone'),
    'options' => cs_recall( 'options_group_toggle_off_on_bool' ),
    'controls' => [
      [
        'key' => 'theme_color_header_light',
        'type' => 'color',
        'label' => __('Light', 'cornerstone'),
      ],
      [
        'key' => 'theme_color_header_dark',
        'type' => 'color',
        'label' => __('Dark', 'cornerstone'),
      ]
    ],
  ];

  return $controls;
}


// Controls
add_filter('cs_theme_options_layout_and_design_controls', __NAMESPACE__ . '\addControls');

add_filter('cs_standalone_theme_options_design_controls', __NAMESPACE__ . '\addControls');


// Add meta tag to <head>
add_action('wp_head', function() {
  // Not enabled
  if (empty(cs_stack_get_value('theme_color_header_enabled'))) {
    return;
  }

  $create = function($key, $colorScheme) {
    $themeColor = cs_stack_get_value($key);
    $themeColor = apply_filters('cs_css_post_process_color', $themeColor);

    // Not set
    if (empty($themeColor) || $themeColor === 'transparent') {
      return;
    }

    echo "<meta name='theme-color' media='(prefers-color-scheme: {$colorScheme})' content='{$themeColor}'/>";
  };

  $create('theme_color_header_light', 'light');
  $create('theme_color_header_dark', 'dark');
});


// Register options
cs_stack_register_options([
  'theme_color_header_enabled' => false,
  'theme_color_header_light' => 'transparent',
  'theme_color_header_dark' => 'transparent',
]);
