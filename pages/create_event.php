<?php
    function ticketmachine_communityevents_create_event($atts){
			
        global $tm_globals, $api, $wpdb;

        //defaults
        $timestamp = new DateTime();
        $event = array(
            "state" => array(
                "shown" => 1
            ),
            "approved" => 0,
            "ev_name" => "", 
            "ev_description" => "",
            "event_img_url" => str_replace("/admin/pages", "", plugin_dir_url(__FILE__)) . 'assets/img/none.png',
            
            "ev_location_name" => "",
            "event_location" => array(
                "street" => "",
                "house_number" => "",
                "zip" => "",
                "city" => "",
                "country" => ""
            ),
    
            "rules" => array(
                "badge" => "",
                "sale_active" => 0,
                "prices_shown" => 0
            ),
    
            "tags" => array(),
    
            "entrytime" => date(DATE_ISO8601, strtotime("today 10:00")),
            "ev_date" =>  date(DATE_ISO8601, strtotime("today 11:00")),
            "endtime" =>  date(DATE_ISO8601, strtotime("today 23:59"))
        );

        $organizer = array(
            "og_name" => "",
            "og_street" => "",
            "og_house_number" => "",
            "og_zip" => "",
            "og_city" => "",
            "og_country" => "",
            "og_email" => "",
            "og_phone" => ""
        );


        if (isset($_POST['submit'])) {
            include "actions/event_save.php";
        }

        $event = (object)$event;
        $organizer = (object)$organizer;

        $ticketmachine_communityevents_output = "";

        if(!empty($_GET['success']) && $_GET['success'] == 1) {
            $messages = array(
                "type" => "success",
                "message" => "Event submitted for review!"
            );
        }

        if(!empty($messages)) {
            if($messages['type'] == "success"){
                $ticketmachine_communityevents_output .= '<div class="alert alert-success">' . $messages['message'] . '</div>';
            }else{
                foreach($messages['message'] as $message){
                    $ticketmachine_communityevents_output .= '<div class="alert alert-danger">' . $message . '</div>';
                }
            }
        }
    
        $event = (object)$event;

        $ticketmachine_communityevents_output .= '<form name="event" action="" method="post" id="event">';
            $ticketmachine_communityevents_output .= wp_nonce_field( 'ticketmachine_communityevents_action_save_event', 'ticketmachine_communityevents_event_create_form_nonce' );

            $ticketmachine_communityevents_output .= '<div class="row">
                                                        <div class="col-md-6">';


            $ticketmachine_communityevents_output .= '<div class="card mt-3">
                                                        <h3 class="hndle px-3 py-2 mt-0">
                                                            <span>' . esc_html__('Event Details', 'ticketmachine-event-manager') . '</span>
                                                        </h3>
                                                        <div class="card-body">';

            $ticketmachine_communityevents_output .= '<label>' . esc_html__('Event Name', 'ticketmachine-event-manager') . ' *</label>
                                                        <input class="form-control"  name="ev_name" type="text" value="' . esc_attr($event->ev_name) . '" required/>';

            $ticketmachine_communityevents_output .= '<label>' . esc_html__('Event Description', 'ticketmachine-event-manager') . '</label>
                                                        <textarea class="form-control" name="ev_description">' . esc_attr($event->ev_description) . '</textarea>';

            //$ticketmachine_communityevents_output .= '<label>' . esc_html__('Event Image', 'ticketmachine-event-manager') . '</label>
            //                                            <div class="custom-file">
            //                                                <input type="file" class="custom-file-input" id="customFile">
            //                                                <label class="custom-file-label" for="customFile">Choose file</label>
            //                                            </div>';

            $ticketmachine_communityevents_output .= '</div>
                                                    </div>';

            $ticketmachine_communityevents_output .= '<div class="card mt-3">
                                                        <h3 class="hndle px-3 py-2 mt-0">
                                                            <span>' . esc_html__('Location', 'ticketmachine-event-manager') . '</span>
                                                        </h3>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-12 form-group">
                                                                    <label for="event_edit_locationname">' . esc_html__('Event Location', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="event_location_name" name="ev_location_name" type="text" class="form-control" value="' . esc_attr($event->ev_location_name) . '">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-8 form-group">
                                                                    <label for="event_edit_strasse">' . esc_html__('Street', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="event_edit_strasse" name="event_location[street]" type="text" class="form-control" value="' . esc_attr($event->event_location['street']) . '">
                                                                </div>
                                                                <div class="col-sm-4 form-group">
                                                                    <label for="house_number">' . esc_html__('House No.', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="event_edit_hausnr" name="event_location[house_number]" type="text" class="form-control" value="' . esc_attr($event->event_location['house_number']) . '">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-4 form-group">
                                                                    <label for="event_edit_plz">' . esc_html__('Zipcode', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="event_edit_plz" name="event_location[zip]" type="text" class="form-control" value="' . esc_attr($event->event_location['zip']) . '">
                                                                </div>
                                                                <div class="col-sm-8 form-group">
                                                                    <label for="event_edit_ort">' . esc_html__('City', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="event_edit_ort" name="event_location[city]" type="text" class="form-control" value="' . esc_attr($event->event_location['city']) . '">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 form-group">
                                                                    <label for="event_edit_land">' . esc_html__('Country', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="event_edit_land" name="event_location[country]" type="text" class="form-control" value="' . esc_attr($event->event_location['country']) . '">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';

            $ticketmachine_communityevents_output .= '</div><div class="col-md-6">';     

            $ticketmachine_communityevents_output .= '<div class="card mt-3">
                                                        <h3 class="hndle px-3 py-2 mt-0">
                                                            <span>' . esc_html__('Dates & Times', 'ticketmachine-event-manager') . '</span>
                                                        </h3>
                                                        <div class="card-body">
                                                            <div>
                                                                <label>' . esc_html__('Entry Time', 'ticketmachine-event-manager') . ' *</label>
                                                            </div>
                                                            <div class="row">
                                                                <div class="input-group col-8">
                                                                    <input type="text" name="entrytime[date]" class="form-control date entrytime" value="' . esc_attr(ticketmachine_i18n_date("d.m.Y", $event->entrytime)) . '">
                                                                </div>
                                                                <div class="input-group col-4">
                                                                    <input type="text" name="entrytime[time]" class="form-control time" value="' . esc_attr(ticketmachine_i18n_date("H:i", $event->entrytime)) . '">
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label>' . esc_html__('Event begins at', 'ticketmachine-event-manager') . ' *</label>
                                                            </div>
                                                            <div class="row">
                                                                <div class="input-group col-8">
                                                                    <input type="text" name="ev_date[date]" class="form-control date starttime" value="' . esc_attr(ticketmachine_i18n_date("d.m.Y", $event->ev_date)) . '">
                                                                </div>
                                                                <div class="input-group col-4">
                                                                    <input type="text" name="ev_date[time]" class="form-control time" value="' . esc_attr(ticketmachine_i18n_date("H:i", $event->ev_date)) . '">
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label>' . esc_html__('Event ends at', 'ticketmachine-event-manager') . ' *</label>
                                                            </div>
                                                            <div class="row">
                                                                <div class="input-group col-8">
                                                                    <input type="text" name="endtime[date]" class="form-control date endtime" value="' . esc_attr(ticketmachine_i18n_date("d.m.Y", $event->endtime)) . '">
                                                                </div>
                                                                <div class="input-group col-4">
                                                                    <input type="text" name="endtime[time]" class="form-control time" value="' . esc_attr(ticketmachine_i18n_date("H:i", $event->endtime)) . '">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';                                 

            $ticketmachine_communityevents_output .= '<div class="card mt-3">
                                                        <h3 class="hndle px-3 py-2 mt-0">
                                                            <span>' . esc_html__('Organizer Details', 'ticketmachine-event-manager') . '</span>
                                                        </h3>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-12 form-group">
                                                                    <label for="og_name">' . esc_html__('Organizer Name', 'ticketmachine-event-manager') . ' *</label>
                                                                    <input id="og_name" name="organizer[og_name]" type="text" class="form-control" value="' . esc_attr($organizer->og_name) . '" required>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-8 form-group">
                                                                    <label for="og_street">' . esc_html__('Street', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="og_street" name="organizer[og_street]" type="text" class="form-control" value="' . esc_attr($organizer->og_street) . '">
                                                                </div>
                                                                <div class="col-sm-4 form-group">
                                                                    <label for="og_house_number">' . esc_html__('House No.', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="og_house_number" name="organizer[og_house_number]" type="text" class="form-control" value="' . esc_attr($organizer->og_house_number) . '">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-4 form-group">
                                                                    <label for="og_zip">' . esc_html__('Zipcode', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="og_zip" name="organizer[og_zip]" type="text" class="form-control" value="' . esc_attr($organizer->og_zip) . '">
                                                                </div>
                                                                <div class="col-sm-8 form-group">
                                                                    <label for="og_city">' . esc_html__('City', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="og_city" name="organizer[og_city]" type="text" class="form-control" value="' . esc_attr($organizer->og_city) . '">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 form-group">
                                                                    <label for="og_country">' . esc_html__('Country', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="og_country" name="organizer[og_country]" type="text" class="form-control" value="' . esc_attr($organizer->og_country) . '">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6 form-group">
                                                                    <label for="og_email">' . esc_html__('Email address', 'ticketmachine-event-manager') . ' *</label>
                                                                    <input id="og_email" name="organizer[og_email]" type="email" class="form-control" value="' . esc_attr($organizer->og_email) . '" required>
                                                                </div>
                                                                <div class="col-6 form-group">
                                                                    <label for="og_phone">' . esc_html__('Phone number', 'ticketmachine-event-manager') . '</label>
                                                                    <input id="og_phone" name="organizer[og_phone]" type="text" class="form-control" value="' . esc_attr($organizer->og_phone) . '">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';

            $ticketmachine_communityevents_output .= '<div class="row">
                                                        <div class="col-12 mt-3 text-right">
                                                            <div class="form-group form-check">
                                                                <input type="checkbox" class="form-check-input" id="dataprivacy" required>
                                                                <label class="form-check-label" for="dataprivacy">
                                                                    ' . esc_html__('I have read and understood the', 'ticketmachine-event-manager') . ' 
                                                                    <a target="_blank" href="/' . $tm_globals->privacy_slug . '">
                                                                    ' . esc_html__('Privacy Policy', 'ticketmachine-event-manager') . '
                                                                    </a> 
                                                                    ' . esc_html__('and agree with the contents.', 'ticketmachine-event-manager') . '
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>';

            $ticketmachine_communityevents_output .= '<div class="row">
                                                        <div class="col-12 mt-3 text-right">
                                                            <input type="submit" name="submit" class="btn btn-primary" value="' . esc_html__('Create event', 'ticketmachine-event-manager') . '">
                                                        </div>
                                                    </div>';
            

        $ticketmachine_communityevents_output .= "</form>";

        $ticketmachine_communityevents_output .= "<script>
                                                    jQuery(document).ready(function($) {
                                                        jQuery('input.date').datetimepicker({
                                                            format: 'DD.MM.YYYY',
                                                            locale: '" .  get_locale() . "'
                                                        });
                                                        jQuery('input.time').datetimepicker({
                                                            format: 'HH:mm',
                                                            locale: '" . get_locale() . "',
                                                            stepping: 15
                                                        });
                                                    });
                                                </script>";

        return $ticketmachine_communityevents_output;
    }
?>