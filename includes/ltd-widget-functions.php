<?php

/**
 * UKDS Widget Handler
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/helpers
 * @author     Ben Campbell <ben@ukds.co>
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


include_once( dirname( __FILE__ ) . '/widgets/class-ltd-tickets-list-categories-widget.php' );



function ltd_tickets_register_widgets() {
    register_widget( 'Ltd_Tickets_List_Categories_Widget' );

}
add_action( 'widgets_init', 'ltd_tickets_register_widgets' );

