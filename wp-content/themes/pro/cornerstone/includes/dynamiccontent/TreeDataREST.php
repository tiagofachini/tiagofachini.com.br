<?php

// @see tree-data dynamic content fields
// This will loop a list of elements till it finds the specified element ID
// Then it will run the filter 'cs_tree_data_element_selector_' . $treeType to grab whatever tree data
// is needed to fill in the dynamic content select
// Register Theme option DC UI
add_action('rest_api_init', function() {

  cornerstone('Routes')->add_route('post', 'dc-tree-data', function($params) {

    $treeType = cs_get_array_value($params, 'tree_type', 'looper');

    $looperFields = [];
    $depths = [];

    // Setup query just for the looper fields
    global $wp_query;
    $wp_query = new WP_Query('post_type=post');

    add_filter( 'cs_looper_main_query', function() {
      return new Cornerstone_Looper_Provider_Archive();
    });

    // Setup global post data
    if ($params['doc_info']['baseType'] === 'content') {
      global $post;
      $post = get_post($params['doc_id']);
      setup_postdata($post);
    }

    // Variable setting up
    $elementID = $params['element_id'];
    $elementIDSearch = '__ELEMENT_ID_FIND__';
    $elements = &$params['elements'];

    // Find element to give it a special label to search for
    $foundElement = &cs_element_find($elements, $elementID);

    if (empty($foundElement)) {
      throw new DomainException('Could not find element from tree : ' . $elementID);
    }

    // We store this so that we can reference in the pre render method
    // makeElementData will create new ids
    $foundElement['_label'] = $elementIDSearch;

    // Setup looper manager defaults
    $LooperManager = CS('Looper_Manager');
    $LooperManager->setup_main_looper_provider();

    $elementData = cornerstone('Resolver')
      ->makeElementData('looper-fields', $elements)
      ->decorated();

    // When our element is being rendered
    // Grab the current data choices
    add_filter("cs_element_pre_render", function($data)
      use (&$looperFields, &$depths, $elementIDSearch, $treeType)
      {
        // Make sure we grab the first item
        static $hasFoundField;

        if (is_null($hasFoundField)) {
          $hasFoundField = false;
        }

        // Ignore tree data for special renders or loops
        if (apply_filters('cs_tree_data_ignore', false)) {
          return $data;
        }

        // Not searching for element
        if ($hasFoundField || empty($data['_label']) || $data['_label'] !== $elementIDSearch) {
          return $data;
        }

        $hasFoundField = true;

        $treeDataFiltered = apply_filters('cs_tree_data_element_selector_' . $treeType, $depths, $data);

        $depths = cs_get_array_value($treeDataFiltered, 'depths', []);
        $looperFields = cs_get_array_value($treeDataFiltered, 'current', []);


        return $data;
      });

    // Render so the pre_render filter happens
    cornerstone('Elements')->directRender([
      '_id' => 'cs0',
      '_type' => 'root',
      '_modules' => $elementData,
    ]);

    // Usage with Archive
    // @TODO determine why the above system wouldn't work
    if (empty($looperFields) && $params['doc_info']['name'] === 'layout:archive') {
      $looperFields = apply_filters('cs_tree_data_element_default_' . $treeType, $depths);
    }

    return [
      'current' => $looperFields,
      'depths' => $depths,
    ];
  });
});
