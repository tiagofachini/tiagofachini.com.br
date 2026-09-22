<?php

// Register Localization DC
add_action('cs_dynamic_content_setup', function() {
  // Register group
  cornerstone_dynamic_content_register_group([
    'name'  => 'localization',
    'label' => __( 'Localization', 'cornerstone' ),
  ]);

  // Locale
  cornerstone_dynamic_content_register_field([
    'name'  => 'locale',
    'group' => 'localization',
    'type' => 'scalar',
    'label' => __( 'Locale', 'cornerstone' ),
  ]);

  // User Locale
  cornerstone_dynamic_content_register_field([
    'name'  => 'user_locale',
    'group' => 'localization',
    'type' => 'scalar',
    'label' => __( 'User Locale', 'cornerstone' ),
  ]);

}, 220);

add_filter( 'cs_dynamic_content_localization', function($result, $field, $args = []) {

  switch ($field) {
    case 'locale':
      $result = get_locale();
      break;
    case 'user_locale':
      $result = get_user_locale(wp_get_current_user());
      break;
  }

  return $result;

}, 10, 3 );


function cs_localization_generate_locale_choices($options = []) {
  // Setup of language select
  require_once ABSPATH . 'wp-admin/includes/translation-install.php';
  $translations = wp_get_available_translations();

  $placeholder = cs_get_array_value($options, 'placeholder', __('Select a Language', 'cornerstone'));


  $availableLanguages = array_unique(
    array_merge(
      [ get_locale() ],
      get_available_languages()
    )
  );

  $languageChoices = [
    [
      'value' => '',
      'label' => $placeholder,
    ]
  ];

  foreach ($availableLanguages as $locale) {
    // This isn't stored in wp_get_available_translations
    if ($locale === 'en_US') {
      $languageChoices[] = [
        'value' => $locale,
        'label' => __('English', 'cornerstone'),
      ];
      continue;
    }

    // Not found or valid locale
    if (empty($translations[$locale])) {
      continue;
    }

    $lang = $translations[$locale];

    $languageChoices[] = [
      'value' => $locale,
      'label' => $lang['english_name'] . ' (' . $lang['native_name'] . ')',
    ];
  }

  return $languageChoices;
}

// Conditions
add_filter( 'cs_condition_contexts', function($contexts) {

  $languageChoices = cs_localization_generate_locale_choices();

  // Add group label
  $contexts['labels']['localization'] = __( 'Localization', 'cornerstone' );

  // Add controls
  $contexts['controls']['localization'] = [
    [
      'key'   => 'localization:locale',
      'label' => __('Locale', 'cornerstone'),
      'toggle' => [
        'type'   => 'boolean',
        'labels' => [
          csi18n('app.conditions.is'),
          csi18n('app.conditions.is-not'),
        ]
      ],
      'criteria' => [
        'type'    => 'select',
        'choices' => $languageChoices,
      ],
    ],

    [
      'key'   => 'localization:user-locale',
      'label' => __('User Locale', 'cornerstone'),
      'toggle' => [
        'type'   => 'boolean',
        'labels' => [
          csi18n('app.conditions.is'),
          csi18n('app.conditions.is-not'),
        ]
      ],
      'criteria' => [
        'type'    => 'select',
        'choices' => $languageChoices,
      ],
    ]
  ];

  return $contexts;
});

// Condition Rule `localization:locale`
add_filter('cs_condition_rule_localization_locale', function($result = false, $args = []) {
  // Return boolean for passed or not
  return $args[0] === get_locale();
}, 0, 2);

add_filter('cs_condition_rule_localization_user_locale', function($result = false, $args = []) {
  // Return boolean for passed or not
  return $args[0] === get_user_locale(wp_get_current_user());
}, 0, 2);

// Add our group the valid global assignments
add_filter('cs_assignment_global_contexts', function($contexts = []) {
  $contexts[] = 'localization';
  return $contexts;
});

// Dynamic option: list of installed WordPress locales (used by Flatpickr locale select)
add_action('cs_dynamic_content_register', function() {
  cs_dynamic_content_register_dynamic_option('locale', [
    'filter' => function($results, $args) {
      return cs_localization_generate_locale_choices([
        'placeholder' => __('User Locale', 'cornerstone'),
      ]);
    },
  ]);
});
