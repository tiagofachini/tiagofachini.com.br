<?php

/**
 * User looper
 */

use function Cornerstone\QueryBuilder\Meta\formatQuery;

add_action('after_setup_theme', function() {

  cs_looper_provider_register('user', [

    'label' => __('Users', 'cornerstone'),

    'values' => [
      // @see https://developer.wordpress.org/reference/classes/wp_user_query/prepare_query/

      'blog_id' => 0,

      'role' => '',
      'role__in' => '',
      'role__not_in' => '',

      'capability' => '',
      'capability__in' => '',
      'capability__not_in' => '',

      'meta_values' => cs_value([], 'markup:array'),
      'meta_relation' => 'OR',

      'include' => cs_value([], 'markup:array'),
      'exclude' => cs_value([], 'markup:array'),

      'search' => '',
      'search_columns' => cs_value([], 'markup:array'),

      'orderby' => 'display_name',
      'order' => 'ASC',

      'offset' => 0,

      'number' => '',

      'custom_atts' => '',
    ],

    'controls' => function() {
      $roleChoices = cs_get_wp_roles_options();

      // This doesnt grab capabilities that only certain roles have
      // Admin probably grabs most of them though
      // @see https://wordpress.stackexchange.com/questions/387040/how-to-get-all-capabilities
      $capabilities = get_role( 'administrator' )->capabilities;
      $capabilities = cs_array_as_choices(array_keys($capabilities));

      return [
        // Role Group Picker
        [
          'type'    => 'group-picker',
          'label'   => __('Roles', 'cornerstone'),
          'keys'    => [
            'role' => 'role',
            'role__in' => 'role__in',
            'role__not_in' => 'role__not_in',
          ],
          'options' => [
            'icon'  => 'wand',
            'label' => __('All', 'cornerstone') . ' ({{length:role}}) '
              . __('In', 'cornerstone') . ' ({{length:role__in}}) '
              . __('Not', 'cornerstone') . ' ({{length:role__not_in}})'
          ],
          'controls'   => [
            // Role
            [
              'key'     => 'role',
              'type'    => 'select-many',
              'label'   => __('Roles', 'cornerstone'),
              'description' => __('Users must match to be included in results. Note that this is an inclusive list: users must match each role.', 'cornerstone'),
              'options' => [
                'choices' => $roleChoices,
              ],
            ],

            // Role In
            [
              'key'     => 'role__in',
              'type'    => 'select-many',
              'label'   => __('Role In', 'cornerstone'),
              'description' => __('Matched users must have at least one of these roles. Contrary to Role which requires the User to have every role provided.', 'cornerstone'),
              'options' => [
                'choices' => $roleChoices,
              ],
            ],

            // Role Not In
            [
              'key'     => 'role__not_in',
              'type'    => 'select-many',
              'label'   => __('Role Not In', 'cornerstone'),
              'description' => __('Users matching one or more of these roles will not be included in results.', 'cornerstone'),
              'options' => [
                'choices' => $roleChoices,
              ],
            ],
          ],
        ],

        // Capabilities Group Picker
        [
          'type'    => 'group-picker',
          'label'   => __('Capabilities', 'cornerstone'),
          'keys'    => [
            'capability' => 'capability',
            'capability__in' => 'capability__in',
            'capability__not_in' => 'capability__not_in',
          ],
          'options' => [
            'icon'  => 'tag',
            'label' => __('All', 'cornerstone') . ' ({{length:capability}}) '
              . __('In', 'cornerstone') . ' ({{length:capability__in}}) '
              . __('Not', 'cornerstone') . ' ({{length:capability__not_in}})'
          ],
          'controls'   => [
            // Capabilities
            [
              'key' => 'capability',
              'type' => 'select-many',
              'label' => __('Capabilities', 'cornerstone'),
              'description' => __('Users with all these capabilities. This is an inclusive list: users must match each capability.', 'cornerstone'),
              'options' => [
                'choices' => $capabilities,
              ],
            ],

            // Capabilities In
            [
              'key' => 'capability__in',
              'type' => 'select-many',
              'label' => __('Capabilities In', 'cornerstone'),
              'description' => __('Matched users must have at least one of these capabilities. Does NOT work for capabilities not in the database or filtered via \'map_meta_cap\'. ', 'cornerstone'),
              'options' => [
                'choices' => $capabilities,
              ],
            ],

            // Capabilities Not In
            [
              'key' => 'capability__not_in',
              'type' => 'select-many',
              'label' => __('Capabilities Not In', 'cornerstone'),
              'description' => __('Users matching one or more of these capabilities will not be included in results. Does NOT work for capabilities not in the database or filtered via \'map_meta_cap\'.', 'cornerstone'),
              'options' => [
                'choices' => $capabilities,
              ],
            ],

          ],
        ],

        // Include Group Picker
        [
          'type'    => 'group-picker',
          'label'   => __('Include', 'cornerstone'),
          'keys'    => [
            'include' => 'include',
          ],
          'description' => __('Include specific users from the looper.', 'cornerstone'),
          'options' => [
            'icon'  => 'user',
            'label' => __('Include', 'cornerstone') . ' ({{length:include}})',
          ],

          'controls'   => [
            // Capabilities
            [
              'key' => 'include',
              'type' => 'select-many',
              'label' => __('Include', 'cornerstone'),
              'options' => [
                'choices' => 'user:all',
              ],
            ],
          ],
        ],

        // Exclude
        [
          'type'    => 'group-picker',
          'label'   => __('Exclude', 'cornerstone'),
          'keys'    => [
            'exclude' => 'exclude',
          ],
          'description' => __('Exclude specific users from the looper.', 'cornerstone'),
          'options' => [
            'icon'  => 'close',
            'label' => __('Exclude', 'cornerstone') . ' ({{length:exclude}})',
          ],

          'controls'   => [
            // Capabilities
            [
              'key' => 'exclude',
              'type' => 'select-many',
              'label' => __('Exclude', 'cornerstone'),
              'options' => [
                'choices' => 'user:all',
              ],
            ],
          ],
        ],

        // Search
        [
          'type'    => 'group-picker',
          'label'   => __('Search', 'cornerstone'),
          'keys'    => [
            'search' => 'search',
            'search_columns' => 'search_columns',
          ],
          'options' => [
            'icon'  => 'search',
            'label' => __('Searching', 'cornerstone') . ' ({{remote:search}})',
          ],
          'controls'   => [
            // Search text
            [
              'key' => 'search',
              'type' => 'text',
              'label' => __('Search', 'cornerstone'),
              'options' => [
                'placeholder' => __('Name, ID ...', 'cornerstone'),
              ],
            ],

            // Search columns
            [
              'key' => 'search_columns',
              'type' => 'select-many',
              'label' => __('Columns', 'cornerstone'),
              'description' => __('Leaving empty will search all columns', 'cornerstone'),
              'options' => [
                'choices' => cs_array_as_choices_deslug([
                  'ID', 'user_login', 'user_email',
                  'user_url', 'user_nicename', 'display_name',
                ]),
              ],
            ],
          ],
        ],

        apply_filters('cs_query_builder_meta_value_control', null, [
          'prefix' => '',
          'conditions' => [],
          'choice_type' => 'dynamic:usermeta:ignore-post',
          'meta_key' => 'nickname',
          'compare' => 'LIKE',
        ]),

        // Order By
        // @TODO this needs to not use query_* as the prefix for keys
        apply_filters('cs_query_builder_orderby_control', null, [
          'order_type' => 'user',
          'meta_choice_type' => 'dynamic:usermeta:ignore-post',
          'prefix' => '',
          'conditions' => [],
        ]),

        // Number
        [
          'key' => 'number',
          'type' => 'choose',
          'label' => cs_recall('label_count'),
          'description' => __('Default will search all users, and should be used with caution.', 'cornerstone'),
          'options' => [
            'choices' => [
              [ 'value' => '',           'label' => cs_recall( 'label_default' ) ],
              [ 'value' => '{{custom}}', 'label' => cs_recall( 'label_custom' )  ],
            ],
            'custom_value' => 4,
            'placeholder'  => 10,
          ],
        ],

        // Offset
        cs_partial_controls('query-offset', [
          'key' => 'offset',
        ]),

        // Custom JSON
        [
          'key'         => 'custom_atts',
          'type'        => 'code-editor',
          'label'       => __('Custom', 'cornerstone'),
          'description' => __('get_users() arguments through JSON. Merges with and overrides all other looper arguments.', 'cornerstone'),
          'options'     => [
            'mode'         => 'json',
            'button_label' => cs_recall('label_edit'),
            'header_label' => cs_recall('label_json'),
          ],
        ],

      ];
    },

    // Uses range() from min and max
    'filter' => function($result, $args = []) {
      // When role__in is empty it will ignore role args
      // So we remove empty
      cs_delete_empty($args);

      // Meta query setup
      if (!empty($args['meta_values'])) {
        $built = formatQuery(
          $args['meta_relation'],
          $args['meta_values']
        );

        $args['orderby'] = !empty($args['orderby'])
          ? array_merge([$args['orderby']], $built['orderBys'])
          : $built['orderBys'];

        $args['meta_query'] = $built['query'];
      }

      unset($args['meta_relation']);
      unset($args['meta_values']);

      // Order by meta value setup
      if (
        !empty($args['orderby'])
        && (
          $args['orderby'] === 'meta_value' || $args['orderby'] === 'meta_value_num'
        )
      ) {
        $args['meta_key'] = $args['orderby_meta_key'];
      } else {
        unset($args['orderby_meta_key']);
      }

      unset($args['orderby_meta_key']);

      // Custom JSON attributes - merges with and overrides all other args
      if (!empty($args['custom_atts'])) {
        $atts = cs_maybe_json_decode($args['custom_atts']);

        // If raw decode failed, value may be a DC string not yet resolved
        if (empty($atts)) {
          $atts = cs_dynamic_content($args['custom_atts']);
          $atts = cs_maybe_json_decode($atts);
        }

        $atts = cs_dynamic_content_object($atts);

        if (is_array($atts)) {
          $args = array_merge($args, $atts);
        }
      }

      unset($args['custom_atts']);

      $users = get_users($args);

      return $users;
    },
  ]);

});
