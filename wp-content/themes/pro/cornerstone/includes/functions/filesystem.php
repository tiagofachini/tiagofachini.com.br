<?php

/**
 * Get recursive list of files in a directory
 * No trailing slash
 *
 * @param string $dir
 *
 * @return array
 */
function cs_directory_get_file_list($dir) {
  // Directory does not exist
  if (!file_exists($dir)) {
    return [];
  }

  $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
  $files = [];

  /** @var SplFileInfo $file */
  foreach ($rii as $file) {
    if ($file->isDir()){
      continue;
    }

    // Only path
    $files[] = str_replace($dir . '/', '', $file->getPathname());
  }

  return $files;
}

/**
 * Add a file usually from temp dir to the media manager
 *
 * @param string $name
 * @param string $tmp_file
 * @param int|null $postId
 *
 * @return int|WP_Error
 */
function cs_media_manager_add($name, $tmp_file, $postId = null) {
  // Needed for various file functions
  require_once(ABSPATH . 'wp-admin/includes/media.php');
  require_once(ABSPATH . 'wp-admin/includes/image.php');
  require_once ABSPATH . 'wp-admin/includes/file.php';

  // Send tmp file to upload dir for later attachment creation
  $sideLoadFile = [
    'name' => $name,
    'tmp_name' => $tmp_file,
  ];

  $results = wp_handle_sideload($sideLoadFile, [
    'test_form' => false
  ]);

  if ( !empty( $results['error'] ) ) {
    throw new DomainException('Failed to sideload image: ' . $name . ': '. $results['error']);
  }

  // Insert to attachment table
  $insertArgs = [
    'post_title' => pathinfo($name, PATHINFO_FILENAME),
    'post_content' => '',
    'post_mime_type' => $results['type'],
    'guid' => $results[ 'url' ]
  ];

  // Create new post
  if (!empty($postId)) {
    $insertArgs['ID'] = $postId;
  }

  // Create new attachment or update
  $postId = wp_insert_attachment($insertArgs, $results['file'] );

  if (!is_wp_error($postId)) {
    wp_generate_attachment_metadata($postId, $results['file']);
  }

  return $postId;
}

function cs_media_manager_add_base64($name, $base64) {
  $dirName = get_temp_dir() . 'cornerstone/attachment/';
  wp_mkdir_p($dirName);

  $tmp_file = $dirName . $name;

  file_put_contents($tmp_file, base64_decode($base64));

  $attachmentID = cs_media_manager_add($name, $tmp_file);

  unlink($tmp_file);

  return $attachmentID;
}
