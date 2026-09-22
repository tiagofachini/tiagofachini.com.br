<?php

class Cornerstone_Looper_Provider_Terms extends Cornerstone_Looper_Provider_Array {

  public function get_array_items( $element ) {
    $terms = get_the_terms( get_the_ID(), $element['looper_provider_terms_tax']);

    // This can return false which will be converted into [ false ] without this
    if ($terms === false) {
      return [];
    }

    return $terms;
  }

}
