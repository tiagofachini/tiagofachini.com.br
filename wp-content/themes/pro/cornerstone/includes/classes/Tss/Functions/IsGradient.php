<?php

namespace Themeco\Cornerstone\Tss\Functions;

class IsGradient extends GradientFunction {

  /**
   * Return true or false if a value is a gradient
   */
  public function run( $input ) {
    $val = $this->isTyped($input) ? $input->value() : $input;

    // Check if DC var() passed
    // @TODO Consolidate with IsGradient
    if (is_string($val) && strpos($val, '--tco-') !== false) {
      $id = preg_replace('/.*tco-/', '', $val);
      $id = str_replace(')', '', $id);

      $runtime = static::getTSSRuntime();
      $dcVars = $runtime->getDynamicContentVars();

      $dcVar = $dcVars[$id];

      $val = cs_dynamic_content($dcVar, false);

      // Returning gradient string from DC
      // If not check later if the value is an array gradient
      if (is_string($val)) {
        return strpos($val, 'gradient(') !== false;
      }
    }

    // Raw gradient string
    if (static::isGradientString($val)) {
      return true;
    }

    if (!empty($val['type'])) {
      return true;
    }

    // Array but not a gradient
    if (is_array($val) || empty($val)) {
      return false;
    }

    $gc = static::getGlobalColors();

    $id = preg_replace('/.*:/', '', $val);
    $global = $gc->locateColor($id);

    if (empty($global)) {
      return false;
    }

    // Is object and has a type
    return !empty($global['value']['type']);
  }
}
