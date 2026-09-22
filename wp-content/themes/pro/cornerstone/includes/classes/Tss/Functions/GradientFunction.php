<?php

namespace Themeco\Cornerstone\Tss\Functions;

abstract class GradientFunction extends BuiltInFunction {
  static private $globalColors;
  static private $tssRuntime;

  static public function getGlobalColors() {
    if (empty(self::$globalColors)) {
      self::$globalColors = cornerstone('GlobalColors');
    }

    return self::$globalColors;
  }

  static public function getTSSRuntime() {
    if (empty(self::$tssRuntime)) {
      self::$tssRuntime = cornerstone('Tss')->getRuntime();
    }

    return self::$tssRuntime;
  }

  static public function isGradientString($val) {
    // Multi gradient string check
    if (is_array($val) && !empty($val[0])) {
      $valEntry = $val[0];
      if (method_exists($valEntry, 'value') && strpos($valEntry->value(), 'gradient(') !== false) {
        return true;
      }
    }

    return is_string($val) && strpos($val, 'gradient(') !== false;
  }
}
