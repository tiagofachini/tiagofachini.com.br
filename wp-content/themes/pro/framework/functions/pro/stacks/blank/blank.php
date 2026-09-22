<?php

namespace Themeco\BlankStack;

$stackInit = function() {

  // Auto disable buddypress integration
  if (get_option("x_buddypress_enable", true)) {
    update_option("x_buddypress_enable", false, true);
  }

  // Auto disable bbpress integration
  if (get_option("x_bbpress_enable_templates", true)) {
    update_option("x_bbpress_enable_templates", false, true);
  }

};

function blank_stack_load_woocommerce() {
  if (!class_exists('WooCommerce')) {
    return '';
  }

  ob_start();

  include(__DIR__ . '/css/woocommerce-ajax-notification.css');

  return ob_get_clean();
}

// Register stack
cs_stack_register(
  //Config
  [
    'id' => 'blank',
    'label' => __("Blank", "__x__"),
    'css' => [
      // WC
      __NAMESPACE__ . '\blank_stack_load_woocommerce',
    ],

    'init' => $stackInit,

    'controls' => function() {
      $breakpoint_keys = [
        'base' => 'x_breakpoint_base',
      ];

      if (apply_filters( 'cs_allow_breakpoint_ranges_change', true )) {
        $breakpoint_keys['ranges'] = 'x_breakpoint_ranges';
      }

      return [

        // Configuration
        [
          'type'  => 'group-sub-module',
          'label' => __( 'Setup', '__x__' ),
          'options' => [ 'tag' => 'custom-setup', 'name' => 'x-theme-options:custom-setup' ],
          'controls' => array_merge(

            // Breakpoint manager
            [

              // Breakpoint manager
              [
                'type'  => 'breakpoint-manager',
                'label' => 'Breakpoints',
                'group' => 'x:layout-and-design',
                'keys'  => $breakpoint_keys,
                'options' => [
                  'notify' => [
                    'message' => 'Please save and fully refresh Cornerstone for the new breakpoint configuration to take effect.',
                    'timeout' => 10000
                  ]
                ]
              ],

              // OEMBED
              [
                'key'     => 'x_site_link_oembed',
                'type'    => 'toggle',
                'label'   => __( 'Use OEmbed', '__x__' ),
              ],

              [
                'key'     => 'x_site_link_oembed_own_site',
                'type'    => 'toggle',
                'label'   => __( 'OEmbed for Internal Links', '__x__' ),
                'conditions' => [
                  [
                    'x_site_link_oembed' => true,
                  ],
                ],
              ],

              // Scroll Top
              apply_filters("cs_theme_options_scroll_top_group", null),

              // Font awesome
              apply_filters("cs_theme_options_fontawesome_group", null),

            ],

            // Misc controls
            apply_filters('cs_theme_options_misc_controls', [])
          ),

        ],

        // Portfolio
        apply_filters("cs_theme_options_portfolio_group", null),

        // Social
        apply_filters("cs_theme_options_social_group", null),

        // Misc
        apply_filters('cs_theme_options_extensions_group', null),

        // Extensions
        apply_filters("cs_theme_options_woocommerce_ajax_cart_group_module", null),

      ];
    },

  ]
);
