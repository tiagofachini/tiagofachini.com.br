<?php

/**
 * Get plugins and courses as single list for extension management
 */
function cs_max_get_all() {
  $plugins = apply_filters('cs_max_get_plugins', []);

  $courses = cs_max_get_courses();
  $courses = $courses['data'];

  $out = [];

  foreach ($plugins as $plugin) {
    $out[$plugin['slug']] = $plugin;
  }

  foreach ($courses as $course) {
    // This is already a plugin
    // Like cornerstone charts
    if (!empty($out[$course['slug']])) {
      continue;
    }

    // Used by UI
    $course['is_course'] = true;

    $out[$course['slug']] = $course;
  }

  return array_values($out);
}

add_filter('cs_dashboard_extensions', function($extensions) {
  $extensions['max'] = cs_max_get_all();

  return $extensions;
});

// Add Max site imports to the Sites section
// of the themeco section
// This is only setup setup for personify
add_filter('cs_app_remote_assets', function($cached) {
  $courses = cs_max_get_courses();

  // Loop courses
  foreach ($courses['data'] as $courseData) {
    // Not purchased
    if (empty($courseData['purchased'])) {
      continue;
    }

    // Loop course items
    foreach ($courseData['course'] as $course) {
      // Not a site import
      if ($course['tcoFileType'] !== 'site') {
        continue;
      }

      $sanitized = sanitize_title($course['title']);

      $cached['templates']['sites'][] = [
        'id' => $course['tcoFile'],
        'title' => $course['title'],
        'type' => 'site',
        'preview' => 'https://theme.co/app/uploads/personify/tiles/' . $sanitized . '.png',
        'demo_url' => 'https://theme.co/personify/' . $sanitized,
        'isRemote' => true,
      ];
    }
  }

  return $cached;
});

add_action('cs_max_output_admin_extensions', function() {
  include(__DIR__ . "/views/page-home-box-max.php");
});
