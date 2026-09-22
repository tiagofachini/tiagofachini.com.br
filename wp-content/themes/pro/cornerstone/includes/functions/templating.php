<?php

/**
 * Template include overwriter
 * Some template including is more aggressive then us so this ends up being needed
 *
 * @param string $changeToPath
 * @param string $overwriteSearch
 * @param int $priority
 */
function cs_template_include_overwrite($changeToPath, $overwriteSearch, $priority = 999999) {
  add_filter('template_include', function($template) use ($changeToPath, $overwriteSearch) {
    if (strpos($template, $overwriteSearch) !== false) {
      $template = $changeToPath;
    }

    return $template;
  }, $priority);
}
