<?php

namespace Themeco\Cornerstone\Services;

use DomainException;
use Themeco\Cornerstone\Util\Factory;
use Themeco\Cornerstone\Templating\Template;
use Themeco\Cornerstone\Templating\Export;
use Themeco\Cornerstone\Documents\Document;
use Themeco\Cornerstone\Documents\DocumentCache;
use Themeco\Cornerstone\Util\Endpoint;
use Themeco\Cornerstone\Util\Filesystem;

class Templates implements Service {

  protected $permissions;
  protected $imageImportEndpoint;
  protected $exportEndpoint;
  protected $exportAllEndpoint;
  protected $filesystem;
  protected $legacySiteImportEndpoint;
  protected $codebaseBridge;

  private $routes;

  public function __construct(
    Permissions $permissions,
    Endpoint $imageImportEndpoint,
    Endpoint $exportEndpoint,
    Endpoint $legacySiteImportEndpoint,
    Endpoint $exportAllEndpoint,
    Filesystem $filesystem,
    CodebaseBridge $codebaseBridge,
    Routes $routes
  ) {
    $this->permissions = $permissions;
    $this->imageImportEndpoint = $imageImportEndpoint;
    $this->exportEndpoint = $exportEndpoint;
    $this->exportAllEndpoint = $exportAllEndpoint;
    $this->filesystem = $filesystem;
    $this->legacySiteImportEndpoint = $legacySiteImportEndpoint;
    $this->codebaseBridge = $codebaseBridge;
    $this->routes = $routes;
  }

  public function setup() {

    $this->exportEndpoint->config( [
      'requestKey' => 'cs-export',
      'handler'    => [ $this, 'exportHandler' ]
    ])->start();

    $this->legacySiteImportEndpoint->config( [
      'requestKey' => 'cs-legacy-site-import',
      'handler'    => [ $this->codebaseBridge->legacyPlugin()->component('Controller_Design_Cloud'), 'import_site' ]
    ])->start();


    $this->imageImportEndpoint->config( [
      'requestKey' => 'cs-upload-image',
      'handler'    => [ $this, 'imageImportHandler' ]
    ])->start();

    // Export All templates
    $this->exportAllEndpoint->config( [
      'requestKey' => 'cs-export-all',
      'handler'    => [ $this, 'exportAllHandler' ]
    ])->start();

    $this->routes->add_route('post', 'export-document', [$this, 'exportDocument']);

    add_action( 'init', function() {
      register_post_type( 'cs_template', array(
        'public'              => false,
        'exclude_from_search' => false,
        'capability_type'     => 'page',
        'supports'            => false,
        'labels' => [
          'name' => __('Templates', 'cornerstone'),
          'singular_name' => __('Template', 'cornerstone'),
        ]
      ) );

      // Classic Cornerstone templates
      register_post_type( 'cs_user_templates', array(
        'public'          => false,
        'capability_type' => 'page',
        'supports'        => false
      ));
    });
  }

  public function query( $data = []) {

    $args = [
      'post_type' => array( 'cs_template', 'cs_user_templates' ), // include old type so it can be accounted for in migration
      'post_status' => array( 'tco-data', 'publish' ),
      'orderby' => 'title',
      'order' => 'ASC',
      'posts_per_page' => apply_filters( 'cs_query_limit', 2500 ),
      'cs_all_wpml' => true
    ];

    if ( isset( $data['type'] ) ) {
      $args['meta_key'] = '_cs_template_identifier';
      $args['meta_value'] = $data['type'];
    }

    $where_filter = null;

    if (isset($data['search'])) {

      $args['suppress_filters'] = false;

      $where_filter = function ($where) use ($data){
        // Search by title and post type.
        global $wpdb;
        $search = '%' .$data['search']. '%';
        $where .= $wpdb->prepare(" AND ({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_content LIKE %s)", $search,$search);
        return $where;
      };

      add_filter('posts_where', $where_filter);

    }

    if ( isset( $data['offset'] ) ) {
      $args['offset'] = $data['offset'];
    }

    $posts = get_posts( $args );

    if ( $where_filter ) {
      remove_action('posts_where', $where_filter);
    }

    return array_values(array_filter(array_map(function( $post ){
      try {
        $template = Template::locate( $post );
        if ($template) {
          return $template->serialize();
        }
        // Template::locate accounts for user permissions, and also active plugins like WC
        // it's possible to have a post in the DB but not allow it as a template
        return null;
      } catch (\Exception $e) {

        trigger_error("Unable to read template data " . $e->getMessage() );
      }

    },$posts)));

  }

  public function createExport() {
    return Factory::create(Export::class);
  }

