<?php

namespace Cornerstone\WordPress\QueryBuilder;


/**
 * Meta values controls
 */
add_filter("cs_query_builder_meta_value_control", function($result, $args = []) {
  // Condition
  // @TODO not needed in the query builder after looperprovider move
  $conditions = cs_get_array_value($args, 'conditions', [
    [
      'looper_provider_type' => 'query-builder'
    ]
  ]);

  $prefix = cs_get_array_value($args, 'prefix', 'looper_provider_query-builder_');

  return [
    'type' => 'group-picker',
    'keys' => [
      'field' => $prefix . 'meta_values',
    ],
    'label'   => __("Meta Values", CS_LOCALIZE),
    'options' => [
      'icon'  => 'dev',
      'label' => __('Meta Values', CS_LOCALIZE) . ' ({{length:field}})',
    ],
    'conditions' => $conditions,

    // Group controls
    'controls'   => [

      // Inside group for styling
      [
        'type' => 'group',
        'controls' => apply_filters("cs_query_builder_meta_value_controls", [], $args),
      ],

    ],
  ];
}, 10, 2);

// Meta Query controls inside picker
add_filter("cs_query_builder_meta_value_controls", function($result, $args = []) {

  $prefix = cs_get_array_value($args, 'prefix', 'looper_provider_query-builder_');

  $choiceType = cs_get_array_value($args, 'choice_type', 'dynamic:postmeta:ignore-post');

  $compareDefault = cs_get_array_value($args, 'compare', 'EXISTS');

  $defaultMetaChoice = cs_get_array_value($args, 'meta_key', '_cornerstone_data');

  return [
    // Relation
    cs_partial_controls('and-or', [
      'key' => $prefix . 'meta_relation',
      'label' => __('Relation', CS_LOCALIZE),
    ]),

    // Meta value editor
    [
      'key'   => $prefix . 'meta_values',
      'type'  => 'list',
      'label' => __("Meta Values", CS_LOCALIZE),
      'options' => [
        'item_label' => '{{key}} {{compare}} {{value}}',
        'initial' => [
          'key' => $defaultMetaChoice,
          'value' => '',
          'compare' => $compareDefault,
          'orderby' => false,
          'orderby_direction' => 'DESC',
          'type' => '',
        ],
      ],
      'controls' => [

        // Key
        [
          'key' => 'key',
          'label' => __("Meta Key", CS_LOCALIZE),
          'type' => 'select',
          'options' => [
            // Uses :ignore-post
            'choices' => $choiceType,
          ],
        ],

        // Compare
        cs_partial_controls('comparison-select', [
          'key' => 'compare',
        ]),

        // Value
        [
          'key' => 'value',
          'label' => __("Meta Value", CS_LOCALIZE),
          'type' => 'text',
        ],

        // Order By
        [
          'key' => 'orderby',
          'label' => __("Order By", CS_LOCALIZE),
          'description' => __("You may need to set this queries 'Order By' to 'Ignore' depending on the type of order value", CS_LOCALIZE),
          'type' => 'toggle',
        ],

        // Meta Type
        [
          'key' => 'type',
          'label' => __('Type', CS_LOCALIZE),
          'description' => __('Cast the value as a specific type', CS_LOCALIZE),
          'type' => 'select',
          'options' => [

            'coalesce' => true,

            'choices' => array_merge(
              [
                [
                  'value' => '',
                  'label' => __('Default', 'cornerstone'),
                ],
              ],
              cs_array_as_choices(['NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'])
            ),
          ],
        ],

        // Direction
        cs_partial_controls("sql-direction", [
          'key' => 'orderby_direction',
          'conditions' => [
            [
              'key' => 'orderby',
              'op' => '==',
              'value' => true,
            ]
          ]
        ]),
      ],

    ],
  ];
}, 10, 2);
