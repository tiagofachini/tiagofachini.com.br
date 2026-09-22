<?php

namespace Themeco\Cornerstone\API\Looper;

use const Themeco\Cornerstone\API\BUILTIN_VALUES;

use function Cornerstone\Api\Controls\controls;

/**
 * Ready to setup
 */
$values = array_merge(
  BUILTIN_VALUES,
  cs_api_extension_values()
);

/**
 * API Looper
 */
cs_looper_provider_register("api", [
  'label' => __("External API", "cornerstone"),
  'controls' => controls(),

  'values' => $values,

  'array_keys' => apply_filters('cs_external_api_array_keys', ['args', 'headers']),

  'loop_keys' => true,

  /**
   * Main
   */
  'filter' => function($result, $args) {
    return cs_api_run($args);
  },
]);
