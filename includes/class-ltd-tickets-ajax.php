<?php

/**
 * The ajax functionality of the plugin.
 *
 * Enqueue all ajax functions.
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/admin
 * @author     Ben Campbell <ben@ukds.co>
 */
class Ltd_Tickets_Ajax {

	private $plugin_name;
	private $version;
    private $integration;
    private $plugin_options;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->plugin_options = get_option($this->plugin_name);
        $this->integration = new LTD_Tickets_Integration($plugin_name, $version);
	}

    public function ajax_init() {

        // ADMIN ONLY CALLS FOR PRODUCT IMPORT
		add_action( 'wp_ajax_ukds-fetch-products', array($this, 'ltd_ajax_fetch_products') );
		add_action( 'wp_ajax_ukds-fetch-venues', array($this, 'ltd_ajax_fetch_venues') );
        add_action( 'wp_ajax_ukds-fetch-product-types', array( $this, 'ltd_ajax_fetch_product_types' ) );
        add_action( 'wp_ajax_ukds-heartbeat', array($this, 'ltd_ajax_heartbeat') );

        // AJAX HANDLER CALL
		add_action( 'wp_ajax_ukds-ajax-handler', array($this, 'ltd_ajax_core') );
        add_action( 'wp_ajax_nopriv_ukds-ajax-handler', array($this, 'ltd_ajax_core') );

        // PUBLIC CALLS FOR PERFORMANCE + PRICE HANDLING
		add_action( 'wp_ajax_ukds-fetch-performances', array($this, 'ltd_ajax_fetch_performances') );
		add_action( 'wp_ajax_ukds-fetch-prices', array($this, 'ltd_ajax_fetch_prices') );
		add_action( 'wp_ajax_nopriv_ukds-fetch-performances', array($this, 'ltd_ajax_fetch_performances') );
		add_action( 'wp_ajax_nopriv_ukds-fetch-prices', array($this, 'ltd_ajax_fetch_prices') );

	}

    public function ltd_ajax_heartbeat() {
 		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'ukds-ajax-handler-nonce' ) ) die ( 'Invalid Nonce' );
        header( "Content-Type: application/json" );
        $success = false;
        $api_url = $_REQUEST['api_url'];
        $api_key = $_REQUEST['api_key'];

        $results = $this->integration->heartbeat( $api_key, $api_url );
        if ( $results == true ) {
            $success = true;
        }
		echo json_encode( array(
			'success' => $success,
			'time' => time(),
		) );
		exit;
    }

    public function ltd_ajax_fetch_prices() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'ukds-ajax-handler-nonce' ) ) die ( 'Invalid Nonce' );
			header( "Content-Type: application/json" );
			$collection = array();
            $results = $this->integration->fetch_performance_prices( $_REQUEST['performance_id'], $_REQUEST['number_of_tickets'] );
			foreach($results as $result) {
				$tickets = array();
				foreach($result['Tickets'] as $ticketItem) {
					$ticket = array(
						'ticket_id'				=> $ticketItem['TicketId'],
						'ticket_row'			=> $ticketItem['TicketName1'],
						'ticket_number'			=> $ticketItem['TicketName2'],
						'is_restricted_view'	=> $ticketItem['IsRestrictedView'],
						'seat_rating_text'		=> ( ! empty($ticketItem['RestrictionDescription']) ? $ticketItem['RestrictionDescription'] : ''),
					);
					array_push($tickets,$ticket);
				}
				$performance = array(
					'area_name'				=> $result['AreaName'],
					'selling_price'			=> $result['SellingPrice'],
					'face_value'			=> $result['FaceValue'],
					'booking_link'			=> $result['SingleItemBookLink'],
					'tickets'				=> $tickets,
				);
				array_push($collection,$performance);
			}
			echo json_encode( array(
				'success' => true,
				'time' => time(),
				'prices' => $collection,
			) );
			exit;
	}

	public function ltd_ajax_fetch_performances() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'ukds-ajax-handler-nonce' ) ) die ( 'Invalid Nonce' );
			header( "Content-Type: application/json" );
			$collectionAvailable = array();
            $collectionSoldOut = array();

            $results = $this->integration->fetch_product_performances( $_REQUEST['product_id'] );
			foreach( $results as $result ) {

                setlocale( LC_MONETARY, array( 'en_GB' ) );
                $price = utf8_encode( money_format( '%n', $result['MinimumTicketPrice'] ) );

                if ( $result['TicketCount'] == 0 ) {
                    $performance = array(
                        'start'				=> $result['PerformanceDate'],
                        'performance_id'	=> $result['PerformanceId'],
                        'title'				=> __("Sold Out", $this->plugin_name),
                        'available'			=> $result['TicketCount'],
                    );
                    array_push( $collectionSoldOut, $performance );
                } else {
                    $performance = array(
                        'start'				=> $result['PerformanceDate'],
                        'performance_id'	=> $result['PerformanceId'],
                        'title'				=> ( $result['MinimumTicketPrice'] == 0 ? '' : 'From ' . $price ),
                        'available'			=> $result['TicketCount'],
                    );
                    array_push( $collectionAvailable, $performance );
                }
			}

            $collection = array(
                'available'             => $collectionAvailable,
                'sold_out'              => $collectionSoldOut,
            );

			echo json_encode( array(
				'success' => true,
				'time' => time(),
				'performances' => $collection,
			) );
			exit;
	}

    public function ltd_ajax_fetch_venues() {
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'ukds-ajax-handler-nonce' ) ) die ( 'Invalid Nonce' );
		header( "Content-Type: application/json" );
        $api = 0;
        if ( isset( $_REQUEST['api'] ) ) {
            try {
                $api = ( int )$_REQUEST['api'];
            } catch ( Exception $e ) {
                $api = 0;
            }
        }
		$collection = array();
        switch ( $api ) {

            // CASE -1 = GET PRODUCTS ALREADY ADDED TO WEBSITE
            case -1;
                $args = array(
                    'posts_per_page'        => -1,
                    'post_type'             => $this->plugin_options['config']['venue_post_type'],
                    'orderby'               => 'title',
                    'order'                 => 'ASC',
                    'suppress_filters'      => 0,
                );
                $myposts = get_posts( $args );
                foreach ( $myposts as $post ) :
                    setup_postdata( $post );
                    $venue = array(
                        'id'            => $post->ID,
                        'name'          => $post->post_title,
                        'venue_id'      => get_post_meta($post->ID, 'venue_id', true),
                        'permalink'     => get_the_permalink($post->ID),
                    );
				    array_push($collection,$venue);
                endforeach;
                wp_reset_postdata();
                echo json_encode( array(
				    'success' => true,
				    'time' => time(),
				    'venues' => $collection,
			    ) );
			    exit;

            case 0 :
                $results = $this->integration->fetch_venues();
			    foreach( $results as $result ) {
				    if( empty( $result['City'] ) )  {
					    continue;
				    } elseif ( $result['City'] != 'London' ) {
					    continue;
				    }
				    $venue = array(
					    'id'	=> $result['VenueId'],
					    'name'	=> $result['Name'],
				    );
				    array_push( $collection, $venue );
			    }
			    echo json_encode( array(
				    'success' => true,
				    'time' => time(),
				    'venues' => $collection,
			    ) );
			    exit;
        }
	}

    public function ltd_ajax_fetch_product_types() {
    	if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'ukds-ajax-handler-nonce' ) ) die ( 'Invalid Nonce' );
        header( "Content-Type: application/json" );

        $success = false;
        $results = $this->integration->fetch_product_types();
        if ($results) {
            $success = true;
        }

        $collection = [];
         foreach($results as $result) {
            $item = array(
				'id'	=> $result['EventTypeId'],
				'name'	=> $result['EventTypeName'],
			);
            $collection[] = $item;
         }

        echo json_encode( array(
		'success' => $success,
		'time' => time(),
		'categories' => $collection,
		));
		exit;
    }

	public function ltd_ajax_fetch_products() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'ukds-ajax-handler-nonce' ) ) die ( 'Invalid Nonce' );
			header( "Content-Type: application/json" );

            $api = 0;
            if (isset($_REQUEST['api'])) {
                try {
                    $api = (int)$_REQUEST['api'];
                } catch (Exception $e) {
                    $api = 0;
                }

            }
			$collection = array();

            switch ($api) {
                // CASE -1 = GET PRODUCTS ALREADY ADDED TO WEBSITE
                case -1 :
                    $args = array(
                        'posts_per_page'        => -1,
                        'post_type'             => $this->plugin_options['config']['product_post_type'],
                        'orderby'               => 'title',
                        'order'                 => 'ASC',
                        'suppress_filters'      => 0,
                        'post_status'           => array( 'publish', 'pending'),

                    );
                    $myposts = get_posts( $args );
                    foreach ( $myposts as $post ) :
                        setup_postdata( $post );
                        $product = array(
                            'id'            => $post->ID,
                            'name'          => $post->post_title,
                            'product_id'    => get_post_meta($post->ID, 'product_id', true),
                            'permalink'     => get_the_permalink($post->ID),
                        );
				        array_push($collection,$product);
                    endforeach;


                    wp_reset_postdata();
                    echo json_encode( array(
				        'success' => true,
				        'time' => time(),
				        'products' => $collection,
			        ) );
			        exit;

                // CASE 0 = LONDON THEATRE DIRECT API
                case 0 :
                    $results = $this->integration->fetch_products();
			        foreach($results as $result) {
				        if (stripos(strrev($result['Name']), "KROY WEN ") === 0) continue;
				        $product = array(
					        'id'	=> $result['EventId'],
					        'name'	=> $result['Name'],
				        );
				        array_push($collection,$product);
			        }

			        echo json_encode( array(
				        'success' => true,
				        'time' => time(),
				        'products' => $collection,
			        ) );
			        exit;
            }


	}

	public function ltd_ajax_core() {
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'ukds-ajax-handler-nonce' ) ) die ( 'Invalid Nonce' );
		header( "Content-Type: application/json" );
		echo json_encode( array(
			'success' => true,
			'time' => time()
		) );
		exit;
    }

}