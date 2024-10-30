<?php
/**
 * LTD Post Types
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/helpers
 * @author     Ben Campbell <ben@ukds.co>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
	Register Venue Post Type
*/
$labels = array(
	'name'               => _x( 'Venues', 'post type general name', $this->plugin_name ),
	'singular_name'      => _x( 'Venue', 'post type singular name', $this->plugin_name ),
	'menu_name'          => _x( 'Venues', 'admin menu', $this->plugin_name ),
	'name_admin_bar'     => _x( 'Venue', 'add new on admin bar', $this->plugin_name ),
	'add_new'            => _x( 'Add New', 'venues', $this->plugin_name ),
	'add_new_item'       => __( 'Add New Venue', $this->plugin_name ),
	'new_item'           => __( 'New Venue', $this->plugin_name ),
	'edit_item'          => __( 'Edit Venue', $this->plugin_name ),
	'view_item'          => __( 'View Venue', $this->plugin_name ),
	'all_items'          => __( 'All Venues', $this->plugin_name ),
	'search_items'       => __( 'Search Venues', $this->plugin_name ),
	'parent_item_colon'  => __( 'Parent Venues:', $this->plugin_name ),
	'not_found'          => __( 'No venues found. Import venues using the LTD Tickets Plugin.', $this->plugin_name ),
	'not_found_in_trash' => __( 'No venues found in Trash.', $this->plugin_name )
);
$args = array(
	'labels'             => $labels,
	'description'        => __( 'All imported Venues.', $this->plugin_name ),
	'public'             => true,
	'show_ui'            => true,
	'show_in_menu'       => true,
	'query_var'          => true,
	'rewrite'            => array( 'slug' => 'venues' ),
	'capability_type'    => 'post',
	'has_archive'        => true,
	'hierarchical'       => false,
	'menu_position'      => null,
	'with_front'	     => false,
	'supports'           => array( 'title', 'editor', 'custom-fields', 'thumbnail', 'page-attributes' ),
	'menu_icon'			 => 'dashicons-admin-multisite',
    'show_in_rest'       => true,
);
register_post_type( 'ukds-venues', $args );


/*
	Register Product Post Type
*/
$labels = array(
	'name'               => _x( 'Products', 'post type general name', $this->plugin_name ),
	'singular_name'      => _x( 'Product', 'post type singular name', $this->plugin_name ),
	'menu_name'          => _x( 'Products', 'admin menu', $this->plugin_name ),
	'name_admin_bar'     => _x( 'Product', 'add new on admin bar', $this->plugin_name ),
	'add_new'            => _x( 'Add New', 'products', $this->plugin_name ),
	'add_new_item'       => __( 'Add New Product', $this->plugin_name ),
	'new_item'           => __( 'New Product', $this->plugin_name ),
	'edit_item'          => __( 'Edit Product', $this->plugin_name ),
	'view_item'          => __( 'View Product', $this->plugin_name ),
	'all_items'          => __( 'All Products', $this->plugin_name ),
	'search_items'       => __( 'Search Products', $this->plugin_name ),
	'parent_item_colon'  => __( 'Parent Products:', $this->plugin_name ),
	'not_found'          => __( 'No products found. Import products using the LTD Tickets Plugin.', $this->plugin_name ),
	'not_found_in_trash' => __( 'No products found in Trash.', $this->plugin_name )
);
$args = array(
	'labels'             => $labels,
	'description'        => __( 'All imported Products.', $this->plugin_name ),
	'public'             => true,
	'show_ui'            => true,
	'show_in_menu'       => true,
	'rewrite'            => array( 'slug' => 'tickets' ),
	'capability_type'    => 'post',
	'has_archive'        => true,
	'hierarchical'       => false,
	'menu_position'      => null,
	'with_front'	     => false,
	'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt'),
    'menu_icon'			 => 'dashicons-tickets-alt',
    'show_in_rest'       => true,
);

register_post_type( 'ukds-products', $args );


