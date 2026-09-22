<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// registers the block using the metadata from the `block.json` file
function vscf_register_block() {
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'vscf_server_side_render_block',
		)
	);
}
add_action( 'init', 'vscf_register_block' );

// enqueue styles for block editor
function vscf_enqueue_editor_styles() {
	if ( is_admin() ) {
		wp_enqueue_style( 'vscf-editor-styles', plugins_url( '/css/vscf-style.min.css', __DIR__ ) );
		wp_enqueue_style( 'vscf-editor-form-styles', plugins_url( '/css/vscf-block-style.min.css', __DIR__ ) );
	}
}
add_action( 'enqueue_block_assets', 'vscf_enqueue_editor_styles' );

// renders the block on server
function vscf_server_side_render_block( $attr ) {
	$content = '';
	$shortcode_settings = isset( $attr['shortcodeSettings'] ) ? $attr['shortcodeSettings'] : '';
	$shortcode_settings = str_replace( array( '[', ']' ), '', $shortcode_settings );
	$content .= do_shortcode( '[contact ' . wp_strip_all_tags( $shortcode_settings, true ) . ']' );
	return $content;
}
