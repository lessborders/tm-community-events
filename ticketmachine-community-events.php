<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	/*
	Plugin Name:        TicketMachine Community Events
    Plugin URI:         https://www.ticketmachine.de/
	Description:        Allow regular users to create and edit their events without using the wordpress backend.
	Version:            1.0.0
    Requires at least:  4.5
    Author:             NET-UP AG
	Author URI:         https://www.net-up.de
	Text Domain: 		ticketmachine-community-events
	Domain Path: 		/languages
	*/

	add_action( 'init', 'ticketmachine_communityevents_check_some_other_plugin' );

	function ticketmachine_communityevents_check_some_other_plugin() {
	}

    register_activation_hook(__FILE__, 'ticketmachine_communityevents_activate');
    register_deactivation_hook(__FILE__, 'ticketmachine_communityevents_deactivate');

	function ticketmachine_communityevents_activate( ) {
        global $wpdb;
        global $jal_db_version;

        //create events overview page
        $new_page_title = 'Create Event';
        $new_page_slug = 'create-event';
        $new_page_content = '[ticketmachine_communityevents page="create_event"]';
        $new_page_template = '';
    
        $page_check = get_page_by_path($new_page_slug);
        $new_page = array(
            'post_type' => 'page',
            'post_title' => $new_page_title,
            'post_name' => $new_page_slug,
            'post_content' => $new_page_content,
            'post_status' => 'publish',
            'post_author' => 1,
        );
        if(!isset($page_check->ID)){
            $new_page_id = wp_insert_post($new_page);
            if(!empty($new_page_template)){
                update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
            }
        }
        $events_slug = get_page_by_path($new_page_slug);

        $table = $wpdb->prefix . 'ticketmachine_events';
        $charset = $wpdb->get_charset_collate();
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    api_event_id int(11) DEFAULT 0 NOT NULL,
                    approved tinyint(1) DEFAULT 0 NOT NULL,
                    ev_name varchar(128) DEFAULT '' NOT NULL,
                    ev_description text DEFAULT '' NOT NULL,
                    event_image varchar(256) DEFAULT '' NOT NULL,
                    tags varchar(256) DEFAULT '' NOT NULL,
                    ev_date varchar(256) DEFAULT '' NOT NULL,
                    entrytime varchar(256) DEFAULT '' NOT NULL,
                    endtime varchar(256) DEFAULT '' NOT NULL,
                    ev_location_name varchar(256) DEFAULT '' NOT NULL,
                    street varchar(256) DEFAULT '' NOT NULL,
                    house_number varchar(256) DEFAULT '' NOT NULL,
                    zip varchar(256) DEFAULT '' NOT NULL,
                    city varchar(256) DEFAULT '' NOT NULL,
                    country varchar(256) DEFAULT '' NOT NULL,
                    organizer_id int(11) DEFAULT 0 NOT NULL,
                    vat_id int(11) DEFAULT 0 NOT NULL,
                    PRIMARY KEY  (id)
                ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        add_option('jal_db_version', $jal_db_version);
    }

    function ticketmachine_communityevents_deactivate( ) {
	}
	
	// load dynamic form for calculator from template
	function ticketmachine_communityevents_initialize( $atts ) {
		if(!session_id())
            session_start(); 

        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        if(is_plugin_active( 'ticketmachine-event-manager/ticketmachine-event-manager.php' )){
                    
            global $tm_globals, $api, $wpdb;
        
            wp_enqueue_script( 'jquery-ui-datepicker', array("jquery") );
            //Cookies
            wp_enqueue_script( 'cookies_JS' );
            //Popper
            wp_enqueue_script( 'popper_JS' );
            //Bootstrap
            wp_enqueue_script( 'bootstrap-4_JS' );
            wp_enqueue_style( 'boostrap-4_CSS' );
            //Core
            wp_enqueue_style( 'core_CSS' );
            wp_enqueue_script( 'core_JS' );
            //FileSaver
            wp_enqueue_script( 'fileSaver_JS' );
            wp_enqueue_script( 'tm_captcha_JS', plugins_url('assets/js/captcha.js', __FILE__ ) );;
            
            wp_enqueue_style( 'datetimepicker_CSS', plugins_url('assets/css/ext/bootstrap-datetimepicker.css', __FILE__ ) );
            wp_enqueue_style( 'fontawesome-5_CSS', plugins_url('assets/css/ext/fontawesome.min.css', __FILE__ ) );
            wp_enqueue_style( 'jquery-ui_CSS', plugins_url('assets/css/ext/jquery-ui.css', __FILE__ ) );

            wp_enqueue_script( 'moment_JS', plugins_url('assets/js/ext/moment-with-locales.js', __FILE__ ) );
            wp_enqueue_script( 'datetimepicker_JS', plugins_url('assets/js/ext/bootstrap-datetimepicker.min.js', __FILE__ ) );
                    
            
            if( $atts ) {
                
                foreach($_GET as $key => $value) {
                    $atts[$key] = $value;
                }
                
                $ticketmachine_communityevents_output = "<div class='ticketmachine_page' data-locale=" . esc_html($tm_globals->locale_short) . ">";
                
                if(isset($atts['page'])){
                    switch ($atts['page']) {
                        case 'create_event':
                            include "pages/create_event.php";
                            $ticketmachine_communityevents_output .= ticketmachine_communityevents_create_event( $atts );
                            break;
                    }
                }

                $ticketmachine_communityevents_output .= "</div>";
                
                $ticketmachine_communityevents_output = shortcode_unautop($ticketmachine_communityevents_output);
                return $ticketmachine_communityevents_output;
            }
		}else{
            return "<a target='_blank' href='https://wordpress.org/plugins/ticketmachine-event-manager/'>TicketMachine Event Manager & Calendar</a> plugin is not installed.";
        }
    }
    add_shortcode( 'ticketmachine_communityevents', 'ticketmachine_communityevents_initialize' );

    if(is_admin()){
        include_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php');
    }