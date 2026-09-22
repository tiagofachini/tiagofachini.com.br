<?php

/**
 * Export documents to our .tco zip file format
 * Returns file of the zip file
 *
 * @param mixed $ids
 * @param string $type document|template
 * @param string $strategy replace|default?
 *
 * @return string
 */
function cs_export_documents($ids = [], $type = 'document', $strategy = 'original') {
  $ids = is_array($ids)
    ? $ids
    : [ $ids ];

  $replacementStrategy = function() use ($strategy) {
    return $strategy;
  };

  add_filter('cs_export_doc_strategy', $replacementStrategy);

  $zip = cornerstone('Templates')
    ->createExport()
    ->setOption('excludeThumbnails', true)
    ->add( [ $ids ], $type )
    ->organize()
    ->archive();

  remove_filter('cs_export_doc_strategy', $replacementStrategy);

  return $zip;
}

/**
 * Helper to return base64 string of documents export
 *
 * @see cs_export_documents
 */
function cs_export_documents_as_base64($ids = [], $type = 'document', $strategy = '') {
  $zip = cs_export_documents($ids, $type, $strategy);

  return base64_encode(file_get_contents($zip));
}

/**
 * Import TCO file in full
 */
function cs_import_tco($tcoFile) {

  set_time_limit(0);

  $CS = cornerstone();

  /* @var Themeco\Cornerstone\Services\Templates $Templates */
  $Templates = cornerstone('Templates');

  /* @var Themeco\Cornerstone\Controllers\Templates $TemplatesController */
  $TemplatesController = $CS->resolve('Themeco\Cornerstone\Controllers\Templates');

  $zip = new ZipArchive();

  $res = $zip->open($tcoFile);

  if ($res !== true) {
    throw new DomainException('Could not open Zip file : ' . (string)$tcoFile);
  }

  // Grab manifest
  $manifest = $zip->getFromName('manifest.json');

  if (empty($manifest)) {
    throw new DomainException('No manifest file in zip file : ' . (string)$tcoFile);
  }

  $manifest = json_decode($manifest, true);

  $images = [];
  $terms = [];
  $docs = [];
  $lastDoc = null;

  // Terms needs to be imported first
  foreach ($manifest['tasks'] as $task) {
    $taskType = $task[0];
    $taskData = $task[1];

    switch ($taskType) {
    case 'terms':
      $terms = $TemplatesController->importTerms([
        'terms' => $taskData
      ]);
      break;
    }
  }

  // Import other tasks now
  foreach ($manifest['tasks'] as $task) {
    $taskType = $task[0];
    $taskData = $task[1];

    switch ($taskType) {
    case 'options':
      // Global colors import
      if (!empty($taskData['colors'])) {
        cornerstone('GlobalColors')->addColorItems($taskData['colors']);
      }

      // Global fonts import
      if (!empty($taskData['fonts'])) {
        cornerstone('GlobalFonts')->addFontItems($taskData['fonts']);
      }
      break;
    case 'images':
      $images = cs_import_tco_images($taskData, $zip);
      break;
    case 'doc':
      $content = $zip->getFromName($taskData['file']);
      $content = cs_import_replace_content($content, $images, ':full');
      $content = cs_import_replace_content($content, $docs);
      $content = cs_import_replace_content($content, $terms);

      // Template type import
      if ($taskData['type'] === 'template') {
        $items = $TemplatesController->import([
          'saveToLibrary' => true,
          'items' => [ json_decode($content, true) ],
          'overwrite' => $taskData['strategy'] === 'replace',
        ]);

        $lastDoc = $items['items'][0]['id'];
      } else {
        // Standard doc
        $doc = $TemplatesController->importDependency([
          'data' => json_decode($content, true),
          'strategy' => $taskData['strategy'],
        ]);

        $docs[$taskData['key']] = $doc;

        $lastDoc = $doc;
      }

      break;
    }
  }

  return $lastDoc;
}

/**
 * Import images from tco zip
 *
 * @param array $images
 * @param ZipArchive $zip
 *
 * @return array
 */
function cs_import_tco_images($images, $zip) {

  $Templates = cornerstone('Templates');

  $hashes = [];
  $files = [];

  $tempDir = get_temp_dir() . 'cornerstone/images/';
  wp_mkdir_p($tempDir);

  // Loop images and setup temp for import
  foreach ($images as $hash => $imageData) {
    $hashes[] = $hash;

    $imagePath = "img-$hash-{$imageData[1]}";
    $rawData = $zip->getFromName($imagePath);

    $tempFile = $tempDir . $imagePath;

    file_put_contents($tempFile, $rawData);

    $files[$hash] = [
      'name' => $imageData[1],
      'path' => $tempFile,
    ];
  }

  // Import
  $images = $Templates->imageImportHandler($hashes, $files);

  // We expect this to be hash => ID not the format we are given
  foreach ($images as $hash => $imageData) {
    $images[$hash] = $imageData[0];
  }

  // Remove tmp
  foreach ($files as $file) {
    unlink($file['path']);
  }

  return $images;
}

/**
 * Replace contents from _cs-tmpl:$hash:cs-tmpl_ strategy
 */
function cs_import_replace_content($contents, $hashToId = [], $addition = '') {
  foreach ($hashToId as $hash => $id) {
    $valueReplacement = $id . $addition;

    $contents = str_replace("_cs-tmpl:{$hash}:cs-tmpl_", (string)$valueReplacement, $contents);
  }

  return $contents;
}
