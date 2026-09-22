<?php

class Cornerstone_Looper_Provider_Taxonomy extends Cornerstone_Looper_Provider_Array
{
  public function get_array_items($element)
  {
    $term_ids = null;
    $operator = $element['looper_provider_query_term_in'] === false ? 'exclude' : 'include';

    if (is_array($element['looper_provider_query_term_ids'])) {
      $term_ids = array_map(function ($item) {
        return explode('|', $item)[1];
      }, $element['looper_provider_query_term_ids']);
    }

    $args = [
      'taxonomy' => $element['looper_provider_tax'],
      'hide_empty' => $element['looper_provider_tax_hide_empty']
    ];

    if (!is_null($term_ids)) {
      $args[$operator] = $term_ids;
    }

    // Term Parent
    if (!empty($element['looper_provider_tax_parent'])) {
      $args['parent'] = $element['looper_provider_tax_parent'];
    }

    // WooCommerce uses an internal term count caching system
    // Without this overwrite it would give zero as the term count every time
    // @see https://stackoverflow.com/questions/29774556/woocommerce-get-term-count-and-get-terms-count-different
    $shouldOverwriteAjax = class_exists('WooCommerce') && !is_ajax();
    if ($shouldOverwriteAjax) {
      add_filter('wp_doing_ajax', '__return_true');
    }

    $terms = get_terms($args);

    if ($shouldOverwriteAjax) {
      remove_filter('wp_doing_ajax', '__return_true');
    }

    if (is_wp_error($terms)) {
      return [];
    }

    return $terms;
  }
}

