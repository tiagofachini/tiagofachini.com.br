<?php

namespace Themeco\Cornerstone\Services;

use DomainException;
use Themeco\Cornerstone\Services\Routes;
use Themeco\Cornerstone\Util\CssAsset;
use Themeco\Cornerstone\Util\JsAsset;

class AdminDashboard implements Service {

  private $routes;
  private $jsAsset;
  private $cssAsset;
  private $permissions;


  public function __construct(
    Routes $routes,
    JsAsset $jsAsset,
    CssAsset $cssAsset,
    Permissions $permissions
  ) {
    $this->routes = $routes;
    $this->jsAsset = $jsAsset;
    $this->cssAsset = $cssAsset;
    $this->permissions = $permissions;
  }

  public function setup() {
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

    do_action('cs_dashboard_setup');

    $this->routes->add_route('post', 'dashboard-save', [$this, 'saveDashboard']);
  }

  /**
   * Update dashboard from params
   */
  public function saveDashboard($params = []) {
    if (!current_user_can('manage_options')) {
      throw new DomainException('User does not have permissions for `manage_options`');
    }

    $this->updateInternal($params);

    do_action('cs_dashboard_save', $params);

    return [
      'success' => true,
    ];
  }

  /**
   * Updates that are internally used
   */
  private function updateInternal($params) {
    // Product validation
    if (
      isset($params['cs_product_validation_key'])
      && $params['cs_product_validation_key'] !== get_option('cs_product_validation_key')
    ) {
      // @TODO Check validation
    }

    // Workspace Position / Side
    if (
      !empty($params['workspace_position'])
      && in_array($params['workspace_position'], ['left', 'right'])
    ) {
      update_option('cs_workspace_position_default', $params['workspace_position']);
    }

    // Custom App Slug
    if (isset($params['custom_app_slug'])) {
      cornerstone('Settings')->update([
        'custom_app_slug' => $params['custom_app_slug'],
      ]);
    }

    // Permissions
    if (!empty($params['role_manager'])) {
      $save_permissions = cornerstone('Permissions')->updateStoredPermissions( $params['role_manager'] );

      if ( is_wp_error( $save_permissions ) ) {
        return wp_send_json_error( $save_permissions );
      }
    }
  }

  /**
   * Launch dashboard app
   */
  public function launchDashboard() {
    do_action('cornerstone_before_admin_dashboard_boot');

    $dashboardApp = $this->jsAsset->get('assets/js/dashboard');

    $deps = [
      'wp-api-fetch',
      'jquery',
      'lodash',
      'moment',
      'react',
      'react-dom',
    ];

    wp_register_script( 'cs-dashboard', $dashboardApp['url'], $deps, $dashboardApp['version'] );

    // App config extras
    add_filter('cs_app_config', function($data) {
      $settings = get_option('cornerstone_settings', []);

      $app_url = apply_filters("cs_app_url", "");
      $max_url = $app_url . "/max";

      // Values for dashboard controls
      $values = [
        'cs_product_validation_key' => get_option('cs_product_validation_key', ''),
        'custom_app_slug' => cs_get_array_value($settings, 'custom_app_slug', ''),
        'workspace_position' => get_option('cs_workspace_position_default', 'right'),
        'max_url' => $max_url,
      ];

      $values = apply_filters('cs_dashboard_values', $values);

      $roleManagerData = $this->permissions->getAppData();

      $values['role_manager'] = $roleManagerData['permissions'];

      // @TODO move most of this
      $data['settings'] = [
        'role-manager' => $roleManagerData,
        'extensions' => apply_filters('cs_dashboard_extensions', []),
        'values' => $values,
      ];

      $data['app']['use_late_data'] = false;

      // Main data for JS app
      $data['dashboard_app'] = [
        'controls' => apply_filters('cs_dashboard_controls', $this->defaultControls()),
      ];

      // Localization
      $data['app']['shared']['admin_i18n'] = cornerstone('I18n')->group('admin');

      return $data;
    });

    add_filter('cs_app_gzip_data', '__return_false');

    $app = cornerstone('App');
    $app->setArgs([ 'permalinks' => [ '', '' ] ]);
    $app->setupAppConfig('cs-dashboard', false, false);

    wp_enqueue_script('cs-dashboard');

    // Root
    echo '<div id="tco-root"><h2>Loading...</h2></div>';

  }

