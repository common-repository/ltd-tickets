<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/admin
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */
class Ltd_Tickets_Admin {

	private $plugin_name;
	private $version;


    use LTD_Tickets_Logging;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/updaters/class-ltd-tickets-background-process-update.php' );
        $this->process_update_all = new LTD_Tickets_Background_Process_Update();

	}


    public function admin_init() {
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-defaults.php' );
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ltd-post-types.php' );
		require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ltd-taxonomies.php' );
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-import-notice.php' );

    }


    public function ltd_setup() {
       add_filter('image_resize_dimensions', 'ltd_image_crop_dimensions', 10, 6);
       add_image_size( 'ltd-product-image', 480, 325, true );

    }


    public function enqueue_styles($hook) {

        if ( 'toplevel_page_ltd-tickets' == $hook ||
            'theatre-tickets_page_ltd-tickets-synchonisation' == $hook) {
            wp_enqueue_style( $this->plugin_name . "dataTables", plugin_dir_url( __FILE__ ) . 'css/jquery.dataTables.min.css', array(), $this->version, 'all' );
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ltd-tickets-admin.css', array('wp-color-picker'), $this->version, 'all' );
	}


	public function enqueue_scripts($hook) {

        if ( 'toplevel_page_ltd-tickets' == $hook ||
            'theatre-tickets_page_ltd-tickets-synchonisation' == $hook) {
    		wp_enqueue_script( $this->plugin_name . "dataTables", plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
        }

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ltd-tickets-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );

        wp_enqueue_script( $this->plugin_name . "core", plugin_dir_url( __FILE__ ) . 'js/core.min.js', array( 'jquery'), $this->version, false );

        wp_enqueue_script( $this->plugin_name . "ltd", plugin_dir_url( __DIR__ ) .  'public/js/ltd.lib.js', array( 'jquery'), $this->version, false );

		wp_enqueue_script( 'ltd-ajax-handler', plugin_dir_url( __FILE__ ) . 'js/ltd-tickets-ajax.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( 'ltd-ajax-handler', 'ukdsAjaxHandler', array (
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'nonce'		=> wp_create_nonce( 'ukds-ajax-handler-nonce' ),
		));
	}

    public function add_plugin_admin_menu() {

        add_menu_page(
			'London Theatre Direct Tickets Integration',
			'Theatre Tickets',
			'manage_options',
			$this->plugin_name,
			array($this, 'ltd_display_plugin_setup_page'),
			'dashicons-tickets');


        add_submenu_page(
            $this->plugin_name,
            'London Theatre Direct Synchonisation',
            'Data Manager',
            'manage_options',
            $this->plugin_name . '-synchonisation',
			array($this, 'ltd_display_plugin_synchronisation_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'London Theatre Direct Shortcodes',
            'Shortcode Builder',
            'manage_options',
            $this->plugin_name . '-shortcodes',
			array($this, 'ltd_display_plugin_shortcodes_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'London Theatre Direct Log',
            'Log',
            'manage_options',
            $this->plugin_name . '-log',
			array($this, 'ltd_display_plugin_log_page')
        );

    }


	public function add_action_links( $links ) {

		$settings_link = array(
		'<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
	   );
	   return array_merge(  $settings_link, $links );

	}

    public function ltd_display_plugin_log_page() {

        require_once( 'partials/ltd-tickets-log-display.php' );

    }

    public function ltd_display_plugin_synchronisation_page() {

        require_once( 'partials/ltd-tickets-synchronisation-display.php' );

    }

    public function ltd_display_plugin_shortcodes_page() {

        require_once( 'partials/ltd-tickets-shortcodes-display.php' );

    }

    public function ltd_display_plugin_setup_page() {

		require_once( 'partials/ltd-tickets-admin-display.php' );

	}


    public function options_update() {

        $LTD_Tickets_Data_Sync = new LTD_Tickets_Data_Sync($this->plugin_name, $this->version);

		register_setting($this->plugin_name, $this->plugin_name, array($this, 'ltd_validate'));

        $imports = array ();
        $updates = array ();
        $async_fn = "";

        if ( isset( $_POST['ImportAll'] ) && !empty( $_POST['ImportAll']) ) {
            $async_fn = "import_all";
        };

        if ( isset( $_POST['ImportProducts'] ) && !empty( $_POST['ImportProducts']) ) {
            $async_fn = "import_products";
        };

        if ( isset( $_POST['ImportVenues'] ) && !empty( $_POST['ImportVenues']) ) {
            $async_fn = "import_venues";
        };

        if ( isset( $_POST['UpdateAll'] ) && !empty( $_POST['UpdateAll']) ) {
            $async_fn = "sync_all";
        };

        if ( isset( $_POST['UpdateProducts'] ) && !empty( $_POST['UpdateProducts']) ) {
            $async_fn = "sync_products";
        };

        if ( isset( $_POST['UpdateVenues'] ) && !empty( $_POST['UpdateVenues']) ) {
            $async_fn = "sync_venues";
        };



        if ($async_fn != "") {
            $this->process_update_all->push_to_queue( array($async_fn) );
            $this->process_update_all->save()->dispatch();
            new LTD_Tickets_Import_Notice( $async_fn, "background", $this->plugin_name );
        }

        if ( isset( $_POST['ImportSelectedProducts'] ) && !empty( $_POST['ImportSelectedProducts']) ) {
            $importArray = array();
            foreach ( $_POST as $key => $value ) {
                if ( strstr( $key, 'product-' ) ) {
                    $x = str_replace( 'product-', '', $key );
                    array_push( $importArray, $x );
                }
            }
            $this->process_update_all->push_to_queue( array('import_selected_products', $importArray ) );
            $this->process_update_all->save()->dispatch();
            new LTD_Tickets_Import_Notice('import_selected_products', "background", $this->plugin_name );
        };

        if ( isset( $_POST['ImportSelectedVenues'] ) && !empty( $_POST['ImportSelectedVenues']) ) {
            $importArray = array();
            foreach ( $_POST as $key => $value ) {
                if ( strstr( $key, 'venue-' ) ) {
                    $x = str_replace( 'venue-', '', $key );
                    array_push( $importArray, $x );
                }
            }
            $this->process_update_all->push_to_queue( array('import_selected_venues', $importArray ) );
            $this->process_update_all->save()->dispatch();
            new LTD_Tickets_Import_Notice('import_selected_venues', "background", $this->plugin_name );
        };


        if ( isset( $_POST['UpdateSelectedProducts'] ) && !empty( $_POST['UpdateSelectedProducts'] ) )  $updates['products'] = $LTD_Tickets_Data_Sync->sync_selected_products();

        if ( isset( $_POST['UpdateSelectedVenues'] ) && !empty( $_POST['UpdateSelectedVenues'] ) )      $updates['venues'] = $LTD_Tickets_Data_Sync->sync_selected_venues();

        if (isset( $_POST['ImportCategories'] ) && !empty($_POST['ImportCategories'] ) ) $imports['categories'] =  $LTD_Tickets_Data_Sync->import_categories();

        if (!empty($imports)) {
            new LTD_Tickets_Import_Notice($imports, "import", $this->plugin_name );
        }
        if (!empty($updates)) {
            new LTD_Tickets_Import_Notice($updates, "update", $this->plugin_name );
        }

        if ( isset( $_POST['ClearLog'] ) && !empty( $_POST['ClearLog'] ) )  $this->ClearLog();

    }


    public function ltd_validate($input) {

        $options = get_option($this->plugin_name);

        if ( $_POST['admin_page'] == 'config' ) {

            $partner_type                                       = $options['config']['partner_type'];
            $api_key_sandbox                                    = $options['partner']['api_key_sandbox'];
            $api_key_live                                       = $options['partner']['api_key_live'];

            // Validate config keys
            $options['config']['partner_type']                  = esc_html($input['config_partner_type']);
            $options['config']['api_target']                    = esc_html($input['config_api_target']);
            $options['config']['redirect_time']                 = (empty($input['config_redirect_time']) ? 1 : (int)$input['config_redirect_time']);
            $options['config']['disable_styles']                = (empty($input['config_disable_styles']) ? 0 : (int)$input['config_disable_styles']);

            $options['partner']['whitelabel_id']                = esc_html($input['partner_whitelabel_id']);
            $options['partner']['awin_id']                      = esc_html($input["partner_awin_id"]);
            $options['partner']['awin_clickref']                = esc_html($input["partner_awin_clickref"]);
            $options['partner']['api_key_live']                 = esc_html($input['partner_api_key_live']);
            $options['partner']['api_key_sandbox']              = esc_html($input['partner_api_key_sandbox']);

            $options['config']['api_user']                      = ($options['config']['partner_type'] == "api" ? 'partner' : 'default');

            // Validate style keys
            $options['styles']['layout']                         = esc_html($input['style_layout']);
            $options['styles']['layout_max_width']               = esc_html($input['style_max_width']);
            $options['styles']['custom_css']                     = esc_html($input['style_custom_css']);
            $options['styles']['primary_colour']                 = esc_html($input['style_primary_colour']);
            $options['styles']['secondary_colour']               = esc_html($input['style_secondary_colour']);
            $options['styles']['primary_button_background']      = esc_html($input['style_primary_button_background']);
            $options['styles']['primary_button_text_colour']     = esc_html($input['style_primary_button_text_colour']);
            $options['styles']['primary_button_css_class']       = esc_html($input['style_primary_button_css_class']);
            $options['styles']['secondary_button_background']    = esc_html($input['style_secondary_button_background']);
            $options['styles']['secondary_button_text_colour']   = esc_html($input['style_secondary_button_text_colour']);
            $options['styles']['secondary_button_css_class']     = esc_html($input['style_secondary_button_css_class']);


            // Reluctantly update advanced options
            if (!empty($input['config_product_post_type']))         $options['config']['product_post_type']         = esc_html($input['config_product_post_type']);
            if (!empty($input['config_product_category_taxonomy'])) $options['config']['product_category_taxonomy'] = esc_html($input['config_product_category_taxonomy']);
            if (!empty($input['config_venue_post_type']))           $options['config']['venue_post_type']           = esc_html($input['config_venue_post_type']);

            if (!empty($input['template_product_template']))        $options['templates']['product_template']       = esc_html($input['template_product_template']);
            if (!empty($input['template_product_archive']))         $options['templates']['product_archive']        = esc_html($input['template_product_archive']);
            if (!empty($input['template_venue_template']))          $options['templates']['venue_template']         = esc_html($input['template_venue_template']);
            if (!empty($input['template_venue_archive']))           $options['templates']['venue_archive']          = esc_html($input['template_venue_archive']);
            if (!empty($input['template_category_template']))       $options['templates']['category_template']      = esc_html($input['template_category_template']);
            if (!empty($input['template_booking_template']))        $options['templates']['booking_template']       = esc_html($input['template_booking_template']);
            if (!empty($input['template_basket_template']))         $options['templates']['basket_template']        = esc_html($input['template_basket_template']);
            if (!empty($input['template_checkout_template']))       $options['templates']['checkout_template']      = esc_html($input['template_checkout_template']);
            if (!empty($input['template_confirmation_template']))   $options['templates']['confirmation_template']  = esc_html($input['template_confirmation_template']);


            if (($partner_type != $input['config_partner_type'] ||
                $api_key_live != $input['partner_api_key_live'] ||
                $api_key_sandbox != $input['partner_api_key_sandbox'] ) &&
                ($input['config_partner_type'] == "api")) {
                $options['partner']['api_key_host'] = ltd_get_api_host($options[$options['config']['api_user']]['api_key_' . $options['config']['api_target']]);
            }

            if ( isset( $_POST['RestoreDefaults'] ) && !empty( $_POST['RestoreDefaults'] ) ) {
                $options['templates'] = Ltd_Tickets_Defaults::get_template_defaults();
                $options['config']['product_post_type'] = 'ukds-products';
                $options['config']['product_category_taxonomy'] = 'ukds-product-category';
                $options['config']['venue_post_type'] = 'ukds-venues';
            }

        } elseif ($_POST['admin_page'] == 'sync') {

            if (!empty($input['import_products_status']))           $options['sync']['import_products_status']      = esc_html($input['import_products_status']);
            if (!empty($input['import_venues_status']))             $options['sync']['import_venues_status']        = esc_html($input['import_venues_status']);

            if ( !empty($input['update_frequency']) &&  ( $options['sync']['update_frequency'] != $input['update_frequency'] ) ) {
                wp_clear_scheduled_hook( 'ltd_tickets_plugin_cron' );
                $options['sync']['update_frequency']                = esc_html($input['update_frequency']);
            }

            $options['sync']['import_products']                     = (!empty($input['import_products']) ? 1 : 0);
            $options['sync']['import_venues']                       = (!empty($input['import_venues']) ? 1 : 0);
            $options['sync']['import_categories']                   = (!empty($input['import_categories']) ? 1 : 0);

            $options['sync']['product_update_title']                = (!empty($input['product_update_title']) ? 1 : 0);
            $options['sync']['product_update_content']              = (!empty($input['product_update_content']) ? 1 : 0);
            $options['sync']['product_update_running_time']         = (!empty($input['product_update_running_time']) ? 1 : 0);
            $options['sync']['product_update_minimum_age']          = (!empty($input['product_update_minimum_age']) ? 1 : 0);
            $options['sync']['product_update_start_date']           = (!empty($input['product_update_start_date']) ? 1 : 0);
            $options['sync']['product_update_end_date']             = (!empty($input['product_update_end_date']) ? 1 : 0);
            $options['sync']['product_update_tagline']              = (!empty($input['product_update_tagline']) ? 1 : 0);
            $options['sync']['product_update_important_notice']     = (!empty($input['product_update_important_notice']) ? 1 : 0);
            $options['sync']['product_update_minimum_price']        = (!empty($input['product_update_minimum_price']) ? 1 : 0);
            $options['sync']['product_update_current_price']        = (!empty($input['product_update_current_price']) ? 1 : 0);
            $options['sync']['product_update_offer_price']          = (!empty($input['product_update_offer_price']) ? 1 : 0);
            $options['sync']['product_update_short_offer_text']     = (!empty($input['product_update_short_offer_text']) ? 1 : 0);
            $options['sync']['product_update_long_offer_text']      = (!empty($input['product_update_long_offer_text']) ? 1 : 0);
            $options['sync']['product_update_main_image_url']       = (!empty($input['product_update_main_image_url']) ? 1 : 0);
            $options['sync']['product_update_image_gallery']        = (!empty($input['product_update_image_gallery']) ? 1 : 0);


            $options['sync']['venue_update_title']                  = (!empty($input['venue_update_title']) ? 1 : 0);
            $options['sync']['venue_update_content']                = (!empty($input['venue_update_content']) ? 1 : 0);
            $options['sync']['venue_update_address']                = (!empty($input['venue_update_address']) ? 1 : 0);
            $options['sync']['venue_update_city']                   = (!empty($input['venue_update_city']) ? 1 : 0);
            $options['sync']['venue_update_postcode']               = (!empty($input['venue_update_postcode']) ? 1 : 0);
            $options['sync']['venue_update_nearest_tube']           = (!empty($input['venue_update_nearest_tube']) ? 1 : 0);
            $options['sync']['venue_update_nearest_train']          = (!empty($input['venue_update_nearest_train']) ? 1 : 0);
            $options['sync']['venue_update_seating_plan']           = (!empty($input['venue_update_seating_plan']) ? 1 : 0);
            $options['sync']['venue_update_image_url']              = (!empty($input['venue_update_image_url']) ? 1 : 0);

        }

		return $options;

	 }

}
