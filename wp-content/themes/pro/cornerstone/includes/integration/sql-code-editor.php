<?php

/**
 * Register CS SQL script
 */
add_action('cs_sql_codemirror_enqueue', function() {
  $sql = cs_js_asset_get('assets/js/app/cornerstone-sql');
  wp_register_script( 'cs-sql', $sql['url'], ['code-editor'], $sql['version'] );
  wp_enqueue_script( 'cs-sql' );
}, -1);

/**
 * Cornerstone booting
 */
add_action('cornerstone_app_enqueue_scripts', function() {
  do_action('cs_sql_codemirror_enqueue');
});
