<?php

class james_booking
{

    public function __construct()
    {

        add_shortcode('james_booking', array($this, 'shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'init_scripts'));
        add_action('init', array($this, 'add_book_post'));
    }

    public function add_book_post()
    {
        if ($_POST['bookSlot']) {

            $this->validation();
            $this->start_book();
        } elseif ($_GET['func'] == "checkAvail") {
            $this->check_avail();

        } elseif ($_GET['paymentId']) {
            $this->receive_payment();
        }
    }

    public function check_avail()
    {
        $startDate     = $_GET["paramStartDate"];
        $noOfTables    = intval($_GET["paramNoOfTables"]);
        $paramLocation = $_GET["paramLocation"];
        $session       = intval($_GET["paramSession"]);
        $result        = array("startDate" => $startDate, "noOfTables" => $noOfTables, "location" => location, "session" => $session);

        $args = array('post_type' => 'slot',
            'meta_query'              => array(
                array(
                    'key'     => 'paramStartDate',
                    'value'   => $startDate,
                    'compare' => '=',
                ),
                array(
                    'key'     => 'paramStartDate',
                    'value'   => $startDate,
                    'compare' => '=',
                ),
            ),
        )
        ;
        $the_query            = new WP_Query($args);
        $result["found_post"] = $the_query->found_posts;
        echo json_encode($result);

        die();
    }

    public function shortcode()
    {

        ob_start();
        $this->booking_form();
        return ob_get_clean();
    }

    public function init_scripts()
    {
        wp_enqueue_style('bootstrap-css', plugins_url('bootstrap/css/bootstrap-tsa.css', __FILE__));
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_script('jquery-validation', plugins_url('js/jquery.validate.min.js', __FILE__));
        wp_enqueue_script('jquery-validation-additional', plugins_url('js/additional-methods.js', __FILE__));
        wp_enqueue_script('james-booking-form', plugins_url('js/james_booking_form.js', __FILE__));

        //wp_enqueue_style('flat-ui-kit', plugins_url('css/flat-ui.css', __FILE__));
        //TODO
        //include the script to have jquery validation inside check whether there is a jquery inside

    }

    public function booking_form()
    {

        include_once 'james_booking_form.php';
        ?>


    <?php
}

    public function validation()
    {

    }

    public function start_book()
    {

        $post_detail = array(

            'post_title'   => 'Slot',
            'post_content' => 'testing it',
            'post_type'    => 'slot',
            'post_name'    => 'yale',
            'post_status'  => 'Publish',
        );

        $post_id = wp_insert_post($post_detail, true);

        foreach ($_POST as $key => $value) {
            if (strstr($key, 'param')) {
                add_post_meta($post_id, $key, $value);
            }
        }

        //add the created date
        add_post_meta($post_id, 'bookedDate', getdate());
        //calculate the cost
        $one_table_cost = (($_POST['paramStudentOrAdult'] == "Student") ? 10 : 15) * (($_POST['paramSession'] == 3) ? 2 : 1);

        add_post_meta($post_id, "total_cost", '$.$one_table_cost');

        $mypostobject = (object) $_POST;

        //start the payment

        //addAndRedirectPayment($one_table_cost, $_POST[$GLOBALS['paramNoOfTables']], $item_name, $post_id);

    }

    public function receive_payment()
    {
        receive_paypal_payment();
        send_sms('test');
    }
}