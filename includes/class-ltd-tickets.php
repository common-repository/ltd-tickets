<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */
class Ltd_Tickets {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {

		$this->plugin_name = LTD_PLUGIN_NAME;
		$this->version = LTD_PLUGIN_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
        $this->define_ajax_hooks();

    }

	private function load_dependencies() {

        // Plugin Logging
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-logging.php';



        if ( ! class_exists( 'WP_Async_Request', false ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/libraries/wp-async-request.php';
        }
        if ( ! class_exists( 'WP_Background_Process', false ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/libraries/wp-background-process.php';
        }



        // Enable Helper Functions
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-cookies.php' );

        // Enable Helper Functions
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ltd-helpers.php' );

        // Integration API
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-integration.php' );

        // Data Sync Class Functions
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-data-sync.php' );

        // Widgets
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ltd-widget-functions.php' );

        // Plugin Cronjobs
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-cron-updater.php';

		// Plugin Actions and Filters
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-loader.php';

        // Language Localisation
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-i18n.php';

        // Ajax Functions
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-ajax.php';

    	/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ltd-tickets-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ltd-tickets-public.php';

		$this->loader = new Ltd_Tickets_Loader();

	}

	private function set_locale() {

		$plugin_i18n = new Ltd_Tickets_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}



    private function define_ajax_hooks() {

        $plugin_ajax = new Ltd_Tickets_Ajax($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action("init", $plugin_ajax, "ajax_init");

    }

	private function define_admin_hooks() {

		$plugin_admin = new Ltd_Tickets_Admin( $this->get_plugin_name(), $this->get_version() );
        $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );


		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		$this->loader->add_action( 'init', $plugin_admin, 'admin_init' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'options_update' );
        $this->loader->add_action( 'after_setup_theme', $plugin_admin, 'ltd_setup' );

	}


	private function define_public_hooks() {

		$plugin_public = new Ltd_Tickets_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'hook_plugin_styles' );
        $this->loader->add_action( 'template_redirect', $plugin_public, 'conditional_redirects' );

    }

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
