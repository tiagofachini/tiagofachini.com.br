<?php

/**
 * Update max plugins
 */
add_action("themeco_update_api_response", function($data) {
  if (empty($data['max']) || !apply_filters("cs_max_enabled", true)) {
    update_option("x_max_plugins", []);
    return;
  }

  update_option("x_max_plugins", $data['max']);
});

// Update theme packages cache with
// max plugins
add_filter("themeco_update_cache", function($cache) {

  $maxPlugins = apply_filters("cs_max_get_plugins", []);

  // loop and add to cache
  // which is keyed by plugin file
  foreach ($maxPlugins as $plugin) {
    $cache['plugins'][$plugin['plugin']] = $plugin;
  }

  return $cache;
}, 1000);

add_action('cs_dashboard_extension_before_install', function() {
  apply_filters('cs_max_get_plugins', []);
});

/**
 * Get plugins and get their status
 */
add_filter("cs_max_get_plugins", function() {
  $plugins = get_option("x_max_plugins", []);

  if (empty($plugins)) {
    return [];
  }

  $tgmpa = apply_filters("cs_tgma_get_instance", null);

  // Register and get status' of plugins
  foreach ($plugins as &$plugin) {
    $plugin = array_merge($plugin, $plugin['x-extension']);

    // Use beta package if running prerelease
    $useBeta = defined('THEMECO_PRERELEASES') && !empty(THEMECO_PRERELEASES) && !empty($plugin['edge']);

    $infoToUse = $useBeta ? $plugin['edge'] : $plugin;

    // Purchased
    if (!empty($plugin['purchased']) && !empty($infoToUse['package'])) {
      // Register with TGMA
      $tgmpa->register([
        'slug' => $plugin['slug'],
        'name' => $plugin['title'],
        'file_path' => $plugin['plugin'],
        'source' => $infoToUse['package'],
        'version' => $infoToUse['new_version']
      ]);

      // TGM file path detection doesn't always work so we need to set the known path here
      $tgmpa->plugins[ $plugin['slug'] ]['file_path'] = $plugin['plugin'];

      $plugin['installed'] = $tgmpa->is_plugin_installed( $plugin['slug'] );
      $plugin['activated'] = $tgmpa->is_plugin_active( $plugin['slug'] );
    } else {
      $plugin['installed'] = false;
      $plugin['activated'] = false;
    }
  }

  return $plugins;
});

function cs_max_load_plugins() {
  apply_filters("cs_max_get_plugins", []);
}


/**
 * This adds max plugins to TGMA registry
 */
add_action( 'wp_ajax_cs_extensions_install', function() {
  // We just need the registry, and not the actual plugins
  apply_filters("cs_max_get_plugins", []);
}, 0);

add_action( 'tgmpa_register', function() {
  apply_filters("cs_max_get_plugins", []);
}, 0);

add_action('cs_dashboard_extension_before_deactivate', 'cs_max_load_plugins');
add_action('cs_dashboard_extension_before_install', 'cs_max_load_plugins');


// Add in plugins manually if in standalone
// The normal theme will auto add max plugin updates
add_filter( 'pre_set_site_transient_update_plugins', function($data) {
  // Theme already does all this
  if (!cornerstone('ThemeManagement')->isStandalone()) {
    return $data;
  }

  // Happens if nothing is installed
  if (empty($data->response)) {
    $data->response = [];
  }

  // Setup TGMA and use that later
  apply_filters('cs_max_get_plugins', []);
  $tgmpa = apply_filters('cs_tgma_get_instance', null);

  // Grab plugins
  $plugins = get_option('x_max_plugins', []);

  // Beta usage
  $useBeta = defined('THEMECO_PRERELEASES') && !empty(THEMECO_PRERELEASES);

  $installed_plugins = get_plugins();

  foreach ($plugins as $plugin) {
    // Not installed or not purchased
    if (empty($plugin['purchased']) || !$tgmpa->is_plugin_installed($plugin['slug'])) {
      continue;
    }


    // Setup packages as using the edge or beta
    if ($useBeta && !empty($plugin['edge'])) {
      $plugin = array_merge($plugin, $plugin['edge']);
    }

    // Check if version is different
    if (!empty($installed_plugins[$plugin['plugin']])) {
      $local = $installed_plugins[$plugin['plugin']];

      // current version is same of greater
      if (version_compare( $plugin['new_version'], $local['Version'], '<=' ) ) {
        continue;
      }
    }

    // Add logo to WordPress format if found
    if (!empty($plugin['x-extension']['logo_url'])) {
      $plugin['icons'] = [
        '2x' => $plugin['x-extension']['logo_url'],
      ];
    }

    // Upgrade notice
    $plugin['upgrade_notice'] = sprintf( csi18n('admin.plugin-update-notice'), admin_url( 'admin.php?page=cornerstone-home' ) );

    // Update plugin slug response
    $data->response[$plugin['plugin']] = (object) $plugin;
  }

  return $data;
});
