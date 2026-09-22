<?php

/**
 * Extensions group shared by all types of theme options
 */
add_filter('cs_theme_options_extensions_group', function() {
  return [
    'type'  => 'group-sub-module',
    'label' => __( 'Miscellaneous', '__x__' ),
    'options' => [ 'tag' => 'misc', 'name' => 'cs-theme-options:misc' ],
    'controls' => apply_filters(
      'cs_theme_options_extensions',
      apply_filters('cs_theme_options_misc_controls', [])
    ),
  ];
});
