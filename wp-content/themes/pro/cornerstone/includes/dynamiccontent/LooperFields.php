<?php


// Populate with looper data, and the depth data
add_filter('cs_tree_data_element_selector_looper', function() {
  $LooperManager = CS('Looper_Manager');

  $looperFields = $LooperManager->get_current_data_choices();

  // Build Depths
  $stack = $LooperManager->get_provider_stack();

  $depths = [];

  foreach ($stack as $provider) {
    $choices = $LooperManager->buildChoices($provider->get_current_data_formatted());

    if (method_exists($provider, 'translate_field_choices')) {
      $choices = $provider->translate_field_choices($choices);
    }

    $depths[] = $choices;
  }

  return [
    'current' => $looperFields,
    'depths' => $depths,
  ];
});

add_filter('cs_tree_data_element_default_looper', function($depths) {
  $post = get_post();
  return cs_array_as_choices(array_keys((array)$post));
});
