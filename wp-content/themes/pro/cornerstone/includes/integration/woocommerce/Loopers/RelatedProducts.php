<?php

/**
 * Related Products looper
 *
 * uses the WooCommerce function wc_get_related_products() to get the ids then uses
 * linked_product_looper_by_ids() to get the post objects
 */

use function Cornerstone\WooCommerce\LinkedProduct\linked_product_looper_by_ids;
use function Cornerstone\WooCommerce\LinkedProduct\linked_product_values;

cs_looper_provider_register('wc-related-products', [
  'label' => __('Related Products', 'cornerstone'),

  'controls' => 'Cornerstone\WooCommerce\LinkedProduct\linked_product_controls',

  'values' => linked_product_values(),

  // Main
  'filter' => function($results, $args = [], $element = []) {
    $product_id = cs_get_array_value($args, 'product', null);

    // Product ID from 'Current Product'
    if (empty($product_id)) {
      $product = cornerstone('DynamicContent')->get_contextual_product();

      // No current product
      if (empty($product)) {
        return [];
      }

      $product_id = $product->get_id();
    }

    $related = wc_get_related_products($product_id);

    // If none dont go through linked product looper
    // or it will give bad results
    if (empty($related)) {
      return [];
    }

    return linked_product_looper_by_ids($related, $args);
  },
]);
