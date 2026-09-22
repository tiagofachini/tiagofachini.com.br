<?php

// Order By control

add_filter("cs_query_builder_orderby_control", function($result, $settings = []) {
  $prefix = cs_get_array_value($settings, 'prefix', 'looper_provider_query_');

  $orderType = cs_get_array_value($settings, 'order_type', 'post');

  $metaChoiceType = cs_get_array_value($settings, 'meta_choice_type', 'dynamic:postmeta:ignore-post');

  $orderOptions = [];

  if ($orderType === 'user') {
    $orderOptions = cornerstone( 'Locator' )->get_user_orderby_options();
  } else {
    $orderOptions = cornerstone( 'Locator' )->get_orderby_options();
  }

  // Conditions
  // remove query-builder part after move to new Looper Provider API
  $conditions = cs_get_array_value($settings, 'conditions', [
    [
      'looper_provider_type' => 'query-builder'
    ]
  ]);

  $options_omega_group_toggle_asc_desc = [
    'toggle' => [
      'always_show' => true,
      'on'          => 'ASC',
      'off'         => 'DESC',
      'on_label'    => cs_recall( 'label_ascending' ),
      'off_label'   => cs_recall( 'label_descending' ),
    ],
  ];

  $metaOrderKey = 'orderby_meta_key';

  // I screwed up the name on query-builder
  if (!empty($prefix)) {
    $metaOrderKey = 'looper_provider_query-builder_orderby_meta_key';
  }

  return [
    'keys' => [
      'direction' => $prefix . 'order',
      'field'     => $prefix . 'orderby',
    ],
    'type'    => 'group-picker',
    'label'   => cs_recall( 'label_order_by' ),
    'options' => [
      'icon'  => 'order',
      'label' => '{{orderby:field,direction}}',
    ],
    'conditions' => $conditions,
    'controls'   => [
      [
        'key'      => $prefix . 'order',
        'type'     => 'group',
        'label'    => cs_recall( 'label_field' ),
        'options'  => $options_omega_group_toggle_asc_desc,
        'controls' => [
          // Order by type
          [
            'key'     => $prefix . 'orderby',
            'type'    => 'select',
            'options' => [
              'choices' => $orderOptions,
            ],
          ],

          // Meta Key if order type is valid
          [
            'key' => $metaOrderKey,
            'label' => __('Meta Key', 'cornerstone'),
            'type' => 'select',
            'options' => [
              'choices' => $metaChoiceType,
              'placeholder' => 'Select',
            ],
            'conditions' => [
              [
                'key' => $prefix . 'orderby',
                'op' => 'IN',
                'value' => ['meta_value', 'meta_value_num'],
              ]
            ],
          ],
        ],
      ],
    ],
  ];
}, 0, 2);
