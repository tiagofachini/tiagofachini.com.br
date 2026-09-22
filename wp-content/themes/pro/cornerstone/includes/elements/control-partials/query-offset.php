<?php


cs_register_control_partial( 'query-offset', function($extend = []) {
  $options_provider_query_offset = [
    'choices' => [
      [ 'value' => '',           'label' => cs_recall( 'label_off' )   ],
      [ 'value' => 'by_page', 'label' => __('By Page', 'cornerstone') ],
      [ 'value' => '{{custom}}', 'label' => cs_recall( 'label_count' ) ],
    ],
    'custom_value' => get_option( 'posts_per_page' ),
    'placeholder'  => get_option( 'posts_per_page' ),
  ];

  return array_merge([
    'key'        => 'looper_provider_query_offset',
    'type'       => 'choose',
    'label'      => cs_recall( 'label_offset' ),
    'options'    => $options_provider_query_offset,
    'description' => __('Where to start the user query loop. \'By Page\' calculates the offset based on the count and the value of the ?paged query var', 'cornerstone'),
  ], $extend);
});
