<?php

// =============================================================================
// FUNCTIONS/GLOBAL/PLUGINS/REVOLUTION-SLIDER.PHP
// -----------------------------------------------------------------------------
// Plugin setup for theme compatibility.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Get Sliders
//   02. Remove Notices
// =============================================================================


// Get Sliders
// =============================================================================

//
// Returns a normalized list of Revolution Slider sliders, supporting both
// Rev Slider 6 (RevSlider / getArrSliders) and Rev Slider 7
// (RevSliderSlider / get_sliders).
//
// Each entry is an array with keys: 'id', 'slug', 'title'.
// For Rev Slider 7, 'id' falls back to 'slug' if getID() is unavailable.
//

function x_revslider_get_sliders() {

  $sliders = array();

  // Rev Slider 7
  if ( class_exists( 'RevSliderSlider' ) ) {

    $rs  = new RevSliderSlider();
    $raw = $rs->get_sliders();

    foreach ( $raw as $s ) {
      $sliders[] = array(
        'id'    => method_exists( $s, 'get_id' ) ? $s->get_id() : $s->get_alias(),
        'slug'  => $s->get_alias(),
        'title' => $s->get_title(),
      );
    }

  } else if ( class_exists( 'RevSlider' ) ) {

    // Rev Slider 6
    $rs  = new RevSlider();
    $raw = $rs->getArrSliders();

    foreach ( $raw as $s ) {
      $sliders[] = array(
        'id'    => $s->getID(),
        'slug'  => $s->getAlias(),
        'title' => $s->getTitle(),
      );
    }

  }

  return $sliders;

}


// Remove Notices
// =============================================================================

function x_revolution_slider_remove_plugin_row_notices() {
  remove_action( 'after_plugin_row_revslider/revslider.php', array('RevSliderAdmin', 'show_purchase_notice') );
  remove_action( 'after_plugin_row_revslider/revslider.php', array('RevSliderAdmin', 'show_update_notice') );
  remove_action( 'after_plugin_row_revslider/revslider.php', array('RevSliderAdmin', 'add_notice_wrap_pre') );
  remove_action( 'after_plugin_row_revslider/revslider.php', array('RevSliderAdmin', 'add_notice_wrap_post') );
}

add_action( 'admin_notices', 'x_revolution_slider_remove_plugin_row_notices' );
