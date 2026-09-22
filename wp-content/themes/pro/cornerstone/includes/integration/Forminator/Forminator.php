<?php

// Add new values to form integration
add_filter('cs_register_element_form-integration', function($element) {
  $element['values']['form_integration_forminator_id'] = cs_value( '', 'markup:int', true );

  return $element;
});


// Add integration type
add_filter('cs_element_form_integration_types', function($types) {
  $types[] = [
    'value' => 'forminator',
    'label' => __( 'Forminator', 'cornerstone' )
  ];

  return $types;
});


// Controls
add_filter('cs_element_form-integration_controls', function($controls) {
  $conditions = [
    [
      'form_integration_type' => 'forminator'
    ]
  ];

  $controls[] = [
    'type'       => 'group',
    'label'      => __('Forminator', 'cornerstone'),
    'group'      => 'form_integration:setup',
    'conditions' => $conditions,
    'controls'   => [
      [
        'key'     => 'form_integration_forminator_id',
        'type'    => 'select',
        'label'   => __('Form', 'cornerstone'),
        'options' => [
          'choices' => 'dynamic:forminator-forms'
        ],
      ],
    ]
  ];

  return $controls;
});


// Dynamic option for forminator forms
add_action( 'cs_dynamic_content_register', function() {
  cs_dynamic_content_register_dynamic_option('forminator-forms', [
    'filter' => function($results, $args) {
      if (!class_exists('Forminator_API')) {
        return [
          [
            'value' => '',
            'label' => __('Forminator is not active', 'cornerstone'),
          ],
        ];
      }

      try {
        $forms = Forminator_API::get_forms(null, 0, 50);

        $out = [
          [
            'value' => '',
            'label' => __('Select a Form', 'cornerstone'),
          ],
        ];

        foreach ($forms as $form) {
          $out[] = [
            'value' => $form->id,
            'label' => $form->settings['formName'],
          ];
        }

        return $out;
      } catch (Throwable $e) {
        return [
          [
            'label' => $e->getMessage(),
            'value' => null,
          ]
        ];
      }
    },
  ]);
});

// Output
add_filter('cs_element_form-integration_output_forminator', function($content, $data) {
  if (empty($data['form_integration_forminator_id'])) {
    return $content;
  }

  $output = "[forminator_form id='{$data['form_integration_forminator_id']}']";

  if (is_cornerstone_preview()) {
    $output .= '<style>.forminator-custom-form { display: block !important; }</style>';
  }

  return do_shortcode($output);
}, 10, 2);
