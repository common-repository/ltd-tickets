<?php
/**
 * Data Import and Synchronisation.
 *
 * Handles the import of products, venues and categories as well as the creation
 * and synchronisation of posts .
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */

class LTD_Tickets_Data_Sync
{
    private $plugin_name;
    private $version;
    private $integration;
    private $plugin_options;
    protected $db;

    use LTD_Tickets_Logging;


    function __construct( $plugin_name, $version )
    {
        global $wpdb;
        $this->db             = $wpdb;
        $this->plugin_name    = $plugin_name;
        $this->version        = $version;
        $this->integration    = new LTD_Tickets_Integration( $plugin_name, $version );
        $this->plugin_options = get_option( $plugin_name );
    }


    public function import_all()
    {

        $imports['categories'] = $this->import_categories();
        $imports['venues']     = $this->import_venues();
        $imports['products']   = $this->import_products();

        return $imports;
    }

    public function sync_all()
    {

        $updates['venues']   = $this->sync_venues();
        $updates['products'] = $this->sync_products();

        return $updates;

    }


    /*
     * ========================
     * Import and Sync Venues
     * ========================
     */


    public function import_selected_venues()
    {

        $importArray = array();
        foreach ( $_POST as $key => $value ) {
            if ( strstr( $key, 'venue-' ) ) {
                $x = str_replace( 'venue-', '', $key );
                array_push( $importArray, $x );
            }
        }
        return $this->import_venues( $importArray );

    }

    public function import_venues( $list = array() )
    {

        $venues = $this->integration->fetch_venues();
        if ($venues === false) return 0;



        $i = 0;
        foreach ( $venues as $venue ) {
            if ( empty( $venue['City'] ) ) {
                continue;
            } elseif ( $venue['City'] != 'London' ) {
                continue;
            }
            if ( ( count( $list ) > 0 ) && !in_array( $venue['VenueId'], $list ) )
                continue;

            $args = array(
                'posts_per_page'        => 1,
                'post_type'             => $this->plugin_options['config']['venue_post_type'],
                'post_status'           => array(
                                            'publish',
                                            'pending',
                                            'draft'
                                        ),
                'meta_query'            => array(
                                                array(
                                                    'key' => 'venue_id',
                                                    'value' => $venue['VenueId']
                                                )
                                            )
            );
            $check_post = get_posts($args);
            if (!empty($check_post)) continue;


            $content = "";
            if (!empty($venue['Info'])) {
                $content = preg_replace(array('"<a (.*?)>"', '"</a>"'), array('',''), $venue['Info']);
            }


            $my_post = array(
                'post_title' => wp_strip_all_tags( $venue['Name'] ),
                'post_content' => $content,
                'post_status' => $this->plugin_options['sync']['import_venues_status'],
                'post_type' => $this->plugin_options['config']['venue_post_type']
            );

            $post_id = wp_insert_post( $my_post, true );
            if ( is_wp_error( $post_id ) ) {
                $this->Log( array(
                     'type' => 'ERROR',
                    'message' => "Unable to add venue with ID: " . $venue['VenueId'] . " - " . $post_id->get_error_message()
                ) );
                continue;
            }

            if ( $this->venue_meta( $venue, $post_id ) ) {
                $this->Log(array(
                    'type'      => 'INFO',
                    'message'   => $venue['Name'] . ' successfully imported with ID : ' . $post_id
                ));
                $i++;
            }


        }
        return $i;
    }

