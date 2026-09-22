<?php

namespace Themeco\Cornerstone\API\GlobalLooper;

use const Themeco\Cornerstone\API\BUILTIN_VALUES;

use function Cornerstone\Api\Controls\controls_global;

/**
 * Ready to setup
 */
$values = cs_api_global_values();
$values['global_id'] = '';

/**
 * API Looper
 */
cs_looper_provider_register("apiglobal", [
  'label' => __("External API Global", "cornerstone"),
  'controls' => controls_global(),

  'values' => $values,

  'loop_keys' => true,

  /**
   * Main
   */
  'filter' => function($result, $args) {
    return cs_api_global_run($args);
  },
]);
