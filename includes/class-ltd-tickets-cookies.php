<?php
/**
 * Admin Notice for imported and synced data.
 *
 * Handles the admin notice indicating imported and update posts and taxonomies.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class LTD_Tickets_Cookies {

    public static function get($cookie_name) {
        if (isset($_COOKIE[LTD_PLUGIN_NAME . "_" .$cookie_name])) {
            return $_COOKIE[LTD_PLUGIN_NAME . "_" .$cookie_name];
        } else {
            return "";
        }
    }

    public static function set($cookie_name, $cookie_value) {
        if (isset($_COOKIE[LTD_PLUGIN_NAME . "_" . $cookie_name])) {
            unset( $_COOKIE[LTD_PLUGIN_NAME . "_" .$cookie_name] );
            setcookie( LTD_PLUGIN_NAME . "_" .$cookie_name, '', time() - ( 15 * 60 ) );
        }
        if ( !is_admin() && !isset($_COOKIE[LTD_PLUGIN_NAME . "_" .$cookie_name])) {
            setcookie( LTD_PLUGIN_NAME . "_" . $cookie_name, $cookie_value, time()+3600*24*100, COOKIEPATH, COOKIE_DOMAIN, false);
        }
    }

}