<?php

namespace Themeco\Theme\Services;

use Themeco\Theme\Theme;
use Themeco\Theme\Util\VersionedUrl;

class Enqueue {
  protected $theme;
  protected $versionedUrl;

  public function __construct(Theme $theme, VersionedUrl $versionedUrl) {
    $this->theme = $theme;
    $this->versionedUrl = $versionedUrl;
  }

  public function setup() {
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
    $this->versionedUrl->configure( $this->theme->path, $this->theme->url );
  }

  public function enqueue() {
    $customStack = x_is_custom_stack();

    // x-theme stylesheet
    if (!$customStack) {
      // @TODO I don't think this is actually used, see frontend/styles.php
      $file = $this->versionedUrl->get('framework/dist/theme','css');
      wp_enqueue_style( 'x-theme', $file['url'], NULL, $file['version'], 'all' );
    }

    if ( is_child_theme() && apply_filters( 'x_enqueue_parent_stylesheet', false ) ) {
      $deps = $customStack
        ? ['cs']
        : ['x-stack'];

      $rev = ( defined( 'X_CHILD_ASSET_REV' ) ) ? X_CHILD_ASSET_REV : X_ASSET_REV;

      wp_enqueue_style( 'x-child', get_stylesheet_directory_uri() . '/style.css', $deps, $rev );
    }
  }

}
