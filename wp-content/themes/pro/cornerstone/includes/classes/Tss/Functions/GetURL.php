<?php

namespace Themeco\Cornerstone\Tss\Functions;


class GetURL extends BuiltInFunction {

  public function run( $keyTyped ) {

    $current = $this->stack->lookup('data', 'module-current');
    $key = $keyTyped->toString();

    if (!isset($current[$key])) {
      return $this->stack->evaluator()->makeTyped('primitive', null);
    }

    // Runs into a weird issue where url(34:full) will crash valueParser
    if (!cs_has_dynamic_content($current[$key])) {
      return 'url(' . cs_resolve_image_source($current[$key]) . ')';
    }

    return $this->stack->evaluator()->resolve( call_user_func($this->stack->lookup('parser', 'valueParser'), 'url(' . $current[$key] . ')', $key) );
  }

}
