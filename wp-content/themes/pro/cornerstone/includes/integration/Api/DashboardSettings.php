<?php

add_filter('cs_dashboard_advanced_controls', function($controls) {

  $controls[] = [
    'key' => 'cs_api_extension_enabled',
    'type' => 'sub-group-extended',
    'options' => array_merge(
      cs_recall( 'options_group_toggle_off_on_bool' ),
      [
        'logo' => CS_ROOT_URL . 'assets/img/external-api-logo.png',
      ]
    ),
    'label' => __('External REST API', CS_LOCALIZE),
    'description' => __('Connect 3rd party data sources directly to your website. <a href="https://theme.co/docs/external-api-integration" target="_blank" rel="noopener noreferrer">Learn more</a>.', CS_LOCALIZE),
    'controls' => [
      [
        'type' => 'textarea',
        'key' => 'cs_api_extension_allowlist',
        'label' => __('Allowlist', CS_LOCALIZE),
        'description' => __('Enter all external domains that you would like to access from your website. Leave this empty to allow any domain. Each new domain should be entered on a new line. Include the protocol (e.g. http or https).', CS_LOCALIZE),
        'options' => [
          'placeholder' => 'https://theme.co/',
        ],
      ],
    ],
  ];

  return $controls;
});

// Register options for save and data
cs_dashboard_register_options([
  'values' => [
    'cs_api_extension_enabled' => false,
    'cs_api_extension_allowlist' => '',
  ],
]);
