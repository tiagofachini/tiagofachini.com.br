<?php

/**
 * Gradient based shared funcs
 */

function cs_gradient_from_array($val) {
  // Improper value or already a gradient string
  if (!is_array($val)) {
    return $val;
  }

  // Apply dynamic content
  $val = cs_dynamic_content_object($val);

  $out = $val['type'] . '( ';

  // Direction of gradient
  $direction = $val['direction'];
  if (!empty($direction) && $direction !== 'auto') {
    $out .= $direction . ', ';
  }

  $last = false;
  $colors = $val['colors'];
  $size = count($colors);

  // Loop colors and build each , separated color
  foreach ($colors as $index => $colorValue) {
    $last = ($index + 1) === $size;

    $out .= cs_color_apply($colorValue['color']);

    // Color from / percentage or px
    $from = !empty($colorValue['from'])
      ? $colorValue['from']
      : null;

    if (!empty($from) && $from !== 'auto') {
      $from = is_array($from)
        ? implode($from)
        : $from;

      $out .= ' ' . $from;
    }

    // Color To / percentage or px
    $to = $colorValue['to'];
    if (!empty($to) && $to !== 'auto') {
      $to = is_array($to)
        ? implode($to)
        : $to;

      $out .= ' ' . $to;
    }

    if (!$last) {
      $out .= ', ';
    }
  }

  $out .= ')';

  return $out;
}
