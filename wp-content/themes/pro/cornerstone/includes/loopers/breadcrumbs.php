<?php


/**
 * Breadcrumbs Looper
 */
add_action('after_setup_theme', function() {

  cs_looper_provider_register('breadcrumbs', [

    'label' => __('Breadcrumbs', 'cornerstone'),

    'values' => [
      'home_label' => __('Home', 'cornerstone'),
    ],

    'controls' => [
      [
        'type' => 'text',
        'key' => 'home_label',
        'label' => __('Home Label'),
      ],
    ],

    /**
     * Uses Breadcrumbs::getItems
     */
    'filter' => function($result, $args = []) {
      $crumbs = Cornerstone('Breadcrumbs')->getItems($args);

      return $crumbs;
    },
  ]);

});

/**
 * Prefab of Breadcrumb (Elements)
 */
add_action('cs_register_prefab_elements', function() {

  cs_register_prefab_element( 'navigation', 'breadcrumb-elements', [
    'title' => __('Breadcrumbs (Elements)', 'cornerstone'),
    'type' => 'layout-div',
    'scope'  => [ 'all' ],

    'values' => [
      '_type' => 'layout-div',
      '_label' => __('Breadcrumbs (Elements)', 'cornerstone'),
      'layout_div_flex_align' => 'center',
      'layout_div_flex_direction' => 'row',
      'layout_div_flexbox' => true,
      'looper_provider' => true,
      'looper_provider_type' => 'breadcrumbs',
      '_modules' => [
        [
          '_type' => 'layout-div',
          '_label' => 'Crumb',
          'layout_div_flex_align' => 'center',
          'layout_div_flex_direction' => 'row',
          'layout_div_flexbox' => true,
          'looper_consumer' => true,
          '_modules' => [
            [
              '_type' => 'layout-div',
              '_label' => 'Link Box',
              'layout_div_box_shadow_dimensions' => '!0em 0em 0em 0em',
              'layout_div_href' => "{{dc:looper:field key='url'}}",
              'layout_div_margin' => '!0px',
              'layout_div_padding' => '!0px',
              'layout_div_tag' => 'a',
              'looper_consumer' => true,
              '_modules' => [
                [
                  '_type' => 'text',
                  'text_content' => "{{dc:looper:field key='label'}}",
                  'text_padding' => '0.807em',
                  '_modules' => [
                  ]
                ]
              ]
            ],
            [
              '_type' => 'icon',
              'icon' => 'arrow-right',
              'show_condition' => [
                [
                  'group' => true,
                  'condition' => 'looper:index',
                  'value' => 'last',
                  'toggle' => false
                ]
              ],
            ]
          ]
        ]
      ]
    ]
  ]);

});
