<?php

// Object fit select

cs_register_control_partial( 'object-fit', function($settings = []) {

  // Output
  // ------

  return array_merge_recursive(
    [
      'type' => 'select',
      'options' => [
        'choices' => [
          [ 'value' => 'contain',    'label' => cs_recall( 'label_contain' )    ],
          [ 'value' => 'cover',      'label' => cs_recall( 'label_cover' )      ],
          [ 'value' => 'fill',       'label' => cs_recall( 'label_fill' )       ],
          [ 'value' => 'none',       'label' => cs_recall( 'label_none' )       ],
          [ 'value' => 'scale-down', 'label' => cs_recall( 'label_scale_down' ) ],
        ]
      ],
    ],
    $settings
  );
});

