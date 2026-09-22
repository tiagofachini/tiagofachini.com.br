<?php

/**
 * Desandro Masonry integration into the Row Element
 */

namespace Cornerstone\DesandroMasonry;


// Enqueue JS
function enqueue() {
  static $enqueued;

  if (!empty($enqueued)) {
    return;
  }

  // Register Script
  $asset = cs_js_asset_get('assets/js/site/cs-masonry');
  wp_register_script('cs-masonry', $asset['url'], ['cs'], $asset['version'], true);

  wp_enqueue_script('cs-masonry');

  $enqueued = true;
}


function values() {
  return [
    'masonry_enabled' => cs_value(false, 'markup:bool'),
    'masonry_columnWidth' => cs_value(200, 'markup:int'),
    'masonry_gutter' => cs_value(0, 'markup:int'),
    'masonry_transitionDuration' => cs_value('0s', 'markup'),
    'masonry_originLeft' => cs_value(true, 'markup:bool'),
    'masonry_originTop' => cs_value(true, 'markup:bool'),
    'masonry_horizontalOrder' => cs_value(true, 'markup:bool'),
    'masonry_percentPosition' => cs_value(false, 'markup:bool'),
    'masonry_fitWidth' => cs_value(false, 'markup:bool'),
    'masonry_stagger' => cs_value(30, 'markup:int'),
  ];
}

function addValues($element) {
  $element['values'] = array_merge(
    $element['values'],
    values()
  );

  return $element;
}


/**
 * Controls building
 */
function controls($group = 'layout_div:design') {
  return [
    'type' => 'group',
    'label' => __('Masonry', 'cornerstone'),
    'group' => $group,
    'key' => 'masonry_enabled',
    'options' => cs_recall( 'options_group_toggle_off_on_bool' ),
    'controls' => controls_simple(),
  ];
}

/**
 * Simple controls
 * Row element does not need a lot of the controls
 * since the row element itself handles the widths of children
 */
function controls_simple() {
  return [
    // Transition Duration @TODO

    // Origin Left
    [
      'type' => 'toggle',
      'key' => 'masonry_originLeft',
      'label' => __('Origin Left', 'cornerstone'),
      'description' => __('When disabled the bricks will be aligned from right to left', 'cornerstone'),
    ],

    // Origin Top
    [
      'type' => 'toggle',
      'key' => 'masonry_originTop',
      'label' => __('Origin Top', 'cornerstone'),
      'description' => __('When disabled the bricks will be aligned from the bottom to the top. Sometimes leaving empty space at the top of the container', 'cornerstone'),
    ],

    // Horizontal Order
    [
      'type' => 'toggle',
      'key' => 'masonry_horizontalOrder',
      'label' => __('Horizontal Order', 'cornerstone'),
      'description' => __('When disabled the order of the bricks will not preserve their order horziontally', 'cornerstone'),
    ],

    // Stagger
    cs_partial_controls('range', [
      'key' => 'masonry_stagger',
      'label' => __('Stagger', 'cornerstone'),
      'description' => __('Amount of time in milliseconds before creating the layout. If you have a third party JS in one of your bricks this can help to make sure the layout sizing happens after that element has created itself', 'cornerstone'),
      'min' => 0,
      'max' => 2000,
      'steps' => 1,
    ]),
  ];
}

/**
 * Adds masonry values to layout div and layout row
 */
add_filter('cs_register_element_layout-row', __NAMESPACE__ . '\addValues');

/**
 * Add masonry controls to layout div and layout row
 */
add_filter('cs_layout_row_design_controls', function($controls) {
  $controls[] = controls('layout_row:design');

  return $controls;
});

/**
 * Pre render and add special data-attribute
 */

add_filter('cs_layout_row_inner_attributes', function($attributes, $element) {
  $masonry = cs_split_to_object($element, 'masonry');

  if (empty($masonry['enabled'])) {
    return $attributes;
  }

  // Enqueue JS
  enqueue();

  $attributes['class'] .= ' x-masonry';

  // Not needed for JS
  unset($masonry['enabled']);
  unset($masonry['columnWidth']);

  $masonry['percentPosition'] = true;

  // Add custom data-x-masonry for later usage
  $attributes['data-x-masonry'] = json_encode($masonry);

  return $attributes;
}, 10, 2);

// Changes flex-basis to 'width' so masonry can properly determine the width setting
add_filter('cs_layout_row_tss_width_property', function($widthProperty, $data) {
  if (!empty($data['masonry_enabled'])) {
    return 'width';
  }

  return $widthProperty;
}, 10, 2);
