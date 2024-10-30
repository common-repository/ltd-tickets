<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/public
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */
class Ltd_Tickets_Public {

    use LTD_Tickets_Logging;

	private $plugin_name;
	private $version;
    private $plugin_options;


	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->plugin_options = get_option( $plugin_name );
        $this->includes();
        new LTD_Tickets_Template_Loader($plugin_name,$version);
        new LTD_Tickets_Template_Functions($plugin_name,$version);
	}

	public function includes() {

        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-wp-query.php' );
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-template-functions.php' );
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-template-loader.php' );
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ltd-tickets-shortcodes.php' );

    }


	public function hook_plugin_styles() {

        $primary_colour                     = esc_attr($this->plugin_options['styles']['primary_colour']);
        $secondary_colour                   = esc_attr($this->plugin_options['styles']['secondary_colour']);

        $primary_button_background          = esc_attr($this->plugin_options['styles']['primary_button_background']);
        $primary_button_text_colour         = esc_attr($this->plugin_options['styles']['primary_button_text_colour']);
        $primary_button_css_class           = esc_attr($this->plugin_options['styles']['primary_button_css_class']);

        $secondary_button_background        = esc_attr($this->plugin_options['styles']['secondary_button_background']);
        $secondary_button_text_colour       = esc_attr($this->plugin_options['styles']['secondary_button_text_colour']);
        $secondary_button_css_class         = esc_attr($this->plugin_options['styles']['secondary_button_css_class']);

        $layout_max_width                   = esc_attr($this->plugin_options['styles']['layout_max_width']);


        $custom_css                         = esc_attr($this->plugin_options['styles']['custom_css']);

        $css = "<style type='text/css'>";
        $js = "<script>";

        if ($this->plugin_options['config']['disable_styles'] == 0) {
            $js .= "var ukdsColours = {";
            $js.= "'primaryColour' : '$primary_colour',";
            $js.= "'secondaryColour' : '$secondary_colour',";
            if ($primary_button_css_class == "") {
                $js.= "'primaryButton' : ['$primary_button_background ','$primary_button_text_colour'],";
            } else {
                $js.= "'primaryButton' : '$primary_button_css_class ',";
            }
            if ($secondary_button_css_class == "") {
                $js.= "'secondaryButton' : ['$secondary_button_background ','$secondary_button_text_colour'],";
            } else {
                $js.= "'secondaryButton' : '$secondary_button_css_class ',";
            }
            $js.= "}";


            $primaryButtonText = '#fff';
            $rgb = HTMLToRGB($primary_colour);
            $hsl = RGBToHSL($rgb);
            if($hsl->lightness > 123) {
                $primaryButtonText = '#121212';
            } else {
                $primaryButtonText = '#fff';
            }



            if ($this->plugin_options['styles']['layout'] == "boxed") {
                if ($layout_max_width == "") $layout_max_width = "1170";
                if (is_numeric($layout_max_width)) $layout_max_width.= "px";
                $css.= ".ukds-container {width:auto; max-width:$layout_max_width;}";
            }

            $css.= ".ukds-product-grid-details {border-top:3px solid $primary_colour;}";
            $css.= ".ukds-product-bottom .product-long-offer {border-color:$primary_colour}";
            $css.= ".ukds-product-grid-special, .offer-tag:before, .offer-tag {background:$primary_colour}";
            $css.= ".ukds-product-pagination ul li .page-numbers,.prev.page-numbers:focus, .prev.page-numbers:hover, .next.page-numbers:focus, .next.page-numbers:hover, .offer-text {color:$primary_colour;}";
            $css.= ".ukds-product-pagination ul li .page-numbers.current {background:$primary_colour;color:$primaryButtonText}";

            if ($primary_button_css_class == "") {
                $css.= ".ukds-primary-button:after, .fc-event,#ukds-container .ukds-product-pagination ul li .current,#ukds-container .ukds-product-pagination ul li .current:hover {background:$primary_button_background; color:$primary_button_text_colour;}";
                $css.= ".ukds-primary-button, .ukds-primary-button:hover,.ukds-primary-button:active,.ukds-primary-button:focus {color:$primary_button_text_colour!important;}";
            }


            if ($secondary_button_css_class == "") {
                $css.= "#ukds-container .ukds-secondary-button,#ukds-container #ukds-calendar button:hover,#ukds-container #ukds-calendar button:focus,#ukds-container #ukds-calendar input[type='button']:hover,#ukds-container #ukds-calendar input[type='button']:focus,#ukds-container #ukds-calendar input[type='reset']:hover,#ukds-container #ukds-calendar input[type='reset']:focus,#ukds-container #ukds-calendar input[type='submit']:hover,#ukds-container #ukds-calendar input[type='submit']:focus,#ukds-container .ukds-toolbar-item select, #ukds-prices .ticket-number, .widget-filter-area input[type=checkbox]:checked + label {color:$secondary_button_text_colour; background:$secondary_button_background}";
            }

            $css.= ".ukds-link-colour, .single-ukds-products .ukds-product-venue h2 a, .ukds-product-bottom .product-long-offer {color:$primary_colour}";

        }

        $css.= $custom_css;
        $css.="</style>";
        $js.="</script>";

        echo $css;
        echo $js;
    }



	/**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
	public function enqueue_styles() {

		/**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ltd_Tickets_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ltd_Tickets_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */


        $my_theme = wp_get_theme();
        if ($my_theme->get('TextDomain') == "twentyseventeen") {
            wp_enqueue_style( $this->plugin_name . '-twentyseventeen', plugin_dir_url( __FILE__ ) . 'css/twentyseventeen.css', array(), $this->version, 'all' );
        }
        if ($my_theme->get('TextDomain') == "twentyfifteen") {
            wp_enqueue_style( $this->plugin_name . '-twentyfifteen', plugin_dir_url( __FILE__ ) . 'css/twentyfifteen.css', array(), $this->version, 'all' );
        }


		wp_enqueue_style( $this->plugin_name . '-bootstrap', plugin_dir_url( __FILE__ ) . 'css/core.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ltd-tickets-public.css', array(), $this->version, 'all' );


        global $wp_styles;
        if (isset($wp_styles)) :
            $srcs = array_map('basename', (array) wp_list_pluck($wp_styles->registered, 'src') );
            if (! in_array('font-awesome.css', $srcs) &! in_array('font-awesome.min.css', $srcs)  ) {
                wp_enqueue_style('font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css' );
            }
        endif;

	}



	/**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
	public function enqueue_scripts() {

		/**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ltd_Tickets_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ltd_Tickets_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
	    wp_enqueue_script( $this->plugin_name . 'core', plugin_dir_url( __FILE__ ) . 'js/core.min.js', array( 'jquery'), $this->version, true );
        wp_enqueue_script( $this->plugin_name . "ltd", plugin_dir_url( __DIR__ ) .  'public/js/ltd.lib.js', array( 'jquery'), $this->version, true );
		wp_localize_script( $this->plugin_name, 'ukdsAjaxHandler', array (
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'nonce'		=> wp_create_nonce( 'ukds-ajax-handler-nonce' ),
 		));

	}


    public function conditional_redirects() {
        if (is_post_type_archive($this->plugin_options['config']['product_post_type']) ||
            is_post_type_archive($this->plugin_options['config']['venue_post_type']) ||
            is_tax($this->plugin_options['config']['product_category_taxonomy'])) {
            if (get_query_var("paged") != ltd_get_url_var("page")) {
                global $wp;
                $current_url =  home_url( $wp->request );
                $position = strpos( $current_url , '/page' );
                $nopaging_url = ( $position ) ? substr( $current_url, 0, $position ) : $current_url;
                wp_redirect( $nopaging_url );
                exit;
            }
        }
    }

}
