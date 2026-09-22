<?php

namespace Themeco\Cornerstone\Services;

class VersionMigration implements Service {

  public function setup() {
    add_action( 'init', [ $this, 'versionMigration' ], -1000 );
  }

  public function versionMigration() {
    $prior = cs_get_option( 'cornerstone_version', CS_VERSION );

    if ( version_compare( $prior, CS_VERSION, '>=' ) ) {
      return;
    }

    $this->update( $prior );
    do_action( 'cornerstone_updated', $prior );
    do_action( 'cs_purge_tmp' );
    do_action( 'cs_purge_cache' );

    update_option( 'cornerstone_version', CS_VERSION, true );

  }

  // Upgrade / update method
  public function update( $prior ) {
    // 7.3.0 to 7.4.0 / 6.3.0 to 6.4.0
    if ( version_compare( $prior, '7.4.0-beta1', '<' ) ) {
      FontAwesome::migrateFromBeforeVersion64();
    }

    // template edit migration
    if ( version_compare( $prior, '7.5.8', '<' ) ) {
      cornerstone('Permissions')->migrateTemplateEditPermission();
    }

    // Migration for standalone shortcode mode setting
    if ( version_compare( $prior, '7.6.0-beta1', '<' ) ) {
      update_option('cs_document_build_as_html', true);
      update_option('cs_document_build_as_html', false);
    }

    // 7.8.0
    if ( version_compare( $prior, '7.8.0-beta1', '<' )) {
      update_option('cs_wpml_advanced_builder', true);
    }
  }

}