  /**
   * Built in controls
   */
  private function defaultControls() {

    return [
      // General
      [
        'value' => 'general',
        'label' => __('General', CS_LOCALIZE),
        'options' => [
          'className' => 'tco-dashboard-tab-max-width',
        ],
        'controls' => apply_filters('cs_dashboard_general_controls', [
          // This is still handled by the validation page
          //[
            //'type' => 'license-validation',
            //'key' => 'cs_product_validation_key',
            //'label' => __('License Key', CS_LOCALIZE),
            //'description' => __('Place your Themeco license to unlock automatic updates, access to support, and Extensions', CS_LOCALIZE),
          //],

          // Custom Path
          [
            'type' => 'text',
            'key' => 'custom_app_slug',
            'label' => __('Path', CS_LOCALIZE),
            'description' => __('Set the location used to load Cornerstone. It will default to /cornerstone if not set.', CS_LOCALIZE),
            'options' => [
              'placeholder' => 'cornerstone',
            ],
          ],

          // Workspace Default
          [
            'type' => 'select',
            'key' => 'workspace_position',
            'label' => __('Workspace', CS_LOCALIZE),
            'description' => __('Set the location of the workspace in Cornerstone. See preferences in the app for more options.', CS_LOCALIZE),
            'options' => [
              'choices' => [
                [
                  'value' => 'left',
                  'label' => __('Workspace Left', CS_LOCALIZE),
                ],
                [
                  'value' => 'right',
                  'label' => __('Workspace Right', CS_LOCALIZE),
                ],
              ],
            ],
          ],

        ]),
      ],

      // Permissions
      [
        'value' => 'permissions',
        'label' => __('Permissions', CS_LOCALIZE),
        'options' => [
          'className' => 'tco-dash-tab-container-no-padding tco-dash-tab-container-role-manager',
        ],
        'controls' => [
          [
            'type' => 'role-manager',
            'key' => 'role_manager',
            'label' => __('Permissions', CS_LOCALIZE),
          ],
        ],
      ],

      // Extensions
      //[
        //'value' => 'extensions',
        //'label' => __('Extensions', CS_LOCALIZE),
        //'controls' => [
          //[
            //'type' => 'extension-manager',
            //'key' => 'extension',
            //'options' => [
              //'extension_key' => 'extensions',
              //'ignoreControlData' => true,
            //],
          //],

          //[
            //'type' => 'extension-manager',
            //'label' => __('Approved Plugins', 'cornerstone'),
            //'key' => 'extension',
            //'options' => [
              //'extension_key' => 'approvedPlugins',
              //'ignoreControlData' => true,
            //],
          //],
        //],
      //],

      // Max
      //[
        //'value' => 'max',
        //'label' => __('Max', CS_LOCALIZE),
        //'controls' => [
          //[
            //'type' => 'max-content',
            //'key' => 'max_content',
            //'options' => [
              //'ignoreControlData' => true,
            //],
          //],

          //[
            //'type' => 'extension-manager',
            //'key' => 'max_extensions',
            //'options' => [
              //'extension_key' => 'max',
              //'ignoreControlData' => true,
              //'is_max' => true,
            //],
          //],
        //],
      //],

      // Advanced
      [
        'value' => 'advanced',
        'label' => __('Advanced', CS_LOCALIZE),
        'controls' => (array)apply_filters('cs_dashboard_advanced_controls', []),
      ],

    ];
  }

  /**
   * Enqueue dashboard styles
   */
  public function enqueue($hook = 'cornerstone_page_cornerstone-dashboard') {
    if ($hook === 'cornerstone_page_cornerstone-dashboard') {
      // Styles
      $app_style_asset = $this->cssAsset->get( "assets/css/app/dashboard");
      wp_register_style('cs_dashboard_style', $app_style_asset['url'], [
        'code-editor',
        'wp-auth-check'
      ], $app_style_asset['version']);
      wp_enqueue_style('cs_dashboard_style');
    }
  }
}
