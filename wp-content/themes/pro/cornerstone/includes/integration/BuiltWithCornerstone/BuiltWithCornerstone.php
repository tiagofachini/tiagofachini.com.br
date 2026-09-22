<?php

namespace Cornerstone\BuiltWithCornerstone;

const URL = 'https://theme.co/cornerstone/';

// Register Options
cs_dashboard_register_options([
  'values' => [
    'cs_built_with_cornerstone_enabled' => false,
    'cs_built_with_cornerstone_url' => URL,
    'cs_built_with_cornerstone_svg_color' => '#442EBF',
    'cs_built_with_cornerstone_text_color' => 'black',
    'cs_built_with_cornerstone_background_color' => 'white',
    'cs_built_with_cornerstone_position' => 'fixed',
    'cs_built_with_cornerstone_alignment' => 'right',
    'cs_built_with_cornerstone_border_radius' => '1em',
    'cs_built_with_cornerstone_border_color' => 'transparent',
    'cs_built_with_cornerstone_border_width' => '0px',
  ],
]);

// Controls are added to the General Area
add_filter('cs_dashboard_general_controls', function($controls) {
  $conditionsIsFixed = [
    [
      'key' => 'cs_built_with_cornerstone_position',
      'op' => '==',
      'value' => 'fixed',
    ]
  ];

  $controls[] = [
    'key' => 'cs_built_with_cornerstone_enabled',
    'label' => __('Built with Cornerstone', 'cornerstone'),
    'description' => __('Share the love and display a custom badge on your website showing it was built with Cornerstone.', 'cornerstone'),
    'type' => 'sub-group-extended',
    'options' => array_merge(
      cs_recall( 'options_group_toggle_off_on_bool' ),
      [
        'logo' => CS_ROOT_URL . 'assets/img/built-with-cornerstone.png',
      ]
    ),
    'controls' => [
      // URL
      [
        'type' => 'text',
        'key' => 'cs_built_with_cornerstone_url',
        'label' => __('URL', 'cornerstone'),
      ],

      // SVG / Icon
      [
        'type' => 'color',
        'key' => 'cs_built_with_cornerstone_svg_color',
        'label' => __('Icon Color', 'cornerstone'),
      ],

      // Text Color
      [
        'type' => 'color',
        'key' => 'cs_built_with_cornerstone_text_color',
        'label' => __('Text Color', 'cornerstone'),
      ],

      // Background Color
      [
        'type' => 'color',
        'key' => 'cs_built_with_cornerstone_background_color',
        'label' => __('Background', 'cornerstone'),
      ],

      // Position
      [
        'type' => 'select',
        'key' => 'cs_built_with_cornerstone_position',
        'label' => __('Position', 'cornerstone'),
        'options' => [
          'choices' => cs_array_as_choices_ucwords([
            'fixed', 'bottom',
          ]),
        ],
      ],

      // Alignment
      [
        'type' => 'select',
        'key' => 'cs_built_with_cornerstone_alignment',
        'label' => __('Alignment', 'cornerstone'),
        'options' => [
          'choices' => cs_array_as_choices_ucwords([
            'left', 'center', 'right',
          ]),
        ],
      ],

      // Border Color
      [
        'type' => 'color',
        'key' => 'cs_built_with_cornerstone_border_color',
        'label' => __('Border Color', 'cornerstone'),
      ],

      // Border Width
      cs_recall('control_mixin_border_width', [
        'key' => 'cs_built_with_cornerstone_border_width',
      ]),

      // Border radius
      cs_recall('control_mixin_border_radius', [
        'key' => 'cs_built_with_cornerstone_border_radius',
        'conditions' => $conditionsIsFixed,
      ]),

    ],
  ];

  return $controls;
});


// Not enabled
if (!get_option('cs_built_with_cornerstone_enabled', false)) {
  return;
}

// Add CSS
add_action('wp_enqueue_scripts', function() {
  $url = cornerstone('Styling')->getCSSAsset('assets/css/site/built-with-cornerstone');
  wp_register_style( 'cs-built-with-cornerstone', $url['url'], [], $url['version'] );
  wp_enqueue_style( 'cs-built-with-cornerstone' );

  $isFixed = get_option('cs_built_with_cornerstone_position') === 'fixed';

  $borderRadius = $isFixed
    ? esc_attr(get_option('cs_built_with_cornerstone_border_radius', '1.5em'))
    : 0;

  $svgColor = apply_filters(
    'cs_css_post_process_color',
    get_option('cs_built_with_cornerstone_svg_color')
  );

  $textColor = apply_filters(
    'cs_css_post_process_color',
    get_option('cs_built_with_cornerstone_text_color')
  );

  $backgroundColor = apply_filters(
    'cs_css_post_process_color',
    get_option('cs_built_with_cornerstone_background_color')
  );

  $borderColor = apply_filters(
    'cs_css_post_process_color',
    get_option('cs_built_with_cornerstone_border_color')
  );

  $borderProp = $isFixed ? 'border' : 'border-top';

  $borderWidth = get_option('cs_built_with_cornerstone_border_width');

  $style = <<<CSS
<style id='built-with-cornerstone-styles'>
.tco-built-with-cornerstone {
  border-radius: {$borderRadius};
  background: {$backgroundColor};
  {$borderProp}: solid {$borderWidth} {$borderColor};
}

.tco-built-with-cornerstone svg path {
  color: {$svgColor};
}

.tco-built-with-cornerstone p {
  color: {$textColor};
}
</style>
CSS;

  echo $style;
});


// output and only output once
function output() {
  static $did_output;
  if (is_null($did_output)) {
    $did_output = false;
  }

  if ($did_output) {
    return;
  }

  $svg = file_get_contents(CS_ROOT_PATH . '/assets/img/cs.svg');

  $txt = __('Built with Cornerstone', 'cornerstone');

  $className = esc_attr(
    get_option('cs_built_with_cornerstone_position', 'fixed')
  );

  // Add alignment
  $className .= ' ' . esc_attr(
    get_option('cs_built_with_cornerstone_alignment', 'right')
  );

  $href = get_option('cs_built_with_cornerstone_url', URL);

  // Create tag and output
  echo cs_tag('a', [
    'class' => "tco-built-with-cornerstone {$className}",
    'href' => $href,
    'target' => '_blank',
    'rel' => 'noopener noreferrer',
  ], "{$svg} <p>{$txt}</p>");

  $did_output = true;
}

// Output built with cornerstone during wp_footer or CS footer
cs_action_defer('cs_colophon', __NAMESPACE__ . '\output', [], 100);
cs_action_defer('wp_footer', __NAMESPACE__ . '\output', [], 100);