function display_product_info_meta_box( $post ) {
    $options = get_post_meta($post->ID);

?>
<h2 class="nav-tab-wrapper" id="ukds-tabs">
    <a href="#top#prices" class="nav-tab nav-tab-active" id="prices-tab"><?php _e("Pricing", 'ltd-tickets'); ?></a>
    <a href="#top#extrainfo" class="nav-tab nav-tab-active" id="extrainfo-tab"><?php _e("Extra Info", 'ltd-tickets'); ?></a>
    <a href="#top#running" class="nav-tab" id="running-tab"><?php _e("Running Info", 'ltd-tickets'); ?></a>
    <a href="#top#gallery" class="nav-tab" id="gallery-tab"><?php _e("Gallery", 'ltd-tickets'); ?></a>
    <!--<a href="#top#urls" class="nav-tab" id="urls-tab"><?php _e("Exit URLs", 'ltd-tickets'); ?></a>-->
</h2>
<div id="prices" class="ukdstab active">
    <table class="meta-table" id="prices_table">
        <tbody>
            <tr>
                <td>Current Price</td>
                <td>
                    <input type="text" class="meta-field" name="product_post_type_current_price" value="<?php echo (!empty($options['current_price']) ? esc_attr($options['current_price'][0]) : ''); ?>" />
                </td>
            </tr>
            <tr>
                <td>Minimum Price</td>
                <td>
                    <input type="text" class="meta-field" name="product_post_type_minimum_price" value="<?php echo (!empty($options['minimum_price']) ? esc_attr($options['minimum_price'][0]) : ''); ?>" />
                </td>
            </tr>
            <tr>
                <td>Offer Price</td>
                <td>
                    <input type="text" class="meta-field" name="product_post_type_offer_price" value="<?php echo (!empty($options['offer_price']) ? esc_attr($options['offer_price'][0]) : ''); ?>" />
                </td>
            </tr>
            <tr>
                <td>Short Offer Text</td>
                <td>
                    <input type="text" class="meta-field" name="product_post_type_short_offer_text" value="<?php echo (!empty($options['short_offer_text']) ? esc_attr($options['short_offer_text'][0]) : ''); ?>" />
                </td>
            </tr>
            <tr>
                <td>Long Offer Text</td>
                <td>
                    <textarea class="meta-field" name="product_post_type_long_offer_text"><?php echo (!empty($options['long_offer_text']) ? esc_attr($options['long_offer_text'][0]) : ''); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="extrainfo" class="ukdstab">
    <table class="meta-table" id="extrainfo_table">
        <tbody>
            <tr>
                <td>Tagline</td>
                <td>
                    <input type="text" class="meta-field" name="product_post_type_tagline" value="<?php echo (!empty($options['tagline']) ? esc_attr($options['tagline'][0]) : ''); ?>" />
                </td>
            </tr>
            <tr>
                <td>Important Information</td>
                <td>
                    <textarea class="meta-field" name="product_post_type_important_notice"><?php echo (!empty($options['important_notice']) ? esc_attr($options['important_notice'][0]) : ''); ?></textarea>
                </td>
            </tr>
            <tr>
                <td>Minimum Age</td>
                <td>
                    <input type="text" class="meta-field" name="product_post_type_minimum_age" value="<?php echo (!empty($options['minimum_age']) ? esc_attr($options['minimum_age'][0]) : ''); ?>" />
                </td>
            </tr>
            <tr>
                <td>Target URL Override</td>
                <td>
                    <input type="text" class="meta-field" name="product_post_type_target_url_override" value="<?php echo (!empty($options['target_url_override']) ? esc_attr($options['target_url_override'][0]) : ''); ?>" />
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="running" class="ukdstab">
    <table class="meta-table" id="running_table">
        <tbody>
            <tr>
                <td>Start Date</td>
                <td>
                    <input type="date" class="meta-field" name="product_post_type_start_date" value="<?php echo (!empty($options['start_date']) ? esc_attr($options['start_date'][0]) : ''); ?>" />
                </td>
            </tr>
            <tr>
                <td>End Date</td>
                <td>
                    <input type="date" class="meta-field" name="product_post_type_end_date" value="<?php echo (!empty($options['end_date']) ? esc_attr($options['end_date'][0]) : ''); ?>" />
                </td>
            </tr>
            <tr>
                <td>Running Time</td>
                <td>
                    <input type="text" class="meta-field" name="product_post_type_running_time" value="<?php echo (!empty($options['running_time']) ? esc_attr($options['running_time'][0]) : ''); ?>" />
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="gallery" class="ukdstab">
    <table class="meta-table" id="gallery_table">
        <tbody>
            <tr>
                <td>Main Image URL</td>
                <td>
                    <input type="text" class="meta-field" name="product_post_type_main_image_url" value="<?php echo (!empty($options['main_image_url']) ? esc_url($options['main_image_url'][0]) : ''); ?>" />
                </td>
            </tr>
            <?php if (!empty( $options['gallery_images'] )) : ?>
            <tr>
                <td>Gallery</td>
                <td>
                    <textarea class="meta-field" name="product_post_type_gallery_images"><?php echo $options['gallery_images'][0]; ?></textarea>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
}
function product_post_type_admin() {
    add_meta_box( 'product_info_meta_box',
        'Product Details',
        'display_product_info_meta_box',
        'ukds-products', 'normal', 'high'
    );
}
add_action( 'admin_init', 'product_post_type_admin' );
add_action( 'save_post', 'product_post_type_fields', 10, 2 );

