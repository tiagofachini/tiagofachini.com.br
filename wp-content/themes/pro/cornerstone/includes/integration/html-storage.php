<?php

/**
 * HTML storage saving from dashboard
 */

use Themeco\Cornerstone\Documents\Document;

// Add extension settings values so it can update and see current state
add_filter('cs_dashboard_values', function($settings) {
  $settings['cs_document_build_as_html'] = get_option('cs_document_build_as_html', true);

  return $settings;
});


// Find all content types
function cs_content_storage_migrate($page = 0) {
  // Check permission
  if (!current_user_can('manage_options')) {
    throw new DomainException('Does not have `manage_options` permission');
  }

  // build as html
  $asHTML = get_option('cs_document_build_as_html', true);

  set_time_limit(0);

  global $wpdb;

  $table = esc_sql("{$wpdb->prefix}posts");
  $metaTable = esc_sql("{$wpdb->prefix}postmeta");

  // Table and where selector
  $querySelector = "
    FROM {$table} p
    INNER JOIN {$metaTable} pm ON p.ID = pm.post_id
    WHERE post_type NOT IN (
      'cs_layout', 'cs_header', 'cs_footer',
      'cs_layout_single', 'cs_layout_archive',
      'cs_layout_single-wc', 'cs_layout_archive-wc',
      'cs_template', 'cs_global_block', 'revision'
    )
    AND pm.meta_key = '_cornerstone_data'
    AND LENGTH(pm.meta_value) > 0
  ";

  // Count SQL
  $countSQL = "SELECT COUNT(p.ID) as count
    $querySelector
  ";

  $countRows = $wpdb->get_results($countSQL, ARRAY_A);
  $totalCount = (int)$countRows[0]['count'];
  $pageOffset = (int) apply_filters('cs_html_migrate_per_pages', 6);
  $offset = $pageOffset * $page;


  // Grab all CS content types with cornerston data in them
  $sql = "SELECT p.ID, CAST(pm.meta_value as CHAR) as meta_value
    $querySelector
    LIMIT $pageOffset
    OFFSET $offset
  ";
  $pages = $totalCount / $pageOffset;

  $rows = $wpdb->get_results($sql, ARRAY_A);


  // Loop rows
  foreach ($rows as $row) {
    $ID = (int)$row['ID'];

    try {
      cs_content_storage_migrate_post($ID, $row['meta_value'], $asHTML);
    } catch(Throwable $e) {
      $trace = $e->getTraceAsString();
      throw new RuntimeException("Post ID {$ID} encountered an issue : " . $e->getMessage() . ' : ' . $trace);
    }
  }

  $hasMore = $pages > ($page + 1);

  // Cache purging if at end
  if (!$hasMore) {
    do_action('cs_purge_tmp');
    do_action('cs_purge_all');
    do_action('cs_purge_cache');
  }

  return [
    'total' => $totalCount,
    'offset' => $pageOffset,
    'pages' => $pages,
    'page' => $page,
    'hasMore' => $hasMore,
  ];
}

// Migrates a single post to a certain storage type
function cs_content_storage_migrate_post($ID, $csMeta, $asHTML = false) {
  $post = get_post($ID);
  $doc = Document::locate($ID);

  // Not valid document or type
  if (empty($doc) || $doc->type() !== 'content') {
    return false;
  }

  // HTML and already an HTML content storage
  // or content is not using cornerstone shortcodes
  if ($asHTML && (
    cs_uses_cornerstone_html($post->post_content) || !cs_uses_cornerstone_shortcodes($post->post_content)
  )) {
    return true;
  }

  // Shortcodes and already a shortcode content
  // or content is not using cornerstone HTML
  if (!$asHTML && (
    cs_uses_cornerstone_shortcodes($post->post_content) || !cs_uses_cornerstone_html($post->post_content)
  )) {
    return true;
  }

  // Grab elements and send update action
  $elements = json_decode($csMeta, true);
  $settings = get_post_meta($ID, '_cornerstone_settings', true);
  $settings = json_decode($settings, true);

  return $doc->updateElements($elements, $settings, false);
}

// Ajax Action setup
add_action('admin_init', function() {
  cornerstone()->resolve('Themeco\\Cornerstone\\Util\\AdminAjax')
    ->setAction( 'document_storage_migrate' )
    ->setHandler(function($params) {
      // Permission check
      if (!current_user_can('manage_options')) {
        wp_send_json_error([
          'message' => 'Does not have `manage_options` permission',
        ]);
        exit;
      }


      $buildAsHTML = true;
      $optionFilter = null;

      // Update option before migration
      if (isset($params['option'])) {
        $buildAsHTML = $params['option'] === 'true' || $params['option'] === '1';
        $optionFilter = function() use ($buildAsHTML) {
          return $buildAsHTML;
        };
        add_filter('option_cs_document_build_as_html', $optionFilter);
      }

      // Run migration now
      try {
        $output = cs_content_storage_migrate((int)$params['page']);
      } catch(Exception $e) {
        $errorMsg = 'Could not migrate document storage. ' . $e->getMessage();
        trigger_error($errorMsg);
        wp_send_json_error([
          'message' => $errorMsg,
        ]);
        exit;
      }

      // On finish save setting
      if (empty($output['hasMore']) && isset($params['option'])) {
        remove_filter('option_cs_document_build_as_html', $optionFilter);
        update_option('cs_document_build_as_html', $buildAsHTML, true);
      }

      wp_send_json_success($output);
      exit;
    })
    ->start();
});

// Advanced controls in dashboard
add_filter('cs_dashboard_advanced_controls', function($controls) {
  $controls[] = [
    'type' => 'sub-group-extended',
    'label' => __('Content Storage', 'cornerstone'),
    'description' => __('Manage how content is stored on your website. <a href="https://theme.co/docs/content-storage" target="_blank" rel="noopener noreferrer">Learn more</a>.', 'cornerstone'),
    'options' => [
      'logo' => CS_ROOT_URL . 'assets/img/content-storage.png',
    ],
    'controls' => [
      [
        'type' => 'content-storage-migration',
        'key' => 'cs_document_build_as_html',
      ],
    ],
  ];

  return $controls;
}, 9);
