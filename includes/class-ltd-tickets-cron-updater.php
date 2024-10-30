<?php

/**
 * Scheduled task controller.
 *
 * Handles the cron jobs and background update processes.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */

class Ltd_Tickets_Cron_Updator {

    protected $plugin_name;
	protected $version;
    protected $plugin_options;
    public function __construct() {
		$this->plugin_name = LTD_PLUGIN_NAME;
		$this->version = LTD_PLUGIN_VERSION;
        $this->plugin_options = get_option($this->plugin_name);
        add_filter( 'cron_schedules', array($this, 'weekly_cron_schedule') );
        add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar' ), 100 );
		add_action( 'init', array( $this, 'ltd_process_handler' ) );
        add_action( 'ltd_tickets_plugin_cron', array($this, 'ltd_update_all') );

    }

   public function init() {


        require_once plugin_dir_path( __FILE__ ) . 'updaters/class-ltd-tickets-background-process-update.php';
        $this->process_update_all = new LTD_Tickets_Background_Process_Update();


        if ($this->plugin_options['sync']['update_frequency'] != "none") {
            if ( ! wp_next_scheduled( 'ltd_tickets_plugin_cron' ) ) {
                    wp_schedule_event(
                    time() + 10,
                    $this->plugin_options['sync']['update_frequency'],
                    'ltd_tickets_plugin_cron'
                );
            }
        }



	}

   function weekly_cron_schedule( $schedules ) {
        $schedules[ 'weekly' ] = array(
            'interval' => 60 * 60 * 24 * 7, # 604,800, seconds in a week
            'display' => __( 'Weekly' ) );
        return $schedules;
    }


    public function admin_bar( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}


            $wp_admin_bar->add_menu( array(
			    'id'    => $this->plugin_name,
			    'title' => __( 'LTD Tickets', $this->plugin_name ),
			    'href'  => '#',
		    ));

            $wp_admin_bar->add_menu( array(
                'parent' => $this->plugin_name,
                'id'     => $this->plugin_name . '-products',
                'title'  => __( 'Products', $this->plugin_name ),
                'href'   => admin_url( 'edit.php?post_type=' . $this->plugin_options['config']['product_post_type']),
            ));

            $wp_admin_bar->add_menu( array(
                'parent' => $this->plugin_name,
                'id'     => $this->plugin_name . '-venues',
                'title'  => __( 'Venues', $this->plugin_name ),
                'href'   => admin_url( 'edit.php?post_type=' . $this->plugin_options['config']['venue_post_type']),
            ));


         if ($this->plugin_options['config']['partner_type'] == 'api') {

            $wp_admin_bar->add_menu( array(
                'parent' => $this->plugin_name,
                'id'     => $this->plugin_name . '-sync',
                'title'  => __( 'Sync Now', $this->plugin_name ),
                'href'   => wp_nonce_url( admin_url( '?page=' . $this->plugin_name . '&sync=all'), 'sync' ),
            ));
        }

	}


    public function ltd_process_handler() {
		if ( ! isset( $_GET['sync'] ) || ! isset( $_GET['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'sync') ) {
			return;
		}

		if ( 'all' === $_GET['sync'] ) {
			$this->ltd_update_all();
		}
	}


    public function ltd_update_all() {

        if ($this->plugin_options['sync']['import_categories'] == 1) {
            $this->process_update_all->push_to_queue( array('import_categories') );
        }
        if ($this->plugin_options['sync']['import_venues'] == 1) {
            $this->process_update_all->push_to_queue( array('import_venues') );
        }
        if ($this->plugin_options['sync']['import_products'] == 1) {
            $this->process_update_all->push_to_queue( array('import_products') );
        }

        $this->process_update_all->push_to_queue( array('sync_categories') );
        $this->process_update_all->push_to_queue( array('sync_venues') );
        $this->process_update_all->push_to_queue( array('sync_products') );

        $this->process_update_all->save()->dispatch();


    }



}
new Ltd_Tickets_Cron_Updator();