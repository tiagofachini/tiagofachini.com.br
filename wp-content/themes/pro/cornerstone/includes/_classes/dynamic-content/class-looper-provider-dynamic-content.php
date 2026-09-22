<?php

class Cornerstone_Looper_Provider_Dynamic_Content extends Cornerstone_Looper_Provider_Generic_Array {

  public function get_array_items( $element ) {
    $out = cs_dynamic_content( $element['looper_provider_dc'], false );

    // Don't allow empty values to be converted to
    // [ empty_value ] by generic array
    if (empty($out)) {
      $out = [];
    }

    return $out;
  }

}
