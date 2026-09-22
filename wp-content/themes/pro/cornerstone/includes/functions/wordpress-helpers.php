<?php

/**
 * Get page by url for REQUEST_URI
 */
function x_get_page_by_current_path() {
  return x_get_page_by_url($_SERVER['REQUEST_URI']);
}

/**
 * Helper to get a page by url
 */
function x_get_page_by_url($url) {
  $parsedUrl = parse_url($url);

  if (empty($parsedUrl)) {
    return;
  }

  $path = empty($parsedUrl['path'])
    ? "/"
    : $parsedUrl['path'];

  return get_page_by_path($path);
}


/**
 * Output Buffering
 *
 * Buffers the entire WP process, capturing the final output for manipulation.
 */
function cs_output_buffer_mode() {
  static $already_queued;

  if (empty($already_queued)) {
    $already_queued = true;
  }

  ob_start();

  add_action('shutdown', function() {
    $final = '';

    // We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting
    // that buffer's output into the final output.
    $levels = ob_get_level();

    for ($i = 0; $i < $levels; $i++) {
      $final .= ob_get_clean();
    }

    // Apply any filters to the final output
    echo apply_filters('final_output', $final);
  }, 0);
}

function cs_attachment_exists($id) {
  $found = get_posts([
    'fields' => 'ids',
    'post_type' => 'attachment',
    'include' => [$id],
    'post_status' => 'any',
  ]);

  return !empty($found);
}