    public function import_venue( $venue_id )
    {

        $venue = $this->integration->fetch_venue( $venue_id );
        if ($venue === false) return false;

        if ( empty( $venue['City'] ) ) {
            return false;
        } elseif ( $venue['City'] != 'London' ) {
            return false;
        }

        $content = "";
        if (!empty($venue['Info'])) {
            $content = preg_replace(array('"<a (.*?)>"', '"</a>"'), array('',''), $venue['Info']);
        }


        $my_post = array(
            'post_title' => wp_strip_all_tags( $venue['Name'] ),
            'post_content' => $content,
            'post_status' => $this->plugin_options['sync']['import_venues_status'],
            'post_type' => $this->plugin_options['config']['venue_post_type']
        );

        $post_id = wp_insert_post( $my_post, true );
        if ( is_wp_error( $post_id ) ) {
            $this->Log( array(
                 'type' => 'ERROR',
                'message' => "Unable to add venue : " . (isset($venue['Name']) ? $venue['Name'] : 'Undefined') . " - " . $post_id->get_error_message()
            ) );
            return false;
        }

        if ($this->venue_meta( $venue, $post_id )) {
            $this->Log(array(
                'type'      => 'INFO',
                'message'   => $venue['Name'] . ' successfully imported with ID : ' . $post_id
            ));
            return true;
        } else {
            $this->Log(array(
                'type'      => 'ERROR',
                'message'   => 'Import of venue : ' . (isset($venue['Name']) ? $venue['Name'] : 'Undefined') . ' failed. '
            ));
            return false;
        }

    }

    public function sync_selected_venues()
    {

        $i = 0;
        foreach ( $_POST as $key => $value ) {
            if ( strstr( $key, 'update-venue-' ) ) {
                $post_id  = str_replace( 'update-venue-', '', $key );
                $venue_id = get_post_meta( $post_id, 'venue_id', true );

                try {
                    if ( empty( get_post_meta($post_id, 'last_updated', true) ) ||
                        strtotime(get_post_meta($post_id, 'last_updated', true)) < strtotime('-1 hour')) {

                        $venue = $this->integration->fetch_venue( $venue_id );
                        if ($venue === false) continue;
                        if ( $this->venue_meta( $venue, $post_id, true ) ) {
                            $i++;
                        }

                    } else {
                        throw new Exception(get_the_title($post_id) . " update failed - Venues cannot be updated more than once an hour.");
                    }

                }
                catch ( Exception $e ) {
                    $this->Log( array(
                         'type' => 'ERROR',
                        'message' => $e->getMessage(),
                    ) );
                }
            }
        }
        return $i;
    }

    public function sync_venues( $list = array() )
    {
        $i = 0;
        $args = array(
            'posts_per_page' => -1,
            'post_type' => $this->plugin_options['config']['venue_post_type'],
            'orderby' => 'title',
            'order' => 'ASC',
            'suppress_filters' => 0,
            'post_status' => array(
                'publish',
                'pending',
                'draft'
            )
        );
        $posts = get_posts( $args );
        if ( !empty($posts) ) {
            foreach ( $posts as $post ):
                $venue_id = get_post_meta( $post->ID, 'venue_id', true );
                if ( ( count( $list ) > 0 ) && !in_array( $venue_id, $list ) ) continue;

                try {
                    if ( empty( get_post_meta($post->ID, 'last_updated', true) ) ||
                        strtotime(get_post_meta($post->ID, 'last_updated', true)) < strtotime('-1 hour')) {

                        $venue = $this->integration->fetch_venue( $venue_id );
                        if ($venue === false) continue;

                        if ($this->venue_meta( $venue, $post->ID, true)) {
                            $this->Log(array(
                                'type'      => 'INFO',
                                'message'   => $venue['Name'] . ' successfully synced.'
                            ));
                            $i++;
                        } else {
                            $this->Log(array(
                                'type'      => 'ERROR',
                                'message'   => 'Failed to sync venue  : ' . (isset($venue['Name']) ? $venue['Name'] : 'Undefined')
                            ));
                        }
                    } else {
                        throw new Exception($post->post_title . " update failed - Venues cannot be updated more than once an hour.");
                    }

                }
                catch ( Exception $e ) {
                    $this->Log( array(
                         'type' => 'ERROR',
                        'message' => $e->getMessage(),
                    ) );
                }
            endforeach;
        }
        return $i;
    }

