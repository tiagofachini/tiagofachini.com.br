<?php


namespace Themeco\Cornerstone\Tss\Functions;

class DynamicContent extends BuiltInFunction {

  public function run( $input ) {
    $val = $this->isTyped($input) ? $input->value() : $input;

    if (method_exists($val, 'value')) {
      $val = $val->value();
    }

    return cs_dynamic_content($val);
  }

}
