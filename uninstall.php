<?php
    // Exit if accessed directly
    if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit;
    }

    // Remove events table
    global $wpdb;
    $table_name = $wpdb->prefix . 'ticketmachine_events';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

?>