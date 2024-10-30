<?php

/**
 * Default Plugin Settings.
 *
 * Defines the preset options for the operation of the plugin.
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/admin
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */
class Ltd_Tickets_Defaults
{

    public static function get_api_defaults() {
        return array(
          'url_live'                                    => 'https://api.londontheatredirect.com/rest/v2/',
          'url_sandbox'                                 => 'https://api-sandbox.londontheatredirect.com/rest/v2/',
        );
    }

    public static function get_default_defaults() {
        return array(
            'api_key_sandbox'                           => 'cb8th3whhnhvsabv4t2mdh2r',
            'api_key_live'                              => 'b7axztchxzwjwmrvxmmnfzuh',
            'awin_id'                                   => '140777',
            'awin_hostname'                             => 'https://www.ltdtickets.com'
        );
    }

    public static function get_config_defaults() {
        return array(
            'api_target'                                => 'sandbox',
            'api_user'                                  => 'default',
            'product_post_type'                         => 'ukds-products',
            'product_category_taxonomy'                 => 'ukds-product-category',
            'venue_post_type'                           => 'ukds-venues',
            'redirect_time'                             => 1,
            'partner_type'                              => 'none',
            'disable_styles'                            => 0
        );
    }

    public static function get_template_defaults() {
        return array(
            'product_template'                         => 'single-ukds-products.php',
            'product_archive'                          => 'archive-ukds-products.php',
            'venue_template'                           => 'single-ukds-venues.php',
            'venue_archive'                            => 'archive-ukds-venues.php',
            'category_template'                        => 'taxonomy-ukds-product-category.php',
            'booking_template'                         => 'template-booking.php',
            'basket_template'                          => 'template-basket.php',
            'checkout_template'                        => 'template-checkout.php',
            'confirmation_template'                    => 'template-confirmation.php'
        );
    }

    public static function get_sync_defaults() {
        return array(
            'last_product_sync'                         => '',
            'import_products'                           => 0,
            'import_products_status'                    => 'publish',
            'import_venues'                             => 0,
            'import_venues_status'                      => 'publish',
            'import_categories'                         => 0,
            'update_frequency'                          => 'daily',
            'product_update_title'                      => 0,
            'product_update_content'                    => 0,
            'product_update_running_time'               => 1,
            'product_update_minimum_age'                => 1,
            'product_update_start_date'                 => 1,
            'product_update_end_date'                   => 1,
            'product_update_tagline'                    => 0,
            'product_update_important_notice'           => 1,
            'product_update_minimum_price'              => 1,
            'product_update_current_price'              => 1,
            'product_update_offer_price'                => 1,
            'product_update_short_offer_text'           => 1,
            'product_update_long_offer_text'            => 1,
            'product_update_main_image_url'             => 0,
            'product_update_image_gallery'              => 0,
            'venue_update_title'                        => 0,
            'venue_update_content'                      => 0,
            'venue_update_address'                      => 0,
            'venue_update_city'                         => 0,
            'venue_update_postcode'                     => 0,
            'venue_update_nearest_tube'                 => 0,
            'venue_update_nearest_train'                => 0,
            'venue_update_seating_plan'                 => 1,
            'venue_update_image_url'                    => 1,
        );
    }

    public static function get_partner_defaults() {

        return array(
            'whitelabel_id'                             => '',
            'awin_id'                                   => '',
            'awin_clickref'                             => 'ltd_wp_plugin',
            'api_key_live'                              => '',
            'api_key_sandbox'                           => '',
            'api_key_host'                              => '',
        );

    }

    public static function get_style_defaults() {
        return array(
            'primary_colour'                            => '#630b79',
            'secondary_colour'                          => '#F0F0F0',
            'primary_button_background'                 => '#d50657',
            'primary_button_text_colour'                => '#ffffff',
            'primary_button_css_class'                  => '',
            'secondary_button_background'               => '#f5f5f5',
            'secondary_button_text_colour'              => '#747474',
            'secondary_button_css_class'                => '',
            'custom_css'                                => '',
            'layout'                                    => 'boxed',
            'layout_max_width'                          => '1170px',

        );
    }
}