<?php

// Breadcrumb Label
// =============================================================================

add_filter( 'x_breadcrumb_post_type_archive', function( $item, $post_type_obj, $args ) {

  if ($post_type_obj->name === 'mec-events' ) {

    if ( isset( $args['events_label'] ) ) {
      $item['label'] = $args['events_label'];
    } else {

      $events_options = get_option( 'mec_options' );

      if (isset( $events_options['settings']) && isset($events_options['settings']['archive_title'])) {
        $item['label'] = $events_options['settings']['archive_title'];
      }

    }

  }

  return $item;

}, 10, 3 );

// Since this was originally not possible
// this acts a fallback to not overwrite MEC custom layouts
if (!apply_filters('cs_mec_allow_custom_layouts', true)) {
  return;
}

// Prevent MEC single layout from outputting if we want to use our own single layout
add_action('cs_will_output_layout_layout:single', function() {
  cs_template_include_overwrite(
    CS_ROOT_PATH . 'includes/views/theming/layout-single.php',
    'single-mec-events.php'
  );
});

// Prevent MEC archive layout from outputting if we want to use our own archive layout
add_action('cs_will_output_layout_layout:archive', function() {
  cs_template_include_overwrite(
    CS_ROOT_PATH . 'includes/views/theming/layout-archive.php',
    'archive-mec-events.php'
  );

  cs_template_include_overwrite(
    CS_ROOT_PATH . 'includes/views/theming/layout-archive.php',
    'taxonomy-mec-category.php'
  );
});

// Alter the main query of the mec-events so that it uses the wordpress default
// This is only done in their custom template or somewhere else
add_action('pre_get_posts', function($query) {
  // If not on admin dashboard
  // and is the main query of our custom post type
  // change the number of posts_per_page
  if (!is_admin() && $query->is_main_query() && is_post_type_archive('mec-events')) {
    $query->set('posts_per_page', get_option('posts_per_page'));
  }
}, 0);
