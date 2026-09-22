<?php

// Theme options has started up
add_action('init', function() {

  add_action('cs_scroll_progress_enqueue', function() {
    $asset = cs_js_asset_get('assets/js/site/cs-scroll-percentage');
    wp_register_script('cs-scroll-percentage', $asset['url'], ['cs'], $asset['version'], true);

    wp_enqueue_script('cs-scroll-percentage');
  });


  // Prefab for Scroll Percentage
  add_action('cs_register_prefab_elements', function() {
    cs_register_prefab_element( 'content', 'line-scroll-percentage', [
      'type'   => 'line',
      'scope'  => [ 'all' ],
      'title'  => __('Scroll Progress'),
      'values' => [
        '_label' => __('Scroll Progress', 'cornerstone'),
        'scroll_progress_enabled' => true,
      ]
    ]);
  });

});

// Line Element Controls
add_filter('cs_line_element_primary_controls', function($controls) {

  $controls[] = [
    'type' => 'group',
    'group' => 'line:design',
    'label' => __('Scroll Progress', 'cornerstone'),
    'controls' => [
      [
        'type' => 'toggle',
        'key' => 'scroll_progress_enabled',
        'description' => __('Will control the width (or height in vertical mode) of the line element based on the percentage the user has scrolled through the page', 'cornerstone'),
        'label' => __('Enabled', 'cornerstone'),
      ]
    ],
  ];

  return $controls;
});

// Add scroll progress enabled to line element values
add_filter('cs_line_element_values', function($values) {
  $values['scroll_progress_enabled'] = cs_value(false);

  return $values;
});

// Enqueue if rendering line with scroll progress enabled
add_action('cs_element_pre_render_line', function($data) {
  // enqueue if progress enabled
  if (!empty($data['scroll_progress_enabled'])) {
    do_action('cs_scroll_progress_enqueue');
  }

  return $data;
}, 10);
