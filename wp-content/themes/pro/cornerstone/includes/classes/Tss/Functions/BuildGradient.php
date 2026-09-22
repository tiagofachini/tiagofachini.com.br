<?php

namespace Themeco\Cornerstone\Tss\Functions;

class BuildGradient extends GradientFunction {

  public function run( $input ) {
    $val = $this->isTyped($input) ? $input->value() : $input;

    // @TODO Consolidate with IsGradient
    if (is_string($val) && strpos($val, '--tco-') !== false) {
      $id = preg_replace('/.*tco-/', '', $val);
      $id = str_replace(')', '', $id);

      $runtime = static::getTSSRuntime();
      $dcVars = $runtime->getDynamicContentVars();
      $dcVar = $dcVars[$id];

      $val = cs_dynamic_content($dcVar, false);

      if (is_array($val)) {
        return cs_gradient_from_array($val);
      }

      return $val;
    }

    // Raw gradient string
    if (static::isGradientString($val)) {
      // Multi gradient string
      if (is_array($val)) {
        $strs = [];

        foreach ($val as $innerGradient) {
          if (!method_exists($innerGradient, 'value')) {
            continue;
          }

          $strs[] = $innerGradient->value();
        }

        return implode(', ', $strs);
      }

      return $val;
    }

    // Global Gradient
    if (is_string($val)) {
      $gc = static::getGlobalColors();

      $id = preg_replace('/.*:/', '', $val);
      $global = $gc->locateColor($id);

      if (!empty($global['value'])) {
        return cs_gradient_from_array($global['value']);
      }

      return 'transparent';
    }

    $valArray = $this->convertToArray($val);

    return cs_gradient_from_array($valArray);
  }

  /**
   * Convert the input class system to just an array
   * Part of me thinks this already exists, but couldn't figure out how to just get
   * the raw assoc array
   *
   * @return array
   */
  private function convertToArray($val) {
    $out = [];

    // @TODO the rest of this should still go to runForArray
    $out['type'] = $val['type']->value();

    // Direction of gradient
    $out['direction'] = $val['direction']->toString();

    $out['colors'] = [];

    $colors = $val['colors']->value();

    // Loop colors and build each , separated color
    foreach ($colors as $index => $colorObj) {
      $colorValue = $colorObj->value();

      // Color from / percentage or px
      $from = !empty($colorValue['from'])
        ? $colorValue['from']->value()
        : null;

      // Color To / percentage or px
      $to = !empty($colorValue['to'])
        ? $colorValue['to']->value()
        : null;

      $out['colors'][] = [
        'color' => $colorValue['color']->value(),
        'from' => $from,
        'to' => $to,
      ];
    }

    return $out;
  }
}