    public function sync_venue( $venue_id )
    {

        $args = array(
            'posts_per_page' => 1,
            'post_type' => $this->plugin_options['config']['venue_post_type'],
            'meta_key' => 'venue_id',
            'meta_value' => $venue_id,
            'suppress_filters' => 0,
            'post_status' => array(
                 'publish',
                'pending',
                'draft'
            )
        );

        $posts = get_posts( $args );
        $valid = false;
        if ( !empty($posts) ) {
            $post  = $posts[0];

            try {
                if ( empty( get_post_meta($post->ID, 'last_updated', true) ) ||
                    strtotime(get_post_meta($post->ID, 'last_updated', true)) < strtotime('-1 hour')) {

                    $venue = $this->integration->fetch_venue( $venue_id );
                    if ($venue === false) return false;

                    if ($this->venue_meta( $venue, $post->ID, true)) {
                        $this->Log(array(
                            'type'      => 'INFO',
                            'message'   => $venue['Name'] . ' successfully synced.'
                        ));
                        $valid = true;
                    } else {
                        $this->Log(array(
                            'type'      => 'ERROR',
                            'message'   => 'Failed to sync venue  : ' . (isset($venue['Name']) ? $venue['Name'] : 'Undefined')
                        ));
                    }
                } else {
                    throw new Exception($post->post_title . " update failed - Venues cannot be updated more than once an hour.");
                }

            }
            catch ( Exception $e ) {
                $this->Log( array(
                     'type' => 'ERROR',
                    'message' => $e->getMessage(),
                ) );
            }
        }
        return $valid;

    }

