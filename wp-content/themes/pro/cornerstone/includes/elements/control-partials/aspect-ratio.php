<?php

cs_register_control_partial('aspect-ratio', function($extend = []) {
  $prefix = cs_get_array_value($extend, 'prefix', '');

  return array_merge(
    [
      'key' => $prefix . 'aspect_ratio_value',
      'label' => __('Aspect Ratio', 'cornerstone'),
      'description' => __('Aspect Ratio for the size of this element to follow', 'cornerstone'),
      'type' => 'text',
      'options' => [
        'placeholder' => '16 / 9, or auto 0.5, or auto, ...',
      ],
    ],
    $extend
  );
});
