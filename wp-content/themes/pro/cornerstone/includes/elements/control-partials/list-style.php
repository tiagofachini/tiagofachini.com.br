<?php

cs_register_control_partial('list-style', function($extend = []) {
  $prefix = cs_get_array_value($extend, 'prefix', '');

  return array_merge([
    'type' => 'group',
    'label' => __('List Style', 'cornerstone'),
    'controls' => [
      // Style
      [
        'key' => $prefix . 'list-style-type',
        'type' => 'select',
        'label' => __('Type', 'cornerstone'),
        'description' => __('This controls the bullets at the start of every list item. Use none to hide completely.', 'cornerstone'),
        'options' => [
          'choices' => cs_array_as_choices_ucwords([
            'none',
            'disc',
            'square',
            'circle',
            'decimal',
            'trad-chinese-informal',
            'lower-alpha',
            'upper-alpha',
            'lower-latin',
            'upper-latin',
            'armenian',
            'georgian',
            'cjk-decimal',
            'hebrew',
            'hiragana',
            'katakana',
            'kannada',
            'roman',
            'arabic-indic',
            'bangla',
            'devanagari',
            'ethiopic',
            'khmer',
            'korean',
            'lao',
            'mongolian',
            'myanmar',
            'sundanese',
            'syriac',
            'tai-lei',
            'thaana',
            'vietnamese'
          ]),
        ],
      ],


      // In & Out
      [
        'key' => $prefix . 'list-style-position',
        'type' => 'choose',
        'label' => __('Position', 'cornerstone'),
        'description' => __('Inside will make the bullet points appear within the text block, making the list feel more compact and the text seem to flow right around the marker. Outside will make the bullet points sit outside the text block, creating a bit of space between the bullet and the text.', 'cornerstone'),
        'options' => [
          'choices' => cs_array_as_choices_ucwords([
            'inside',
            'outside',
          ]),
        ],
      ],
    ],
  ], $extend);
});
