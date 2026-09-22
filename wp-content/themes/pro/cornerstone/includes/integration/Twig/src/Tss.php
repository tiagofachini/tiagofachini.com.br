<?php

/**
 * Run twig before processing on Custom CSS
 */
add_filter('cs_tss_post_detect_dynamic_content', function($value, $containerScope, $key, $tss) {
  $value = preg_replace_callback( '/{{.*?}}/', function($matches) use ($containerScope, $key, $tss){
    return $tss->makeDcVar( $containerScope, $key, $matches[0]);
  }, $value);

  return cs_twig_render($value);
}, 10, 4);