  public function imageImportHandler( $request, $files = [] ) {
    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    // This can take a long time due to image magick processing
    set_time_limit(1200);

    $list = is_array($request)
      ? $request
      : explode(',', $request->get_param('cs_media_upload_files'));

    $result = [];

    foreach ($list as $hash) {

      try {

        global $wpdb;
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_cs_attachment_import' AND meta_value = %s", $hash ) );

        if ( $results ) {
          $post_id = $results[0]->post_id;
        } else {

          //Fix media import issue due to other 3rd party plugins that uses wp_handle_upload_prefilter
          global $wp_filter;
          unset($wp_filter['wp_handle_upload_prefilter']);

          require_once( ABSPATH . 'wp-admin/includes/image.php' );
          require_once( ABSPATH . 'wp-admin/includes/file.php' );
          require_once( ABSPATH . 'wp-admin/includes/media.php' );

          $post_id = null;
          if (empty($files[$hash])) {
            $post_id = media_handle_upload( 'cs_media_upload_' . $hash, 0, [], [ 'action' => 'wp_handle_upload'] );
          } else {
            $post_id = $this->addLocalToMediaManager(
              $files[$hash]['name'],
              $files[$hash]['path']
            );
          }

          if ( is_wp_error( $post_id ) ) {
            throw new \Exception( $post_id->get_error_message() . ' ' . $hash);
            throw new \Exception( $post_id->get_error_message() );
          }

          update_post_meta( $post_id, '_cs_attachment_import', $hash );

        }

        $result[$hash] = [$post_id,wp_get_attachment_url( $post_id )];

      } catch( \Exception $e) {
        $result[$hash] = [ 'error' => $e->getMessage() ];
      }

    }

    return $result;
  }

  // Export .tco file (zip) and output it
  // via createExport
  public function exportHandler($data) {
    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $data['ids'] ) ) {
      throw new \Exception('ids not specified');
    }

    $excludeThumbnails = apply_filters('cs_template_export_exclude_thumbnails', false);

    $zip = $this->createExport()
      ->setOption('excludeThumbnails', $excludeThumbnails)
      ->add( $data['ids'] )
      ->organize()
      ->archive();

    if (is_wp_error($zip)) {
      throw new \DomainException($zip->get_error_message());
    }

    $this->filesystem->sendFile($zip);

  }

  /**
   * Export individual document
   *
   * Uses 'document' group so it adds a new document on another site
   */
  public function exportDocument($params) {
    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new DomainException( 'Unauthorized' );
    }

    if (empty($params['id'])) {
      throw new DomainException('Valid id not sent to document export');
    }

    $zip = $this->createExport()
      ->setOption('excludeThumbnails', true)
      ->add( [ $params['id'] ], 'document' )
      ->organize()
      ->archive();

    if (is_wp_error($zip)) {
      throw new \DomainException($zip->get_error_message());
    }

    return [
      'tco' => base64_encode(file_get_contents($zip))
    ];
  }

  /**
   * Export site handler
   */
  public function exportAllHandler($data) {
    if (!$this->permissions->userCan('global.export_documents')) {
      throw new \Exception( 'Unauthorized' );
    }

    // Get all post types in our document system
    $allowed = cornerstone('Resolver')->getAllowedPostTypes();

    // Remove global block otherwise it will add twice
    // through DependencyMapper
    $key = array_search('cs_global_block', $allowed);
    if($key !== false){
      unset($allowed[$key]);
    }

    //$allowed[] = 'cs_template';

    $tcoPosts = get_posts([
      'posts_per_page' => -1,
      'post_type' => $allowed,
      'post_status' => ['any', 'tco-data'],
      'fields' => 'ids',
    ]);

    // testing
    //$tcoPosts = [80];
    //$tcoPosts = [103];

    $export = $this->createExport();
    $export->setOption('fullSite', true);

    // Export doc
    add_filter('cs_export_doc_strategy', function() {
      return 'replace';
    });

    // Add each document to export
    foreach ($tcoPosts as $id) {
      $export->add($id, 'document');
    }

    $export->addMenus();

    // Setup zip file
    $export = $export->organize();
    $zip = $export->archive();

    // Bad zip generation
    if (is_wp_error($zip)) {
      throw new \DomainException($zip->get_error_message());
    }

    // Send file
    // exits after this
    $this->filesystem->sendFile($zip);

  }

  public function addLocalToMediaManager($fileName, $tmp_file, $postId = null) {
    // Required for generate attachment metadata
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // Send tmp file to upload dir for later attachment creation
    $sideLoadFile = [
      'name' => $fileName,
      'tmp_name' => $tmp_file,
    ];

    $results = wp_handle_sideload($sideLoadFile, [
      'test_form' => false
    ]);

    if ( !empty( $results['error'] ) ) {
      throw new DomainException('Failed to sideload image: ' . $name);
    }

    // Insert to attachment table
    $insertArgs = [
      'post_title' => pathinfo($fileName, PATHINFO_FILENAME),
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

    // Generate metadata
    wp_generate_attachment_metadata($postId, $results['file']);

    return $postId;
  }

}
