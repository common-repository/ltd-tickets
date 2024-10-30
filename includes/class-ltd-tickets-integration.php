<?php

/**
 * API Integration Functions.
 *
 * Contains the API requests used for importing data from the LTD API.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */

class LTD_Tickets_Integration {

    use LTD_Tickets_Logging;

	private $plugin_name;
	private $version;
    protected $api_url;
    protected $headers;
    protected $db;
    protected $basket_id;

    function __construct( $plugin_name, $version ) {
        global $wpdb;
        //$this->basket_id = get_basket_id();
        $this->db = $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$options = get_option( $plugin_name );
        $this->api_url = $options['api']['url_' . $options['config']['api_target']];
        $api_key = $options[$options['config']['api_user']]['api_key_' . $options['config']['api_target']];
        $this->headers = array (
			"Api-Key: " . $api_key,
			"Content-Type: application/json",
        );
    }

    public function execute( $method, $body = NULL, $type = "GET" ) {
        try {
		    $curl = curl_init( $this->api_url . $method );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		    curl_setopt( $curl, CURLOPT_HTTPHEADER, $this->headers );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
            if ($type != "GET") {
                switch($type) {
                    case "POST" :
  		                curl_setopt( $curl, CURLOPT_POST, true );
                        break;
                    case "DELETE" :
                        curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "DELETE" );
                        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
                        break;
                }
            }
            if (isset($body)) {
                curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $body ) );
            }
            $curl_response = curl_exec($curl);
		    if ($curl_response === false) {
                throw new Exception( curl_error( $curl ), curl_errno( $curl ) );
		    }
		    curl_close($curl);

		    $decoded = json_decode( $curl_response, true );
		    if (isset($decoded->ErrorCode) && $decoded->ErrorCode != '') {
                $this->Log(array('type'=>'ERROR','message'=>'REST Error: ' . $decoded->Message,'stack'=>$decoded->MessageDetail));
			    die('error occured: ' . $decoded->Message . '\n' . $decoded->MessageDetail);
		    }

            return $decoded;

        } catch(Exception $e) {
            $this->Log(
                array(
                    'type'      =>'ERROR',
                    'message'   => sprintf(
                                    'Curl failed with error #%d: %s',
                                    $e->getCode(), $e->getMessage()),
                    'stack'     =>var_export($e->getTrace(), true)
                )
            );
        }
        return false;
    }

    // CHECK STATUS OF API / VALIDATE API KEYS
    public function heartbeat( $api_key = null, $api_url = null ) {
        if ( isset( $api_url ) ) $this->api_url = $api_url;
        if ( isset( $api_key ) ) {
            $this->headers = array (
                "Api-Key: " . $api_key,
                "Content-Type: application/json",
            );
        }
        $result = $this->execute("System/HeartBeat");
        return $result;
    }

    // FETCH DELIVERY TYPES AND PRICES
    public function fetch_delivery_type_price() {
		$result = $this->execute("System/DeliveryTypes");
        if (isset($result["DeliveryTypes"])) {
            return $result["DeliveryTypes"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for System/DeliveryTypes.',
               )
           );
            return false;
        }
    }

    // FETCH ARRAY OF PRODUCT TYPES
    public function fetch_product_types() {
        $result = $this->execute("System/EventTypes");
        if (isset($result["EventTypes"])) {
            return $result["EventTypes"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for System/EventTypes.',
               )
           );
            return false;
        }
    }

    public function fetch_ticket_protection_price() {
		$result = $this->execute("TicketPlanPrice");
        if (isset($result["Price"])) {
            return $result["Price"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for TicketPlanPrice.',
               )
           );
            return false;
        }
    }

    // FETCH ALL VENUES
    public function fetch_venues() {
        $result = $this->execute("Venues");
        if (isset($result["Venues"])) {
            return $result["Venues"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for fetch_venues.',
               )
           );
            return false;
        }
    }

    // FETCH SINGLE VENUE
    public function fetch_venue( $venue_id ) {
        $result = $this->execute("Venues/" . $venue_id);
        if (isset($result["Venue"])) {
            return $result["Venue"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for fetch_venue with ID ' . $venue_id,
               )
           );
            return false;
        }
    }

    // FETCH ALL PRODUCTS
    public function fetch_products($api_key = null) {
        if ($api_key) {
            $this->headers = array (
			    "Api-Key: " . $api_key,
			    "Content-Type: application/json",
            );
        }
  		$result = $this->execute("Events");
        if (isset($result["Events"])) {
            return $result["Events"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for fetch_events.',
               )
           );
            return false;
        }
    }


    // FETCH SINGLE PRODUCT
    public function fetch_product( $product_id ) {
  		$result = $this->execute("Events/" . $product_id);
        if (isset($result["Event"])) {
            return $result["Event"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for fetch_event with ID ' . $product_id,
               )
           );
            return false;
        }
    }

    // FETCH PERFORMANCES FOR PRODUCT
    public function fetch_product_performances( $product_id ) {
		$result = $this->execute("Events/$product_id/Performances");
        if (isset($result["Performances"])) {
            return $result["Performances"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for Performances with ID ' . $product_id,
               )
           );
            return false;
        }
    }

    // FETCH AVAILABLE PRICES FOR PERFORMANCE
    public function fetch_performance_prices( $performance_id, $number_of_tickets = 2 ) {
		$result = $this->execute("Performances/$performance_id/AvailableTickets?requiredTicketsCount=$number_of_tickets");
        if (isset($result["TicketAreas"])) {
            return $result["TicketAreas"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for fetch_performance_prices with ID ' . $performance_id,
               )
           );
            return false;
        }
    }

    // FETCH LIVE SEAT PLAN FOR PERFORMANCE
    // NOTE: This will not work if Wordpress is installed on a subdirectory
    public function fetch_seat_plan( $performance_id ) {
        $domain = site_url();
        $css = "https://showsinlondon.ukds.co/wp-content/themes/ukds-child/css/seatingplan.css";
        $result = $this->execute("Performances/$performance_id/SeatingPlan?parentDomain=$domain&customCssUrl=$css");
        if (isset($result["SeatingPlanUrl"])) {
            return $result["SeatingPlanUrl"];
        } else {
            $this->Log(
               array(
                   'type'      =>'WARNING',
                   'message'   => 'API failed to return data for fetch_seat_plan with ID ' . $performance_id,
               )
           );
            return false;
        }
    }

    // FETCH SHOPPING BASKET
    public function fetch_basket() {
        if ($this->basket_id != null) {
            $result = $this->execute("Baskets/$this->basket_id");
        } else {
            $result = false;
        }
        return $result;
    }

    // FETCH A SUBMITTED SHOPPING BASKET
    public function fetch_submitted_basket( $basket_id = NULL ) {
        $basket_id = ($basket_id == null ? ($this->basket_id != false ? $this->basket_id : null) : $basket_id);
        if ($basket_id != null) {
            $result = $this->execute("Baskets/$basket_id/SubmittedBasketSummary");
        } else {
            $result = false;
        }
        return $result;
    }

    // CREATE A NEW SHOPPING BASKET
    public function create_basket() {
		$result = $this->execute("Baskets", null, "POST" );
        return $result;
    }

    // ADD TICKETS TO A SHOPPING BASKET
    public function add_to_basket( $seat_ids ) {
        $tickets["Tickets"] = $seat_ids;
        if ($this->basket_id != false) {
    		$result = $this->execute( "Baskets/$this->basket_id/Tickets", $tickets, "POST" );
        } else {
            $result = false;
            $this->Log(array('type'=>'ERROR','message'=>"Cannot add to basket: No basket ID found"));
        }
        return $result;
    }

    // REMOVE AN ITEM FROM SHOPPING BASKET
    public function remove_from_basket( $basket_item ) {
        if ($this->basket_id != false) {
            $result = $this->execute( "Baskets/$this->basket_id/BasketItem/$basket_item", null, "DELETE" );
        } else {
            $result = false;
            $this->Log(array('type'=>'ERROR','message'=>"Cannot remove from basket: No basket ID found"));
        }
        return $result;
    }

    // DELETE THE CURRENT SHOPPING BASKET
    public function delete_basket() {
        if ( $this->basket_id != false ) {
            $result = $this->execute( "Baskets/$this->basket_id/Tickets", null, "DELETE" );
        } else {
            $result = false;
            $this->Log(array('type'=>'ERROR','message'=>"Cannot delete basket: No basket ID found"));
        }
        return $result;
    }

    // SUBMIT AN ORDER
    public function submit_order( $collection ) {
        if ($this->basket_id != false) {
        	$result = $this->execute( "Baskets/$this->basket_id/SubmitOrder", $collection, "POST" );
        } else {
            $result = false;
            $this->Log(array('type'=>'ERROR','message'=>"Cannot submit order: No basket ID found"));
        }
        return $result;
    }


}
