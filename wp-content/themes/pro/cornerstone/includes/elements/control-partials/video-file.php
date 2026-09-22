<?php

cs_register_control_partial( 'video-file', function($settings = []) {

  // Output
  // ------

  return array_merge_recursive(
    [
      'type' => 'file',
      'options' => [
        'file_types' => ['video'],
      ],
    ],
    $settings
  );
});
