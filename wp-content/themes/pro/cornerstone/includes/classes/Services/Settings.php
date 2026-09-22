<?php

namespace Themeco\Cornerstone\Services;

class Settings implements Service {

  protected $settings;

  protected $permissions;
  protected $routes;

  public function __construct(
    Routes $routes,
    Permissions $permissions
  ) {
    $this->permissions = $permissions;
    $this->routes = $routes;
  }

  public function setup() {
    $this->routes->add_route('post', 'dashboard-clear-system-cache', [$this, 'ajaxClean']);
    //$this->ajaxClean->setAction( 'dashboard_clear_system_cache' )->setHandler( [ $this, 'ajaxClean'] )->start();

    // API
    add_filter("cs_app_slug", [$this, 'appSlug']);
    add_filter("cs_app_url", [$this, 'appUrl']);
  }

  public function defaults() {
    return [
      'custom_app_slug'  => '',
      'themeless'        => true
    ];
  }

  public function controls() {
    return [
      [
        'key' => 'custom_app_slug',
        'type' => 'text',
        'title'       => __( 'Custom Path', 'cornerstone' ),
        'description' => __( 'Change the path used to load the main interface.', 'cornerstone' ),
        'options' => array(
          'placeholder' => apply_filters( 'cornerstone_default_app_slug', 'cornerstone' )
        ),
      ]
    ];
  }

  public function getAll() {
    if ( ! isset( $this->settings ) ) {
      $this->settings = wp_parse_args( get_option( 'cornerstone_settings', array() ), $this->defaults() );
    }
    return $this->settings;
  }

  public function get( $name ) {
    $all = $this->getAll();
    return isset( $all[$name] ) ? $all[$name] : null;
  }

  public function update($data) {
    $this->getAll();

    if ( isset( $data['custom_app_slug'] ) ) {
      $this->settings['custom_app_slug'] = sanitize_title_with_dashes( $data['custom_app_slug'] );
    }

		if ( isset( $data['themeless'] ) ) {
			$this->settings['themeless'] = is_bool($data['themeless']) ? $data['themeless'] : $data['themeless'] === 'true';
		}


		update_option( 'cornerstone_settings', $this->settings );
  }

  public function appSlug() {

    $customSlug = $this->get('custom_app_slug');
    $slug = apply_filters( 'cornerstone_default_app_slug', 'cornerstone' );

    if ( ! empty( $customSlug ) ) {
      $slug = sanitize_title_with_dashes( $customSlug );
    }

    return $slug;

  }

  public function appUrl() {
    $url = untrailingslashit( home_url( $this->appSlug() ) );

    // See WPML::filterAppURL
    // what it does there might be what we always want it to do
    $url = apply_filters( 'cs_filter_app_url', $url );

    return $url;
  }

  public function ajaxClean() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return wp_send_json_error();
		}

    do_action( 'cs_purge_tmp' );
    do_action( 'cs_purge_all' );

    return [ 'success' => true ];
		//return wp_send_json_success();

	}

}
