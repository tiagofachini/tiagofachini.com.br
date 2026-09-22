<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// =============================================================================
// INDEX.PHP
// -----------------------------------------------------------------------------
// Templates are automatically populated from Cornerstone integration
// =============================================================================

get_header();
do_action( 'tco_theme_template' );
get_footer();
