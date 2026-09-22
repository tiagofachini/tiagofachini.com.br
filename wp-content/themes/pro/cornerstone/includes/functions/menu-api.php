<?php

/**
 * Builds menu items as a tree
 * requires passing in items grabbed from wp_get_nav_menu_items
 */
function cs_menu_build_tree($elements, $parentId = 0) {
  if (empty($elements)) {
    return [];
  }

  $branch = [];

  foreach ($elements as &$element) {
    $element = (array)$element;

    if ((int)$element['menu_item_parent'] === $parentId) {
      $children = cs_menu_build_tree($elements, $element['ID']);

      $element['children'] = $children;

      $branch[] = $element;
    }
  }

  return $branch;
}
