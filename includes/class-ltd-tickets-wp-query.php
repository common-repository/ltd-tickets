<?php

/**
 * WP Query Updates.
 *
 * Hooks the 'pre_get_posts' action to update the main query. This is used
 * to prevent the plugin shows products with end dates in the past. The
 * query is also updated if the front page is set to the product archive.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */
class LTD_Tickets_WP_Query
{
    use LTD_Tickets_Logging;

    private $plugin_name;
    private $version;
    public $plugin_options;


    function __construct() {
        $this->plugin_name = LTD_PLUGIN_NAME;
        $this->version = LTD_PLUGIN_VERSION;
        $this->plugin_options = get_option( $this->plugin_name );
        $this->init();
    }


    function init() {
        add_action("pre_get_posts", array($this, "ltd_query_reset"), 10);
        add_action("pre_get_posts", array($this, "ltd_apply_daterestriction"), 10);
        add_filter( 'query_vars',  array($this, 'add_query_vars_filter') );

    }

    public function ltd_query_reset($query) {

        if ( ! $query->is_main_query() ) {
			return;
		}

        if ( $query->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $query->get( 'page_id' ) ) === $this->plugin_options['config']['product_archive'] ) {

            $query->set('post_type', array($this->plugin_options['config']['product_post_type']) );

            if ( isset( $query->query['paged'] ) ) {
                $query->set( 'paged', $query->query['paged'] );
            }

            $query->set( 'page_id', '' );

            global $wp_post_types;

            $product_archive_page 	= get_post( $this->plugin_options['config']['product_archive'] );

            $wp_post_types[$this->plugin_options['config']['product_post_type']]->ID 			= $product_archive_page->ID;
            $wp_post_types[$this->plugin_options['config']['product_post_type']]->post_title 	= $product_archive_page->post_title;
            $wp_post_types[$this->plugin_options['config']['product_post_type']]->post_name 	= $product_archive_page->post_name;
            $wp_post_types[$this->plugin_options['config']['product_post_type']]->post_type    = $product_archive_page->post_type;
            $wp_post_types[$this->plugin_options['config']['product_post_type']]->ancestors    = get_ancestors( $product_archive_page->ID, $product_archive_page->post_type );

            $query->is_singular          = false;
            $query->is_post_type_archive = true;
            $query->is_archive           = true;
            $query->is_page              = true;

            add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

        }


    }

    public function ltd_apply_daterestriction($query) {
        if ((!is_admin() &! defined( 'DOING_CRON' ))
           && (
                (
                    in_array ( $query->get('post_type'), array($this->plugin_options['config']['product_post_type']))
                    ||
                    ($query->get($this->plugin_options['config']['product_category_taxonomy']) != "")
                )
                &! ($query->is_main_query() && is_single())
            ) ||
            ($query->is_main_query() && 'page' === get_option( 'show_on_front' ) && $query->get( 'page_id' )  === '' )
            &! ($query->get('ukds_ignore_restrictions') == true)
            && empty($query->get('post__in'))
           ) {
            $query->set('meta_query', array(
               array(
                 'key' => 'start_date'
               ),
               array(
                   'key'           => 'end_date',
                   'value'         => date("Y-m-d"),
                   'compare'       => '>=',
                   'type'          => 'DATE'
                   )
               )
           );



            $ltd_get_product_per_page = "";

            if (!empty($_POST['ItemsPerPage']) && $_POST['ItemsPerPage'] != "") {
                $ltd_get_product_per_page = esc_html($_POST['ItemsPerPage']);
                LTD_Tickets_Cookies::set("product_per_page", $ltd_get_product_per_page);
                $query->set('paged', 0);
            } else {
                $ltd_get_product_per_page = LTD_Tickets_Cookies::get('product_per_page');
            }
            if ($ltd_get_product_per_page == "") $ltd_get_product_per_page = 12;

            $query->set( 'ltd_product_per_page', $ltd_get_product_per_page);
            $query->set( 'posts_per_page', $ltd_get_product_per_page);


            if (is_post_type_archive($this->plugin_options['config']['product_post_type']) ||
                is_tax($this->plugin_options['config']['product_category_taxonomy'])) {

                $order = "ASC";
                $order_by = "title";
                $meta_key = "";
                $meta_type = "";


                $ltd_get_product_order = "";
                if (!empty($_POST['ItemOrder']) && $_POST['ItemOrder'] != "") {
                    $ltd_get_product_order = $_POST['ItemOrder'];
                    LTD_Tickets_Cookies::set("product_order", esc_html($_POST['ItemOrder']));
                } else {
                    $ltd_get_product_order = LTD_Tickets_Cookies::get('product_order');
                }

                if ($ltd_get_product_order != "") {
                    switch ($ltd_get_product_order) {
                        case "OrderAlphaAsc" :
                            $order = "ASC";
                            break;
                        case "OrderAlphaDesc" :
                            $order = "DESC";
                            break;
                        case "OrderPriceAsc" :
                            $order = "ASC";
                            $order_by = 'meta_value';
                            $meta_key = 'minimum_price';
                            $meta_type = 'DECIMAL';
                            break;
                        case "OrderPriceDesc" :
                            $order = "DESC";
                            $order_by = 'meta_value';
                            $meta_key = 'minimum_price';
                            $meta_type = 'DECIMAL';
                            break;
                        case "OrderEndingSoon" :
                            $order = "ASC";
                            $order_by = 'meta_value';
                            $meta_key = 'end_date';
                            $meta_type = 'DATE';
                            break;
                        case "OrderComingSoon" :
                            $order = "ASC";
                            $order_by = 'meta_value';
                            $meta_key = 'start_date';
                            $meta_type = 'DECIMAL';
                            break;
                    }
                    $query->set( 'ltd_product_order', $ltd_get_product_order);
                }

                $query->set( 'order', $order);
                $query->set( 'orderby', $order_by);
                if ($meta_key != "") {
                    $query->set( 'meta_key', $meta_key);

                }
                if ($meta_type != "") {
                    $query->set( 'meta_type', $meta_type);

                }
            }
        } else if (!is_admin()
           && (
               in_array ( $query->get('post_type'), array($this->plugin_options['config']['venue_post_type']))
           )
           &! ($query->is_main_query() && is_single())
           && empty($query->get('post__in'))
           && is_post_type_archive($this->plugin_options['config']['venue_post_type'])
           ) {

            if (!empty($_POST['ItemsPerPage']) && $_POST['ItemsPerPage'] != "") {
                $ltd_get_venue_per_page = esc_html($_POST['ItemsPerPage']);
                LTD_Tickets_Cookies::set("venue_per_page", $ltd_get_venue_per_page);
                $query->set('paged', 0);
            } else {
                $ltd_get_venue_per_page = LTD_Tickets_Cookies::get('venue_per_page');
            }
            if ($ltd_get_venue_per_page == "") $ltd_get_venue_per_page = 12;

            $query->set( 'ltd_venue_per_page', $ltd_get_venue_per_page);
            $query->set( 'posts_per_page', $ltd_get_venue_per_page);


            $order = "ASC";
            $order_by = "title";
            $meta_key = "";
            $meta_type = "";

            $ltd_get_venue_order = "";
            if (!empty($_POST['ItemOrder']) && $_POST['ItemOrder'] != "") {
                $ltd_get_venue_order = $_POST['ItemOrder'];
                LTD_Tickets_Cookies::set("venue_order", esc_html($_POST['ItemOrder']));
            } else {
                $ltd_get_venue_order = LTD_Tickets_Cookies::get('venue_order');
            }
            if ($ltd_get_venue_order != "") {
                switch ($ltd_get_venue_order) {
                    case "OrderAlphaAsc" :
                        $order = "ASC";
                        break;
                    case "OrderAlphaDesc" :
                        $order = "DESC";
                        break;
                }
                $query->set( 'ltd_venue_order', $ltd_get_venue_order);
            }

            $query->set( 'order', $order);
            $query->set( 'orderby', $order_by);
            if ($meta_key != "") {
                $query->set( 'meta_key', $meta_key);

            }
            if ($meta_type != "") {
                $query->set( 'meta_type', $meta_type);

            }
        }




    }


    public function add_query_vars_filter( $vars ){
      $vars[] = "ltd_product_order";
      $vars[] = "ltd_product_per_page";
      $vars[] = "ltd_venue_order";
      $vars[] = "ltd_venue_per_page";
      return $vars;
    }


}

new LTD_Tickets_WP_Query();