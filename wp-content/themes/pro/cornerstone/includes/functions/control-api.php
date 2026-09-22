<?php


/**
 * Cut controls by their key
 */
function cs_control_cut($controls, $controlKey) {

  foreach ($controls as $key => $control) {
    // Key is this control
    if (!empty($control['key']) && $control['key'] === $controlKey) {
      // Would break using unset
      $controls[$key] = null;
      continue;
    }

    // Is group of controls
    if (!empty($control['controls'])) {
      $controls[$key]['controls'] = cs_control_cut($control['controls'], $controlKey);
    }
  }

  return $controls;
}


function cs_control_values_from_assoc($values = [], $designation = 'markup') {
  $out = [];

  foreach ($values as $key => $value) {
    if (is_array($value)) {
      $out[$key] = $value;
      continue;
    }

    $out[$key] = cs_value($value, $designation);
  }

  return $out;
}


// Gets array of values based on a key and the child elements
function cs_control_get_key_values($element, $key) {
  $out = [];

  if (empty($element['_modules'])) {
    return $out;
  }

  // Loop modules
  foreach ($element['_modules'] as $child) {
    if (isset($child[$key])) {
      $out[] = $child[$key];
    }

    if (!empty($child['_modules'])) {
      $out = array_merge($out, cs_control_get_key_values($child, $key));
    }
  }

  return $out;
}
