<?php

// Register Connection DC
add_action('cs_dynamic_content_setup', function() {
  // Register group
  cornerstone_dynamic_content_register_group([
    'name'  => 'cookie',
    'label' => __( 'Cookie', CS_LOCALIZE ),
  ]);

  // Cookie Get
  cornerstone_dynamic_content_register_field([
    'name'  => 'get',
    'group' => 'cookie',
    'type' => 'scalar',
    'label' => __( 'Get', CS_LOCALIZE ),
    'deep' => true,
    'controls' => [
      // Name
      [
        'key' => 'name',
        'label' => __('Name', 'cornerstone'),
        'type' => 'text',
      ]
    ],
  ]);

  // Cookie Set
  cornerstone_dynamic_content_register_field([
    'name'  => 'set',
    'group' => 'cookie',
    'type' => 'scalar',
    'label' => __( 'Set', CS_LOCALIZE ),
    'deep' => true,
    'controls' => [
      // Name
      [
        'key' => 'name',
        'label' => __('Name', 'cornerstone'),
        'type' => 'text',
      ],

      // Value
      [
        'key' => 'value',
        'label' => __('Value', 'cornerstone'),
        'type' => 'text',
      ],

      // Expires
      cs_partial_controls('seconds-select', [
        'key' => 'expires',
        'label' => __('Expires', 'cornerstone'),
        'once' => false,
      ]),

      // Path
      [
        'key' => 'path',
        'label' => __('Path', 'cornerstone'),
        'type' => 'text',
        'options' => [
          'placeholder' => __('/', CS_LOCALIZE),
        ],
      ],

      // Domain
      [
        'key' => 'domain',
        'label' => __('Domain', 'cornerstone'),
        'type' => 'text',
      ],
    ],
  ]);

}, 220);

add_filter( 'cs_dynamic_content_cookie', function($result, $field, $args = []) {

  switch ($field) {
    case 'get':
      if (empty($args['name'])) {
        $result = $_COOKIE;
      } else {
        $result = cs_get_array_value($_COOKIE, $args['name'], '');
      }
      break;

    case 'set':
      $name = cs_get_array_value($args, 'name', '');
      $value = cs_get_array_value($args, 'value', '');
      $expires = cs_get_array_value($args, 'expires', 0);

      if (empty($expires)) {
        $expires = 0;
      } else {
        $expires = time() + $expires;
      }

      // Set cookie
      setcookie(
        $name,
        $value,
        $expires,
        cs_get_array_value($args, 'path', '/'),
        cs_get_array_value($args, 'domain', ''),
        cs_get_array_value($args, 'secure', false),
        cs_get_array_value($args, 'httponly', false)
      );

      // In case we use this cookie later
      $_COOKIE[$name] = $value;
      break;
  }

  return $result;

}, 10, 3 );
