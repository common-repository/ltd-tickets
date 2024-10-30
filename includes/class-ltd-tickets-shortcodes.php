<?php

/**
 * Shortcode Functions.
 *
 * Builds and controls the plugin Shortcodes.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */
class LTD_Tickets_Shortcodes
{
    use LTD_Tickets_Logging;

    private $plugin_name;
    private $version;
    public $plugin_options;


    function __construct() {
        $this->plugin_name = LTD_PLUGIN_NAME;
        $this->version = LTD_PLUGIN_VERSION;
        $this->plugin_options = get_option( $this->plugin_name );

        add_shortcode( 'ltd_button', array($this, 'ltd_shortcode_book_button') );


    }


    public function ltd_shortcode_book_button($args) {

            $a = shortcode_atts( array(
                'id' => '',
                'text' => 'Book Tickets',
            ), $args );

            $url = "";

            if ($this->plugin_options['config']['redirect_time'] != 1) {

            } else {

                $partner_type = $this->plugin_options['config']['partner_type'];

                if ($partner_type == "whitelabel") {
                    $newUrl = "https://" . $this->plugin_options['partner']['whitelabel_id'] . ".londontheatredirect.com";
                    $url = $newUrl . "/booking.aspx?eventId=" . $a['id'] . "&nbTickets=2&firstAvailableMonth=true";
                } elseif ($partner_type == "awin") {
                    $newUrl = $this->plugin_options['default']['awin_hostname'];
                    $awinid = (!empty($this->plugin_options['partner']['awin_id']) ? $this->plugin_options['partner']['awin_id'] : $this->plugin_options['default']['awin_id']);
                    $url =  ltd_build_awin_deeplink($awinid, $newUrl . "/booking.aspx?eventId=" . $a['id'] . "&nbTickets=2&firstAvailableMonth=true", $this->plugin_options['partner']['awin_clickref']);
                } elseif ($partner_type == "api") {
                    $url = $this->plugin_options['partner']['api_key_host'] . "/booking.aspx?eventId=" . $a['id'] . "&nbTickets=2&firstAvailableMonth=true";
                } else {
                    $newUrl = "https://www.londontheatredirect.com";
                    $url = $newUrl . "/booking.aspx?eventId=" . $a['id'] . "&nbTickets=2&firstAvailableMonth=true";
                }
            }

            $cls = $this->plugin_options['styles']['primary_button_css_class'];
            if ($cls == "") $cls = "ukds-primary-button";
            return "<a href='$url' class='$cls'>" . $a['text'] . "</a>";

    }

}
new LTD_Tickets_Shortcodes();