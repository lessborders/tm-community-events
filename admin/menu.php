<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	function ticketmachine_communityevents_admin_menu () {
		global $tm_globals, $api;

		include( plugin_dir_path( __FILE__ ) . 'pages/events.php');
		
		if( current_user_can('edit_posts') ) {

			if(!empty($tm_globals->activated) || isset($_GET['code'])) {

				add_submenu_page(
					'ticketmachine_event_manager',
					esc_html__('Community Events', 'ticketmachine-event-manager'),
					esc_html__('Community Events', 'ticketmachine-event-manager'),
					'manage_options',
					'ticketmachine_communityevents_events',
					'ticketmachine_communityevents_render_list_page',
					null,
					100
				);

			}
		}
	}

	add_filter( 'submenu_file', function($submenu_file){
		$screen = get_current_screen();
		if($screen->id === 'ticketmachine_event_manager'){
			$submenu_file = 'ticketmachine_event_manager';
		}
		return $submenu_file;
	});
	
	add_action('admin_menu', 'ticketmachine_communityevents_admin_menu', 11);
		
?>