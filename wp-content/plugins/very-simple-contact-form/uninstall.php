<?php
// exit if uninstall is not called
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$keep = get_option( 'vscf-setting-1' );
if ( $keep != 'yes' ) {
	// set global
	global $wpdb;

	// delete options
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'vscf-setting%'" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name = 'widget_vscf_widget'" );

	// delete submissions
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'submission'" );
}
