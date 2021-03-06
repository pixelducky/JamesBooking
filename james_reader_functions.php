<?php
function james_get_users_for_readers()
{
    $startDateObj = new DateTime('now',new DateTimeZone("Asia/Singapore"));
    
    $minStartDateObj = new DateTime('now',new DateTimeZone("Asia/Singapore"));
    $minStartDateObj->setTime(0,0);

    
    $interval = date_diff($minStartDateObj, $startDateObj);
    
    if(intval($interval->format('%h')) < 9)
        $startDateObj->modify('-1 day');


    $startDate = $startDateObj->format('d M Y');
    $location  = $_GET["location"];

    //get the location
    //get the current date
    //get the pins to input with the post id as the user id

    

    $args = array('post_type' => 'slot',
        'meta_query'              => array(
            array(

                'key'     => 'StartDate',
                'value'   => $startDate,
                'compare' => '=',
            ),
            array(
                'key'     => 'Location',
                'value'   => $location,
                'compare' => '=',
            ),
        ),
    );
    $the_query = new WP_Query($args);

    $post_count = $the_query->post_count;

    $results     = array();
    $latest_date = "";

    while ($the_query->have_posts()) {
        $the_query->the_post();
        $postId        = get_the_ID();
        $pins          = get_post_meta($postId, 'pin', true);
        $modified_date = get_the_modified_date('d M Y h:i');
        $slot_date     = get_post_meta($postId, 'StartDate', true);
        $expired_from = get_post_meta($postId, 'expiredFrom', true);
        $expired_to = get_post_meta($postId, 'expiredTo', true);

        
        if (DateTime::createFromFormat('d M Y h:i',$modified_date) > $latest_date_obj) {
            $latest_date = $modified_date;
            $latest_date_obj = DateTime::createFromFormat('d M Y h:i', $modified_date);
        }

        $paypalPaymentID = get_post_meta($postId, 'paypalPaymentID', true);
        $session         = get_post_meta($postId, 'Session', true);
        $pin_array       = explode(",", $pins);
        for ($curr_pin = 0; $curr_pin < count($pin_array); $curr_pin++) {
            array_push($results, array(
                'modified_date' => $modified_date,
                'user_id'       => "Slot$postId Date$slot_date PN".$pin_array[$curr_pin],
                'pin'           => $pin_array[$curr_pin],
                'slot'          => get_the_ID(),
                'session'       => $session,
                'expiredFrom' => $expired_from,
                'expiredTo' => $expired_to

            ));
        }

    }

    echo json_encode(array('last_modified_date' => $latest_date, 'pins' => $results));

    die();
}

//add function where we can add the user id into the website to keep track of the reader user id into the website
