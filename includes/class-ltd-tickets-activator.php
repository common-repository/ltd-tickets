<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */
class Ltd_Tickets_Activator {
    protected $plugin_name;
	protected $version;
    public function __construct() {
		$this->plugin_name = LTD_PLUGIN_NAME;
		$this->version = LTD_PLUGIN_VERSION;
        $this->activate();
    }



    private function checkop() {

        $exists = false;
        $options = array();
        $defaults = array();
        $defaults['version']     =       $this->version;
        $defaults['api']         =       Ltd_Tickets_Defaults::get_api_defaults();
        $defaults['default']     =       Ltd_Tickets_Defaults::get_default_defaults();
        $defaults['config']      =       Ltd_Tickets_Defaults::get_config_defaults();
        $defaults['templates']   =       Ltd_Tickets_Defaults::get_template_defaults();
        $defaults['sync']        =       Ltd_Tickets_Defaults::get_sync_defaults();
        $defaults['partner']     =       Ltd_Tickets_Defaults::get_partner_defaults();
        $defaults['styles']       =      Ltd_Tickets_Defaults::get_style_defaults();


        if(!get_option($this->plugin_name)) {

            $options = $defaults;

        } else  {

            $exists = true;
            $options = get_option($this->plugin_name);

            if (!isset($options['version']) || $options['version'] != $this->version) {

                $options['version']     =   $this->version;
                $options['api']         =   wp_parse_args((!isset($options['api']) ? array() : $options['api']), $defaults['api'] );
                $options['default']     =   wp_parse_args((!isset($options['default']) ? array() : $options['default']), $defaults['default'] );
                $options['config']      =   wp_parse_args((!isset($options['config']) ? array() : $options['config']), $defaults['config'] );
                $options['templates']   =   wp_parse_args((!isset($options['templates']) ? array() : $options['templates']), $defaults['templates'] );
                $options['sync']        =   wp_parse_args((!isset($options['sync']) ? array() : $options['sync']), $defaults['sync'] );
                $options['partner']     =   wp_parse_args((!isset($options['partner']) ? array() : $options['partner']), $defaults['partner']);
                $options['styles']      =   wp_parse_args((!isset($options['styles']) ? array() : $options['styles']), $defaults['styles']);

            }

        }


        if (!isset($options['config']['product_archive'])) :
            $new_page_title = 'Tickets';
            $page_check = get_page_by_title($new_page_title);
            $new_page = array(
                    'post_type' => 'page',
                    'post_title' => $new_page_title,
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_author' => 1,
            );
            if(!isset($page_check->ID) || (isset($page_check->ID) && $page_check->post_status != 'publish')){
                $new_page_id = wp_insert_post($new_page);
                if (!is_wp_error($new_page_id)) {
                    $options['config']['product_archive'] = $new_page_id;
                }
            } else {
                $options['config']['product_archive'] = $page_check->ID;
            }
        endif;


        if (!isset($options['config']['venue_archive'])) :
            $new_page_title = 'Venues';
            $page_check = get_page_by_title($new_page_title);
            $new_page = array(
                    'post_type' => 'page',
                    'post_title' => $new_page_title,
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_author' => 1,
            );
            if(!isset($page_check->ID) || (isset($page_check->ID) && $page_check->post_status != 'publish')){
                $new_page_id = wp_insert_post($new_page);
                if (!is_wp_error($new_page_id)) {
                    $options['config']['venue_archive'] = $new_page_id;
                }
            } else {
                $options['config']['venue_archive'] = $page_check->ID;
            }
        endif;

        if ($exists) {
            update_option($this->plugin_name, $options);
        } else {
            add_option($this->plugin_name, $options);
        }
    }

	/**
	 * Plugin Activation Function.
	 *
	 * Creates performance database tables and plugin default options.
	 *
	 * @since    1.0.0
	 */
	public function activate() {
       global $wpdb;
       require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

       $performances_table = $wpdb->prefix . "ltd_performances";

       $charset_collate = $wpdb->get_charset_collate();

       $sql = "CREATE TABLE IF NOT EXISTS $performances_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_id int(32) NOT NULL,
        performance_id int(32) NOT NULL,
        performance_date_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        ticket_count mediumint(9) NOT NULL,
        total_available_tickets mediumint(9) NOT NULL,
        contains_discount_offer_tickets tinyint(1) DEFAULT 0 NOT NULL,
        contains_no_fee_offer_tickets tinyint(1) DEFAULT 0 NOT NULL,
        minimum_ticket_price numeric(15,2) NOT NULL,
        maximum_consecutive_seats mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        $log_table = $wpdb->prefix . "ltd_log";
        $sql.= "CREATE TABLE IF NOT EXISTS $log_table (
          idx int(32) NOT NULL AUTO_INCREMENT,
          type varchar(45) NOT NULL DEFAULT 'INFO',
          message text NOT NULL,
          stack blob NOT NULL,
          url varchar(255) NOT NULL,
          user_id int(16) NOT NULL DEFAULT '0',
          basket_id char(36) NOT NULL,
          ip char(15) NOT NULL DEFAULT '0.0.0.0',
          user_agent varchar(255) NOT NULL,
          timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          PRIMARY KEY (idx)
        ) $charset_collate;";

        dbDelta( $sql );
        $this->checkop();
	}
}
