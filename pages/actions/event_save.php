<?php 
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    global $tm_globals, $api, $wpdb;

	if (isset($_POST['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $errors = array();

        if ( ! isset( $_POST['ticketmachine_communityevents_event_create_form_nonce'] ) || ! wp_verify_nonce( $_POST['ticketmachine_communityevents_event_create_form_nonce'], 'ticketmachine_communityevents_action_save_event' ) ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        }else{
            unset($_POST['ticketmachine_communityevents_event_create_form_nonce']);
            unset($_POST['_wp_http_referer']);
            $tm_post = $_POST;

            if($tm_post["captcha_check"] && $tm_post["captcha_check"] != $tm_post["captcha_submit"]) {
                $errors[] = __("Captcha failed.", "ticketmachine-community-events");
            }
    
            if(!isset($tm_post['approved'])) {
                $tm_post['approved'] = 0;
            }
            if(isset($tm_post['tags'])) {
                $tm_post['tags'] = explode(",", $tm_post['tags']);
                array_walk($tm_post['tags'], function(&$value, &$key) {
                    $value = sanitize_text_field($value);
                });
            }
            if(isset($tm_post['entrytime'])) {
                $tm_post['entrytime'] = sanitize_text_field(ticketmachine_i18n_reverse_date($tm_post['entrytime']['date'] . $tm_post['entrytime']['time']));
            }else{
                $errors[] = __("No entry time was set", "ticketmachine-community-events");
            }
            if(isset($tm_post['ev_date'])) {
                $tm_post['ev_date'] = sanitize_text_field(ticketmachine_i18n_reverse_date($tm_post['ev_date']['date'] . $tm_post['ev_date']['time']));
            }else{
                $errors[] = __("No start time was set", "ticketmachine-community-events");
            }
            if(isset($tm_post['endtime'])) {
                $tm_post['endtime'] = sanitize_text_field(ticketmachine_i18n_reverse_date($tm_post['endtime']['date'] . $tm_post['endtime']['time']));
            }else{
                $errors[] = __("No end time was set", "ticketmachine-community-events");
            }
    
            if(!empty($tm_post['ev_name'])) {
                $tm_post['ev_name'] = sanitize_text_field($tm_post['ev_name']);
            }else{
                $errors[] = __("No event title was set", "ticketmachine-community-events");
            }
    
            if(isset($tm_post['description'])) {
                $tm_post['description'] = sanitize_text_field(strip_shortcodes($tm_post['description']));
            }
    
            if(!empty($tm_post['id'])) {
                $tm_post['id'] = absint($tm_post['id']);
            }
            
            $tm_post['vat_id'] = 1;
    
            if(empty($tm_globals->organizer_id) || !is_int($tm_globals->organizer_id)){
                $errors[] = __("No organizer id could be found", "ticketmachine-community-events");
            }
    
            $tm_post['organizer_id'] = absint($tm_globals->organizer_id);
            $tm_post['approved'] = absint($tm_post['approved']);
            $tm_post['vat_id'] = absint($tm_post['vat_id']);
    
            if(empty($tm_post['organizer']['og_name'])) {
                unset($tm_post['organizer']);
            }
            
            if(empty($errors)){
                unset($tm_post["captcha_check"]);
                unset($tm_post["captcha_submit"]);
    
                if(!empty($tm_post['event_location'])) {
                    $tm_event_location = $tm_post['event_location'];
                    unset($tm_post['event_location']);
                    $tm_post['street'] = $tm_event_location['street'];
                    $tm_post['house_number'] = $tm_event_location['house_number'];
                    $tm_post['zip'] = $tm_event_location['zip'];
                    $tm_post['city'] = $tm_event_location['city'];
                    $tm_post['country'] = $tm_event_location['country'];
                }
    
                if(!empty($tm_post['organizer'])){
                    $organizer_temp = $tm_post['organizer'];
                    unset($tm_post['organizer']);
                }
    
                $tm_post_array = $tm_post;
                unset($tm_post_array['submit']);
    
                $sql = $wpdb->insert(
                    $wpdb->prefix . "ticketmachine_events",
                    $tm_post_array
                );
                $event_id = $wpdb->insert_id;
                
                if(!empty($organizer_temp)) {
                    $table = $wpdb->prefix . 'ticketmachine_organizers';
                    $organizer_check = $wpdb->get_row( "SELECT * FROM $table WHERE og_name = '" . $organizer_temp['og_name'] . "'");
                    if(!empty($organizer_check)){
                        $wpdb->update($table, $organizer_temp, array('id' => $organizer_check->id));
                        $table = $wpdb->prefix . 'ticketmachine_organizers_events_match';
                        $wpdb->insert($table, array('organizer_id' => $organizer_check->id, 'local_event_id' => $event_id));
                    }else{
                        $wpdb->insert($table, $organizer_temp);
                        $organizer_id = $wpdb->insert_id;
                        $table = $wpdb->prefix . 'ticketmachine_organizers_events_match';
                        $wpdb->insert($table, array('organizer_id' => $organizer_id, 'local_event_id' => $event_id));
                    }
                }
    
                $messages = array(
                    "type" => "success",
                    "message" => __("Event submitted for review!", "ticketmachine-community-events")
                );
    
                $to = get_option('admin_email');
                $subject = 'Someone submitted an event';
                $message = 'Test';
                wp_mail($to, $subject, $message );
                unset($_POST);
                $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                header("Location: $actual_link" . "?success=1");
    
            }else{
                $messages = array(
                    "type" => "error",
                    "message" => $errors
                );
    
                $event = $tm_post;
            }
        }
    } 
?>