function product_post_type_fields( $post_id, $post ) {

    if ( $post->post_type == 'ukds-products' ) {

        if ( isset( $_POST['product_post_type_current_price'] ) && $_POST['product_post_type_current_price'] != '' ) {
            $current_price = ltd_sanitise_currency($_POST['product_post_type_current_price']);
            if ($current_price !== false) {
                update_post_meta( $post_id, 'current_price', $current_price );
            }
        }

        if ( isset( $_POST['product_post_type_minimum_price'] ) && $_POST['product_post_type_minimum_price'] != '' ) {
            $minimum_price = ltd_sanitise_currency($_POST['product_post_type_minimum_price']);
            if ($minimum_price !== false) {
                update_post_meta( $post_id, 'minimum_price', $minimum_price );
            }
        }

        if ( isset( $_POST['product_post_type_offer_price'] ) && $_POST['product_post_type_offer_price'] != '' ) {
            $offer_price = ltd_sanitise_currency($_POST['product_post_type_offer_price']);
            if ($offer_price !== false) {
                update_post_meta( $post_id, 'offer_price', $offer_price );
            }
        }

        if ( isset( $_POST['product_post_type_short_offer_text'] ) && $_POST['product_post_type_short_offer_text'] != '' ) {
            $short_offer_text = ltd_sanitise_meta_text($_POST['product_post_type_short_offer_text']);
            update_post_meta( $post_id, 'short_offer_text', $short_offer_text );
        }

        if ( isset( $_POST['product_post_type_long_offer_text'] ) ) {
            $long_offer_text = ltd_sanitise_meta_text($_POST['product_post_type_long_offer_text']);

            update_post_meta( $post_id, 'long_offer_text', $long_offer_text );
        }

        if ( isset( $_POST['product_post_type_start_date'] ) && $_POST['product_post_type_start_date'] != '' ) {
            $date = ltd_sanitise_date_field($_POST['product_post_type_start_date']);
            if ($date !== false) {
                update_post_meta( $post_id, 'start_date', $date );
            }
        }

        if ( isset( $_POST['product_post_type_end_date'] ) && $_POST['product_post_type_end_date'] != '' ) {
            $date = ltd_sanitise_date_field($_POST['product_post_type_end_date']);
            if ($date !== false) {
                update_post_meta( $post_id, 'end_date', $date );
            }
        }

        if ( isset( $_POST['product_post_type_running_time'] ) ) {
            $running_time = sanitize_text_field($_POST['product_post_type_running_time']);
            update_post_meta( $post_id, 'running_time', $running_time );
        }

        if ( isset( $_POST['product_post_type_minimum_age'] ) ) {
            $minimum_age = sanitize_text_field($_POST['product_post_type_minimum_age']);
            update_post_meta( $post_id, 'minimum_age', $minimum_age );
        }

        if ( isset( $_POST['product_post_type_gallery_images'] ) ) {
            $gallery_images = sanitize_text_field($_POST['product_post_type_gallery_images']);
            update_post_meta( $post_id, 'gallery_images', $gallery_images );
        }

        if ( isset( $_POST['product_post_type_main_image_url'] ) && $_POST['product_post_type_main_image_url'] != '' ) {
            update_post_meta( $post_id, 'main_image_url', esc_url($_POST['product_post_type_main_image_url']) );
        }

        if ( isset( $_POST['product_post_type_important_notice'] ) ) {
            $important_notice = ltd_sanitise_meta_text($_POST['product_post_type_important_notice']);
            update_post_meta( $post_id, 'important_notice', $important_notice);
        }

        if ( isset( $_POST['product_post_type_target_url_override'] ) && $_POST['product_post_type_target_url_override'] != '' ) {
            $target_url_override = esc_url($_POST['product_post_type_target_url_override']);
            if (!filter_var($target_url_override, FILTER_VALIDATE_URL) === FALSE) {


                update_post_meta( $post_id, 'target_url_override', $target_url_override);
            }

            if ( isset( $_POST['product_post_type_tagline'] ) ) {
                $tagline = sanitize_text_field($_POST['product_post_type_tagline']);
                update_post_meta( $post_id, 'tagline', $tagline );
            }

        }
    }
}

add_image_size('ltd_featured_preview', 55, 55, true);

function ltd_product_get_featured_image($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'ltd_featured_preview');
        return $post_thumbnail_img[0];
    }
    return false;
}

function ltd_product_columns_head($defaults) {

    $cols = array();
    $i = 0;
    foreach($defaults as $key => $value) {
        $cols[$key] = $value;
        if ($i==0) $cols['featured_image'] = 'Image';
        $i++;
    }
    return $cols;
}

function ltd_product_columns_content($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        $post_featured_image = ltd_product_get_featured_image($post_ID);
        if ($post_featured_image) {
            echo '<img src="' . $post_featured_image . '" />';
        }
        else {
            echo '<img src="' . LTD_PLUGIN_DIR . 'admin/images/product-default.png" />';
        }
    }
}

add_filter('manage_ukds-products_posts_columns', 'ltd_product_columns_head');
add_action('manage_ukds-products_posts_custom_column', 'ltd_product_columns_content', 10, 2);