<?php

namespace Cornerstone\QueryBuilder\Meta;

/**
 * Meta query integration
 * uses list editor meta_values
 */
const META_KEY = 'looper_provider_query-builder_meta_values';
const META_RELATION_KEY = 'looper_provider_query-builder_meta_relation';

function formatQuery($relation, $metaValues) {
  if (is_string($metaValues)) {
    $metaValues = cs_maybe_json_decode(cs_dynamic_content($metaValues, false));
  }
  
  $metaValues = cs_dynamic_content_object($metaValues);
  $metaValues = cs_maybe_json_decode($metaValues);

  if (empty($metaValues)) {
    $metaValues = [];
  }

  // Built value
  $orderBys = [];
  $metaQuery = [];

  foreach ($metaValues as $values) {
    $id = cs_get_array_value($values, 'id', 0);
    $type = cs_get_array_value($values, 'type', '');

    // Add to built named queries
    $metaQuery[$id] = [
      'key' => $values['key'],
      'value' => $values['value'],
      'compare' => $values['compare'],
    ];

    // Value type
    if (!empty($type)) {
      $metaQuery[$id]['type'] = $type;
    }

    // No orderby
    if (empty($values['orderby'])) {
      continue;
    }

    $orderBys[$id] = cs_get_array_value($values, 'orderby_direction', 'DESC');
  }

  // Relation
  $metaQuery['relation'] = $relation;
  $metaQuery['relation'] = cs_dynamic_content($metaQuery['relation']);

  return [
    'orderBys' => $orderBys,
    'query' => $metaQuery,
  ];
}

add_filter("cs_looper_provider_query_args", function($config, $element = []) {

  // Check is query builder
  $type = cs_get_array_value($element, 'looper_provider_type', '');

  if ($type !== 'query-builder') {
    return $config;
  }

  // Grab looper provider values values
  $metaValues = cs_get_array_value($element, META_KEY, []);
  $relation = cs_get_array_value($element, META_RELATION_KEY, "AND");

  $currentMetaQuery = cs_get_array_value($config, 'meta_query', []);

  // Building query from value system
  $builtQuery = formatQuery($relation, $metaValues);

  $orderBys = $builtQuery['orderBys'];
  $metaQuery = $builtQuery['query'];


  // No metavalues setup
  if (empty($metaQuery)) {
    return $config;
  }

  // Only expecting one order by
  // convert to multi
  if (isset($config['orderby']) && !is_array($config['orderby'])) {
    $config['orderby'] = [
      $config['orderby'] => cs_get_array_value($config, 'order', 'DESC'),
    ];

    unset($config['order']);
  }

  // No order by at all
  if (empty($config['orderby'])) {
    $config['orderby'] = [];
  }

  // Merge originals with new additions
  $config['orderby'] = array_merge($config['orderby'], $orderBys);

  $config['meta_query'] = array_merge($currentMetaQuery, $metaQuery);

  return $config;
}, 10, 2);
