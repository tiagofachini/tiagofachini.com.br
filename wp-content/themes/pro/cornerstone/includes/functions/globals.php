<?php

/**
 * Global Colors
 */

/**
 * Get all colors from global colors
 * @return array
 */
function cs_color_get_all() {
  return cornerstone("GlobalColors")->getAllColorItems();
}

function cs_color_get_stored() {
  return cornerstone("GlobalColors")->getStoredColorItems();
}

function cs_color_apply($color) {
  return apply_filters( 'cs_css_post_process_color', $color);
}


/**
 * Global Fonts
 */

function cs_fonts_get_all() {
  return cornerstone('GlobalFonts')->get_font_items();
}

function cs_fonts_get_config() {
  return cornerstone('GlobalFonts')->get_font_config();
}

function cs_fonts_process($id) {
  return apply_filters('cs_css_post_process_font-family', $id);
}