    private function venue_meta( $venue, $post_id, $sync = false )
    {
        try {

            update_post_meta($post_id, 'last_updated', date('Y-m-d H:i:s'));

            if ( !empty( $venue['Address'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['venue_update_address'] == 1 ) {
                    update_post_meta( $post_id, 'address', esc_html( $venue['Address'] ) );
                }
            }

            if ( !empty( $venue['City'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['venue_update_city'] == 1 ) {
                    update_post_meta( $post_id, 'city', $venue['City'] );
                }
            }

            if ( !empty( $venue['Postcode'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['venue_update_postcode'] == 1 ) {
                    update_post_meta( $post_id, 'postcode', $venue['Postcode'] );
                }
            }

            if ( !empty( $venue['NearestTube'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['venue_update_nearest_tube'] == 1 ) {
                    update_post_meta( $post_id, 'nearest_tube', $venue['NearestTube'] );
                }
            }

            if ( !empty( $venue['Train'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['venue_update_nearest_train'] == 1 ) {
                    update_post_meta( $post_id, 'nearest_train', $venue['Train'] );
                }
            }

            if ( !empty( $venue['PlanLink'] ) ) {
                if(ltd_curl_get_headers($venue['PlanLink']) == "200"){
                    if ( !$sync || $this->plugin_options['sync']['venue_update_seating_plan'] == 1 ) {
                        update_post_meta( $post_id, 'seating_plan', $venue['PlanLink'] );
                    }
                }
            }

            if ( !empty( $venue['VenueId'] ) ) {
                update_post_meta( $post_id, 'venue_id', esc_html( $venue['VenueId'] ) );
            }

            if ( !empty( $venue['ImageUrl'] ) ) {
                if(ltd_curl_get_headers($venue['ImageUrl']) == "200"){
                    if ( !$sync || $this->plugin_options['sync']['venue_update_image_url'] == 1 )
                        update_post_meta( $post_id, 'image_url', $venue['ImageUrl'] );
                    if (!$sync) {
                        ltd_generate_featured_image( $venue['ImageUrl'], $post_id );
                    }
                }
            }

            return true;

        }
        catch ( Exception $e ) {
            $this->Log( array(
                 'type' => 'ERROR',
                'message' => $e->getMessage(),
            ) );
        }
        return false;
    }


    /*
     * ========================
     * Import and Sync Products
     * ========================
     */


    public function import_selected_products()
    {

        $importArray = array();
        foreach ( $_POST as $key => $value ) {
            if ( strstr( $key, 'product-' ) ) {
                $x = str_replace( 'product-', '', $key );
                array_push( $importArray, $x );
            }
        }
        return $this->import_products( $importArray );

    }

    public function import_products( $list = array() )
    {

        $products = $this->integration->fetch_products();
        if ($products === false) return 0;

        $i = 0;
        foreach ( $products as $product ) {
            if ( stripos( strrev( $product['Name'] ), "KROY WEN " ) === 0 )
                continue;
            if ( ( count( $list ) > 0 ) && !in_array( $product['EventId'], $list ) )
                continue;
            if ( empty( $product['EventDetailUrl'] ) )
                continue;
            if ( !isset( $product['StartDate'] ) || !isset( $product['EndDate'] ) ) {
                $this->Log( array(
                    'type' => 'ERROR',
                   'message' => "Unable to add product: " . $product['Name'] . " - Missing either Start Date or End Date."
               ) );
                continue;
            }
            if ( strtotime( $product['EndDate'] ) < time() ) {
                $this->Log( array(
                    'type' => 'ERROR',
                   'message' => "Unable to add product: " . $product['Name'] . " - Last performance is in the past."
               ) );
                continue;
            }

            $args = array(
                'posts_per_page'        => 1,
                'post_type'             => $this->plugin_options['config']['product_post_type'],
                'post_status'           => array(
                                            'publish',
                                            'pending',
                                            'draft'
                                        ),
                'meta_query'            => array(
                                                array(
                                                    'key' => 'product_id',
                                                    'value' => $product['EventId']
                                                )
                                            )
            );
            $check_post = get_posts($args);
            if (!empty($check_post)) continue;

            $content = "";
            if (!empty($product['Description'])) {
                $content = preg_replace(array('"<a (.*?)>"', '"</a>"'), array('',''), $product['Description']);
            }


            $my_post = array(
                'post_title'            => wp_strip_all_tags( $product['Name'] ),
                'post_content'          => $content,
                'post_status'           => $this->plugin_options['sync']['import_products_status'],
                'post_type'             => $this->plugin_options['config']['product_post_type']
            );
            $post_id = wp_insert_post( $my_post, true );
            if ( is_wp_error( $post_id ) ) {

                $this->Log( array(
                     'type' => 'ERROR',
                    'message' => "Unable to add product with ID: " . $product['EventId'] . " - " . $post_id->get_error_message()
                ) );
                continue;
            }

            if ( $this->product_meta( $product, $post_id ) ) {
                $this->Log(array(
                    'type'      => 'INFO',
                    'message'   => $product['Name'] . ' successfully imported with ID : ' . $post_id
                ));
                $i++;
            } else {
                $this->Log(array(
                    'type'      => 'ERROR',
                    'message'   => 'Import of product : ' . (isset($product['Name']) ? $product['Name'] : 'Undefined') . ' failed. '
                ));
            }

        }
        return $i;
    }

    public function import_product( $product_id )
    {

        $product = $this->integration->fetch_product( $product_id );
        if ($product === false) return false;

        if ( empty( $product['EventDetailUrl'] ) ) {
            $this->Log( array(
                 'type' => 'ERROR',
                'message' => "Unable to add product with ID: " . $product_id . " - No Event URL Found."
            ) );
            return false;
        }

        $content = "";
        if (!empty($product['Description'])) {
            $content = preg_replace(array('"<a (.*?)>"', '"</a>"'), array('',''), $product['Description']);
        }


        $my_post = array(
            'post_title'            => wp_strip_all_tags( $product['Name'] ),
            'post_content'          => $content,
            'post_status'           => $this->plugin_options['sync']['import_products_status'],
            'post_type'             => $this->plugin_options['config']['product_post_type']
        );

        $post_id = wp_insert_post( $my_post, true );
        if ( is_wp_error( $post_id ) ) {
            $this->Log( array(
                 'type' => 'ERROR',
                'message' => "Unable to add product with ID: " . $product_id . " - " . $post_id->get_error_message()
            ) );
            return false;
        }

        if ( $this->product_meta( $product, $post_id ) ) {
            $this->Log(array(
                'type'      => 'INFO',
                'message'   => $product['Name'] . ' successfully imported with ID : ' . $post_id
            ));
            return true;
        } else {
            $this->Log(array(
                'type'      => 'ERROR',
                'message'   => 'Import of product : ' . (isset($product['Name']) ? $product['Name'] : 'Undefined') . ' failed. '
            ));
            return false;
        }

    }

    public function sync_selected_products()
    {

        $i = 0;
        foreach ( $_POST as $key => $value ) {
            if ( strstr( $key, 'update-product-' ) ) {
                $post_id    = str_replace( 'update-product-', '', $key );
                $product_id = get_post_meta( $post_id, 'product_id', true );

                try {
                    if ( empty( get_post_meta($post_id, 'last_updated', true) ) ||
                        strtotime(get_post_meta($post_id, 'last_updated', true)) < strtotime('-1 hour')) {

                        $product    = $this->integration->fetch_product( $product_id );
                        if ($product === false) continue;
                        if ( $this->product_meta( $product, $post_id, true ) ) {
                            $this->Log(array(
                                'type'      => 'INFO',
                                'message'   => $product['Name'] . ' successfully synced.'
                            ));
                            $i++;
                        }

                    } else {
                        throw new Exception(get_the_title($post_id) . " update failed - Products cannot be updated more than once an hour.");
                    }

                }
                catch ( Exception $e ) {
                    $this->Log( array(
                         'type' => 'ERROR',
                        'message' => $e->getMessage(),
                    ) );
                }
            }
        }
        return $i;
    }

    public function sync_products( $list = array() )
    {
        $i = 0;

        $args = array(
             'posts_per_page' => -1,
            'post_type' => $this->plugin_options['config']['product_post_type'],
            'orderby' => 'title',
            'order' => 'ASC',
            'suppress_filters' => 0,
            'post_status' => array(
                'publish',
                'pending',
                'draft'
            )
        );

        $posts = get_posts( $args );
        if ( !empty($posts) ) {
            foreach ( $posts as $post ):
                $product_id = get_post_meta( $post->ID, 'product_id', true );

                if ( ( count( $list ) > 0 ) && !in_array( $product_id, $list ) )
                    continue;

                try {
                    if ( empty( get_post_meta($post->ID, 'last_updated', true) ) ||
                        strtotime(get_post_meta($post->ID, 'last_updated', true)) < strtotime('-1 hour')) {

                        $product = $this->integration->fetch_product( $product_id );
                        if ($product === false) continue;
                        if ( $this->product_meta( $product, $post->ID, true ) ) {
                            $this->Log(array(
                                'type'      => 'INFO',
                               'message'   =>  $product['Name'] . ' successfully synced.'
                            ));
                            $i++;
                        }
                    } else {
                        throw new Exception($post->post_title . " update failed - Products cannot be updated more than once an hour.");
                    }

                }
                catch ( Exception $e ) {
                    $this->Log( array(
                         'type' => 'ERROR',
                        'message' => $e->getMessage(),
                    ) );
                }
            endforeach;
        }
        return $i;
    }

    public function sync_product( $product_id )
    {

        $args = array(
             'posts_per_page' => 1,
            'post_type' => $this->plugin_options['config']['product_post_type'],
            'meta_key' => 'product_id',
            'meta_value' => $product_id,
            'suppress_filters' => 0,
            'post_status' => array(
                 'publish',
                'pending',
                'draft'
            )
        );

        $posts = get_posts( $args );
        $valid = false;
        if ( !empty($posts) ) {
            $post    = $posts[0];

            try {
                if ( empty( get_post_meta($post->ID, 'last_updated', true) ) ||
                        strtotime(get_post_meta($post->ID, 'last_updated', true)) < strtotime('-1 hour')) {

                        $product = $this->integration->fetch_product( $product_id );
                        if ($product === false) return false;
                        if ( $this->product_meta( $product, $post->ID, true ) ) {
                            $this->Log(array(
                                'type'      => 'INFO',
                                'message'   => $product['Name'] . ' successfully synced.'
                            ));
                            $valid = true;
                        } else {
                            $this->Log(array(
                                'type'      => 'ERROR',
                                'message'   => 'Sync of product : ' . (isset($product['Name']) ? $product['Name'] : 'Undefined') . ' failed. '
                            ));
                        }

                } else {
                    throw new Exception($post->post_title . " update failed - Products cannot be updated more than once an hour.");
                }

            }
            catch ( Exception $e ) {
                $this->Log( array(
                     'type' => 'ERROR',
                    'message' => $e->getMessage(),
                ) );
            }
        }
        return $valid;
    }

    private function product_meta( $product, $post_id, $sync = false )
    {
        try {

            update_post_meta($post_id, 'last_updated', date('Y-m-d H:i:s'));

            if ( !empty( $product['Description'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_content'] == 1 ) {
                    $content = preg_replace(array('"<a (.*?)>"', '"</a>"'), array('',''), $product['Description']);
                    $my_post = array(
                        'ID'           => $post_id,
                        'post_content' => $content,
                    );
                    wp_update_post( $my_post );
                    if (is_wp_error($post_id)) {
                        $errors = $post_id->get_error_messages();
                        foreach ($errors as $error) {
                            throw new Exception($error);
                        }
                    }
                }
            }

            if ( !empty( $product['RunningTime'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_running_time'] == 1 )
                    update_post_meta( $post_id, 'running_time', esc_html( $product['RunningTime'] ) );
            }
            if ( !empty( $product['MinimumAge'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_minimum_age'] == 1 )
                    update_post_meta( $post_id, 'minimum_age', esc_html( $product['MinimumAge'] ) );
            }
            if ( !empty( $product['StartDate'] ) ) {
                $start_date = date( "Y-m-d", strtotime( $product['StartDate'] ) );
                if ( !$sync || $this->plugin_options['sync']['product_update_start_date'] == 1 )
                    update_post_meta( $post_id, 'start_date', $start_date );
            }
            if ( !empty( $product['EndDate'] ) ) {
                $end_date = date( "Y-m-d", strtotime( $product['EndDate'] ) );
                if ( !$sync || $this->plugin_options['sync']['product_update_end_date'] == 1 )
                    update_post_meta( $post_id, 'end_date', $end_date );
            }
            if ( !empty( $product['TagLine'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_tagline'] == 1 )
                    update_post_meta( $post_id, 'tagline', $product['TagLine'] );
            }
            if ( !empty( $product['ImportantNotice'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_important_notice'] == 1 )
                    update_post_meta( $post_id, 'important_notice', $product['ImportantNotice'] );
            }
            if ( !empty( $product['EventMinimumPrice'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_minimum_price'] == 1 )
                    update_post_meta( $post_id, 'minimum_price', $product['EventMinimumPrice'] );
            }
            if ( !empty( $product['CurrentPrice'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_current_price'] == 1 ) {
                    $current_price = ($product['CurrentPrice'] == "" ? $product['EventMinimumPrice'] : $product['CurrentPrice']);
                    update_post_meta( $post_id, 'current_price', $current_price );
                }
            }
            if ( !empty( $product['OfferPrice'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_offer_price'] == 1 ) {
                    $offer_price = ($product['OfferPrice'] == "" ? $product['EventMinimumPrice'] : $product['OfferPrice']);
                    update_post_meta( $post_id, 'offer_price', $offer_price  );
                }
            }
            if ( !empty( $product['ShortOfferText'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_short_offer_text'] == 1 ) {
                    $short_offer_text = ltd_sanitise_meta_text($product['ShortOfferText']);
                    update_post_meta( $post_id, 'short_offer_text', $short_offer_text );
                }

            }
            if ( !empty( $product['LongOfferText'] ) ) {
                if ( !$sync || $this->plugin_options['sync']['product_update_long_offer_text'] == 1 ) {
                    $long_offer_text = ltd_sanitise_meta_text($product['LongOfferText']);
                    update_post_meta( $post_id, 'long_offer_text', $long_offer_text );
                }
            }
            if ( !empty( $product['MainImageUrl'] ) ) {
                if(ltd_curl_get_headers($product['MainImageUrl']) == "200"){
                    if ( !$sync || $this->plugin_options['sync']['product_update_main_image_url'] == 1 )
                        update_post_meta( $post_id, 'main_image_url', $product['MainImageUrl'] );
                    if (!$sync) {
                        ltd_generate_featured_image( $product['MainImageUrl'], $post_id );
                    }
                }
            }
            if( !empty($product['Images'])) {
                if ( !$sync || $this->plugin_options['sync']['product_update_image_gallery'] == 1 ) {
                    $thisImageCollection = $product['Images'];
                    $imageString = array();
                    foreach($thisImageCollection as $thisImage) {
                        array_push( $imageString , $thisImage['Url']);
                    }
                    if (count($imageString) > 0) add_post_meta($post_id, 'gallery_images', implode(',', $imageString));
                }
            }

            if ( !empty( $product['EventId'] ) ) {
                update_post_meta( $post_id, 'product_id', $product['EventId'] );
            }
            if ( !empty( $product['VenueId'] ) ) {
                update_post_meta( $post_id, 'venue_id', $product['VenueId'] );
            }
            if ( !empty( $product['EventDetailUrl'] ) ) {
                update_post_meta( $post_id, 'product_url', $product['EventDetailUrl'] );
            }

            if ( !empty( $product['EventType'] ) ) {
                $args  = array(
                     'number' => 1,
                    'hide_empty' => false,
                    'meta_query' => array(
                         array(
                             'key' => 'event_type_id',
                            'value' => $product['EventType'],
                            'compare' => 'EQUALS'
                        )
                    )
                );
                $terms = get_terms( $this->plugin_options['config']['product_category_taxonomy'], $args );
                if ( count( $terms ) > 0 ) {
                    wp_set_post_terms( $post_id, $terms[0]->term_taxonomy_id, $this->plugin_options['config']['product_category_taxonomy'] );
                }
            }
            //if ( !empty( $product['Images'] ) & !$sync ) {
            //    $thisImageCollection = $product['Images'];
            //    $attachment_ids      = array();
            //    foreach ( $thisImageCollection as $thisImage ) {
            //        $attachment_id = ltd_generate_featured_image( $thisImage['Url'], $post_id );
            //        if ( $attachment_id ) {
            //            $attachment_ids[] = $attachment_id;
            //        }
            //    }
            //    if ( count( $attachment_ids ) > 0 )
            //        update_post_meta( $post_id, 'image_gallery', $attachment_ids );
            //}

            //if ( !empty( $product['MultimediaContent'] ) & !$sync ) {
            //    $thisMediaCollection = $product['MultimediaContent'];
            //    $mediaGroup          = array();
            //    foreach ( $thisMediaCollection as $thisMedia ) {
            //        $arrMedia["media_type"] = $thisMedia['Type'];
            //        $arrMedia["media_url"]  = $thisMedia['Url'];
            //        array_push( $mediaGroup, $arrMedia );
            //    }
            //    if ( count( $mediaGroup ) > 0 )
            //        update_post_meta( $post_id, 'multimedia', $mediaGroup );
            //}
            return true;
        }
        catch ( Exception $e ) {
            $this->Log( array(
                 'type' => 'ERROR',
                'message' => $e->getMessage(),
            ) );
        }
        return false;
    }


    /*
     * ===========================
     * Import and Sync Categories
     * ===========================
     */


    public function import_categories( $delete_existing = false )
    {

        $categories = $this->integration->fetch_product_types();
        if ($categories === false) return 0;
        $i = 0;
        foreach ( $categories as $category ) {
            $term = $category['EventTypeName'];
            $term = str_replace( '&', 'and', $term );

            $slug = strtolower( $term );
            $slug = post_slug( $slug );
            $tax  = $this->plugin_options['config']['product_category_taxonomy'];
            $args = array(
                 'description' => $term . ' Tickets',
                'slug' => $slug
            );


            $checkTerm = term_exists( $term, $tax );
            if ( $checkTerm !== 0 && $checkTerm !== NULL ) {
                if ( !$delete_existing )
                    continue;
                $bool = delete_term_meta( $checkTerm['term_id'], 'event_type_id' );
                $bool = wp_delete_term( $checkTerm['term_id'], $tax );
            }

            $return = wp_insert_term( $term, $tax, $args );
            if ( is_wp_error( $return ) ) {
                $this->Log( array(
                     'type' => 'ERROR',
                    'message' => $return->get_error_message()
                ) );
                continue;
            }
            $return = add_term_meta( $return['term_id'], 'event_type_id', $category['EventTypeId'], true );
            if ( is_wp_error( $return ) ) {
                $this->Log( array(
                     'type' => 'ERROR',
                    'message' => $return->get_error_message()
                ) );
                continue;
            } else {
                $i++;
            }
        }
        return $i;
    }


    public function sync_categories()
    {

        return $this->import_categories( false );

    }


    /*
     * =============================
     * Import and Sync Performances
     * =============================
     */

    public function import_performances( $product_ids = array() )
    {
        $products = $this->integration->fetch_products();
        if ($products === false) return 0;
        $count    = 0;
        if ( !empty( $products ) ) {
            foreach ( $products as $product ) {
                if ( !empty( $product_ids ) && !in_array( $product['EventId'], $product_ids ) )
                    continue;
                $count += $this->import_performances_for_product( $product["EventId"] );
            }
        } else {
            $this->Log( array(
                 'type' => 'ERROR',
                'message' => "No Products returned from API."
            ) );
        }
        return $count;
    }


    public function import_performances_for_product( $product_id )
    {
        $performances = $this->api->fetch_product_performances( $product_id );
        $count        = 0;
        if ( !empty( $performances ) ) {
            foreach ( $performances as $performance ) {
                $count += 1;
                $table_name            = $this->db->prefix . "ukds_performances";
                $performance_date_time = str_replace( "T", " ", $performance['PerformanceDate'] );
                $performance_id        = $performance['PerformanceId'];
                $row_array             = array(
                     'product_id' => $product_id,
                    'performance_id' => $performance_id,
                    'performance_date_time' => $performance_date_time,
                    'ticket_count' => $performance['TicketCount'],
                    'total_available_tickets' => $performance['TotalAvailableTickesCount'],
                    'contains_discount_offer_tickets' => ( $performance['ContainsDiscountOfferTickets'] == false ? 0 : 1 ),
                    'contains_no_fee_offer_tickets' => ( $performance['ContainsNoFeeOfferTickets'] == false ? 0 : 1 ),
                    'minimum_ticket_price' => $performance['MinimumTicketPrice'],
                    'maximum_consecutive_seats' => $performance['MaximumConsecutiveSeatsCount']
                );

                // CHECK IF THE PERFORMANCE EXISTS
                $result = $this->db->get_row( "SELECT * FROM $table_name WHERE product_id = $product_id AND performance_id = $performance_id" );
                if ( null !== $result ) {

                    // UPDATE EXISTING ROW WITH LATEST DATA
                    $this->db->update( $table_name, $row_array, array(
                         'performance_id' => $row_array['performance_id']
                    ) );

                } else {

                    // INSERT THE PERFORMANCE
                    $this->db->insert( $table_name, $row_array );

                }
            }
            $this->Log( array(
                 'type' => 'INFO',
                'message' => "Performances updated for Product. EventId = " . $product_id
            ) );
        } else {
            $this->Log( array(
                 'type' => 'ERROR',
                'message' => "No Performances found for Product: EventId = " . $product_id
            ) );
        }
        return $count;
    }


    public function sync_performances()
    {

    }


    public function sync_performances_for_product( $product_id )
    {

    }

}
