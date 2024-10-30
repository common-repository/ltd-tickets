<?php

/**
 * Template Loader.
 *
 * Controls which templates are served when plugin pages are requested.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Template Loader
 */
class LTD_Tickets_Template_Loader {

    use LTD_Tickets_Logging;

    private $plugin_name;
	private $version;
    private $plugin_options;

    function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_options = get_option( $plugin_name );
        $this->init();
    }

	public function init() {
		add_filter( 'template_include', array( $this, 'template_loader' ) );
	}


    public function get_page_id( $page ) {

        $page = apply_filters( 'ukds_get_' . $page . '_page_id', $this->plugin_options['templates'][ $page . '_template'] );
        return $page ? absint( $page ) : -1;

    }

	/**
	 * Load a template.
	 */
	public function template_loader( $template ) {

		$find = array( '../ltd-tickets.php' );
		$file = '';

		if ( is_embed() ) {
			return $template;
		}

        if ( is_single() && get_post_type() == $this->plugin_options['config']['product_post_type'] ) {

            $file = $this->plugin_options['templates']['product_template'];
			$find[] = $file;
			$find[] = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file;

		} elseif ( is_single() && get_post_type()  == $this->plugin_options['config']['venue_post_type'] ) {

            $file = $this->plugin_options['templates']['venue_template'];
			$find[] = $file;
			$find[] = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file;

        //} elseif ( is_page( $this->get_page_id( 'booking' ) ) ) {

        //    $file = $this->plugin_options['templates']['booking_template'];
        //    $find[] = $file;
        //    $find[] = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file;

        //} elseif ( is_page( $this->get_page_id( 'checkout' ) ) ) {

        //    $file = $this->plugin_options['templates']['checkout_template'];
        //    $find[] = $file;
        //    $find[] = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file;

        //} elseif ( is_page( $this->get_page_id( 'confirmation' ) ) ) {

        //    $file = $this->plugin_options['templates']['confirmation_template'];
        //    $find[] = $file;
        //    $find[] = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file;

        } elseif ( is_product_taxonomy() ) {
            if ( is_tax( $this->plugin_options['config']['product_category_taxonomy'] ) ) {
                $file = $this->plugin_options['templates']['category_template'];
                $find[] = $file;
                $find[] = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file;
            }
        } elseif ( is_post_type_archive( $this->plugin_options['config']['product_post_type'] ) || is_page( $this->plugin_options['config']['product_archive'] ) ) {
            $file 	= $this->plugin_options['templates']['product_archive'];
            $find[] = $file;
            $find[] = plugin_dir_path( dirname( __FILE__ ) )  . 'templates/' .  $file;

        } elseif ( is_post_type_archive( $this->plugin_options['config']['venue_post_type'] )  || is_page( $this->plugin_options['config']['venue_archive'] ) ) {
            $file 	= $this->plugin_options['templates']['venue_archive'];
            $find[] = $file;
            $find[] = plugin_dir_path( dirname( __FILE__ ) )  . 'templates/' .  $file;
        }

		if ( $file ) {
			$template  = locate_template( array_unique( $find ) );
			if ( ! $template || false) {
				$template = plugin_dir_path( dirname( __FILE__ ) )  . 'templates/' . $file;
			}
		}


		return $template;
	}


}