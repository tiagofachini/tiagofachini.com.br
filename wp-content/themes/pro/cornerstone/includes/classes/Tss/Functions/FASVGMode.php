<?php

/**
 * Returns true in TSS if the site is running SVG mode for font awesome
 */

namespace Themeco\Cornerstone\Tss\Functions;

use Themeco\Cornerstone\Services\FontAwesome;

class FASVGMode extends BuiltInFunction {

  public function run( $keyTyped, $noResolve = false ) {
    return FontAwesome::getDefaultLoadType() === 'svg';
  }

}
