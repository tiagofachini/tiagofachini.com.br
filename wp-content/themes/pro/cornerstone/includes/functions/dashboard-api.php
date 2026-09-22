<?php


function cs_dashboard_register_options($config) {
  $options = cs_get_array_value($config, 'values', []);

  foreach ($options as $key => $default) {
    $options[$key] = cs_get_option($key, $default);
  }

  add_filter('cs_dashboard_values', function($values) use ($options) {
    return array_merge($values, $options);
  });

  add_action('cs_dashboard_save', function($params) use ($options) {
    foreach ($options as $key => $value) {
      // Not updating this value
      if (!isset($params[$key])) {
        continue;
      }

      update_option($key, $params[$key], true);
    }
  });
}

/**
 * Add a CS dashboard tab
 *
 * @param string $id
 * @param array $config
 * @param int $position
 */
function cs_dashboard_add_tab($id, $config, $position = -1) {
  if (!is_array($config)) {
    trigger_error('Config for cs_dashboard_add_tab not an array');
    return;
  }

  $config['value'] = $id;


  add_filter('cs_dashboard_controls', function($controls) use ($config, $position) {
    if ($position <= -1) {
      $controls[] = $config;
    } else {
      array_splice($controls, $position, 0, [$config]);
    }

    return $controls;
  });
}
