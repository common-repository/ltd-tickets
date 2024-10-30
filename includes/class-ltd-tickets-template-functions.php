<?php

/**
 * Template Functions.
 *
 * Handles the actions used to display elements on template pages.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class LTD_Tickets_Template_Functions {

    use LTD_Tickets_Logging;

    private $plugin_name;
    private $version;
    public $plugin_options;




    function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_options = get_option( $plugin_name );
        $this->init();
    }

    function init() {

        add_action( 'ltd_tickets_container_class', array($this, 'ltd_container_class'), 10);
        add_action( 'ltd_tickets_booking_link', array($this, 'ltd_booking_link'), 10, 1);
        add_action( 'ltd_tickets_product_featured_image', array($this, 'ltd_product_featured_image'), 10);
        add_action( 'ltd_tickets_breadcrumbs', array( $this, 'ltd_breadcrumbs'), 10 );
        add_action( 'ltd_tickets_product_gallery', array($this, 'ltd_product_gallery'), 10 );
        add_action( 'ltd_tickets_product_excerpt', array($this, 'ltd_product_excerpt'), 10, 2 );
        add_action( 'ltd_tickets_product_offer_tag', array($this, 'ltd_product_offer_tag'), 10);
        add_action( 'ltd_tickets_product_from_price', array($this, 'ltd_product_from_price'), 10);
        add_action( 'ltd_tickets_product_grid_from_price', array($this, 'ltd_product_grid_from_price'), 10, 1);
        add_action( 'ltd_tickets_product_full_info', array($this, 'ltd_product_full_info'), 10 );
        add_action( 'ltd_tickets_product_special_offer_long', array($this, 'ltd_product_special_offer_long'), 10);
        add_action( 'ltd_tickets_product_meta', array($this, 'ltd_product_meta'), 10 );
        add_action( 'ltd_tickets_product_important_notice', array($this, 'ltd_product_important_notice'), 10 );
        add_action( 'ltd_tickets_product_tagline', array($this, 'ltd_product_tagline'), 10 );
        add_action( 'ltd_tickets_product_minimum_age', array($this, 'ltd_product_minimum_age'), 10 );
        add_action( 'ltd_tickets_product_venue_link', array($this, 'ltd_product_venue_link'), 10, 1 );
        add_action( 'ltd_tickets_featured_image', array($this, 'ltd_featured_image'), 10);
        add_action( 'ltd_tickets_venue_address', array($this, 'ltd_venue_address'), 10);
        add_action( 'ltd_tickets_venue_address_full', array($this, 'ltd_venue_address_full'), 10);
        add_action( 'ltd_tickets_venue_seating_plan', array($this, 'ltd_venue_seating_plan'), 10);
        add_action( 'ltd_tickets_archive_product_order', array($this, 'ltd_archive_product_order'), 10 );
        add_action( 'ltd_tickets_archive_product_per_page', array($this, 'ltd_archive_product_per_page'), 10 );


        add_filter( 'get_the_archive_title', array($this, 'ltd_archive_title'), 10, 1 );





    }

    public function ltd_venue_address_full() {
        global $post;
        $options = get_post_meta($post->ID);

        echo "<section class='ukds-venue-address widget'>";
        echo '<h2 class="widget-title"><a href="' . get_the_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h2>';
        echo '<div class="textwidget">';
        if ( ! empty($options['address'])) echo '<span class="ukds-product-venue-detail ukds-product-venue-address">' . esc_html($options['address'][0]) . '</span>';
        if ( ! empty($options['city'])) echo '<span class="ukds-product-venue-detail ukds-product-venue-city">' . esc_html($options['city'][0]) . '</span>';
        if ( ! empty($options['postcode'])) echo '<span class="ukds-product-venue-detail ukds-product-venue-postcode">' . esc_html($options['postcode'][0]) . '</span>';
        if ( ! empty($options['nearest_tube'])) {
            echo '<h5>' . __('Nearest Tube', $this->plugin_name) . '</h5>';
            echo '<span class="ukds-product-venue-detail ukds-product-venue-nearest-tube">' . esc_html($options['nearest_tube'][0]) . '</span>';
        }
        if ( ! empty($options['nearest_train'])) {
            echo '<h5>' . __('Nearest Train', $this->plugin_name) . '</h5>';
            echo '<span class="ukds-product-venue-detail ukds-product-venue-nearest-train">' . esc_html($options['nearest_train'][0]) . '</span>';
        }

        echo '</div>';
        echo '</section>';

    }

    public function ltd_venue_seating_plan() {
        global $post;
        $options = get_post_meta($post->ID);
        if ( ! empty($options['seating_plan'])) {
        echo "<section class='ukds-venue-seating-plan widget'>";
        echo '<h2 class="widget-title">' . __('Seating Plan', $this->plugin_name) . '</h2>';
        echo '<div class="textwidget">';
        echo '<span class="ukds-product-venue-detail ukds-product-venue-seating-plan-note">' . __('Click seating plan to see full size version', $this->plugin_name) . '.</span>';
        echo '<span class="ukds-product-venue-detail ukds-product-venue-seating-plan"><a data-ui="popover" href="' . esc_url($options['seating_plan'][0]) . '"><img  src="' . esc_url($options['seating_plan'][0]) . '" alt="' . get_the_title() . ' ' . __('Seating Plan', $this->plugin_name) . '" style="max-width:100%" /></a></span>';
        echo "</div>";
        echo "</section>";
        }
    }


    public function ltd_venue_address() {
        $address = get_post_meta(get_the_ID(), 'address', true);
        if ($address != "") {
            echo "<span class='ukds-product-grid-title' itemprop='name'>" . esc_html($address) . "</span>";
        }
    }



    public function ltd_container_class() {
        global $post;
        $cls = "";
        if ($this->plugin_options['config']['disable_styles'] != 1) {
            $cls.="ukds-container";
            $cls.= (!empty($this->plugin_options['styles']['layout']) && $this->plugin_options['styles']['layout'] == "fluid"  ? "-fluid" : "" );
        } else {
            $cls.="container";
        }
        $cls.= " ";
        $cls.= $this->plugin_options['config']['product_post_type'] . (isset($post) ? "-" . $post->ID : '');
        echo $cls;
    }


    public function ltd_product_featured_image() {
        global $post;
        $options = get_post_meta($post->ID);
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		$imgPath = (empty($image[0]) ? "" : $image[0]);
        if ($imgPath == "" && (isset($options['featured_image']))) $imgPath = $options['featured_image'][0];
        echo "<img src='" .  esc_url($imgPath) . "' alt='"  . get_the_title() . "' style='max-width:100%' class='product-image' />";
    }


       public function ltd_product_grid_from_price($postid = null) {

        if ($postid == null) {
            global $post;
            $postid = $post->ID;
        }
        $options = get_post_meta($postid);
        if (isset($options['minimum_price']) && $options['minimum_price'][0] != "0") {
            echo sprintf(__('<span class="from">From</span> %s', $this->plugin_name), ltd_format_price($options['minimum_price'][0]));
        } else {
            echo __('Click for prices', $this->plugin_name);
        }
    }

    public function ltd_product_from_price() {
        global $post;
        $options = get_post_meta($post->ID);
        if (isset($options['minimum_price']) && $options['minimum_price'][0] != "0") {
            echo '<div class="product-from-price">';
            echo sprintf(__('<span class="from">From</span> %s', $this->plugin_name), ltd_format_price($options['minimum_price'][0]));
            echo '</div>';
        } else {
            echo '<div class="product-no-price">';
            echo __('Click "Book Tickets" for latest prices', $this->plugin_name);
            echo '</div>';
        }
    }


    public function ltd_build_booking_url() {
        global $post;
        $options = get_post_meta($post->ID);
        if (isset($options['target_url_override']) && $options['target_url_override'][0] != "") {
            return $options['target_url_override'][0];
        }
        $bookUrl = parse_url($options['product_url'][0]);
        if ($this->plugin_options['config']['redirect_time'] != 1) {
            return "/book-tickets/" . $post->post_name . "/";
        } else {
            if ($this->plugin_options['config']['partner_type'] == "whitelabel") {
                $newUrl = "https://" . $this->plugin_options['partner']['whitelabel_id'] . ".londontheatredirect.com";
                return $newUrl . "/booking.aspx?eventId=" . $options['product_id'][0] . "&nbTickets=2&firstAvailableMonth=true";
            } elseif ($this->plugin_options['config']['partner_type'] == "awin") {
                $newUrl = $this->plugin_options['default']['awin_hostname'];
                $awinid = (!empty($this->plugin_options['partner']['awin_id']) ? $this->plugin_options['partner']['awin_id'] : $this->plugin_options['default']['awin_id']);
                return ltd_build_awin_deeplink($awinid, $newUrl . "/booking.aspx?eventId=" . $options['product_id'][0] . "&nbTickets=2&firstAvailableMonth=true", $this->plugin_options['partner']['awin_clickref']);
            } else {
                $newUrl = "https://" . $bookUrl['host'];
                return  $newUrl . "/booking.aspx?eventId=" . $options['product_id'][0] . "&nbTickets=2&firstAvailableMonth=true";
            }
        }
    }


    public function ltd_booking_link( $button_text = "" ) {
        $button_text = ($button_text == "" ? __('Book Tickets', $this->plugin_name) : $button_text);
        global $post;
        $options = get_post_meta($post->ID);
        $url = esc_url($this->ltd_build_booking_url());
        $cls = $this->plugin_options['styles']['primary_button_css_class'];
        if ($cls == "") $cls = "ukds-primary-button";
        if (strtotime($options['end_date'][0]) < date('Y-m-d')) {
            $url = "javascript:void(0);";
            $button_text =  __('No more performances!', $this->plugin_name);
            $cls .= " btn-disabled";
        }
        echo "<a href='$url' class='$cls'>" . $button_text . "</a>";
    }

    public function ltd_breadcrumbs() {
        global $post;

        $done = false;
        echo '<ul id="ukds-breadcrumbs">';

        echo '<li><a href="' . get_home_url() . '"><i class="fa fa-home"></i></a></li>';


        if (is_post_type_archive( $this->plugin_options['config']['venue_post_type'] ) || (is_single() && get_post_type() === $this->plugin_options['config']['venue_post_type'])) {
            echo '<li><a href="' . get_the_permalink($this->plugin_options['config']['venue_archive']) . '">' . get_the_title($this->plugin_options['config']['venue_archive']) . '</a></li>';
            if (is_post_type_archive( $this->plugin_options['config']['venue_post_type'] ) ) $done = true;
            if (is_single() && get_post_type() === $this->plugin_options['config']['venue_post_type']) {
                echo '<li>' . get_the_title($post->ID) . '</li>';
                $done = true;
            }
        }

        if (! $done) echo '<li><a href="' . get_the_permalink($this->plugin_options['config']['product_archive']) . '">' . get_the_title($this->plugin_options['config']['product_archive']) . '</a></li>';
        if (is_post_type_archive( $this->plugin_options['config']['product_post_type'] ) ) $done = true;


        if (!$done) {
            if (is_tax($this->plugin_options['config']['product_category_taxonomy'])) {
                $tax = get_taxonomy($this->plugin_options['config']['product_category_taxonomy']);
                $tax_slug = $tax->rewrite['slug'];
                $queried_object = get_queried_object();
                echo "<li><a href='/$tax_slug/" . $queried_object->slug . "/'>" . $queried_object->name . "</a></li>";
            } else if (is_single() && get_post_type() === $this->plugin_options['config']['product_post_type']) {
                $terms  = wp_get_post_terms($post->ID, $this->plugin_options['config']['product_category_taxonomy']);
                if (isset($terms[0])) {
                    $tax = get_taxonomy($this->plugin_options['config']['product_category_taxonomy']);
                    $tax_slug = $tax->rewrite['slug'];
                    echo "<li><a href='/$tax_slug/" . $terms[0]->slug . "/'>" . $terms[0]->name . "</a></li>";
                }
            }
        }
        if (is_archive()) $done = true;

        if (!$done) {
            echo '<li>' . get_the_title($post->ID) . '</li>';
        }

        echo '</ul>';
        echo '<div class="ukds-clearfix"></div>';
    }

    public function ltd_product_gallery() {

        global $post;
        $options = get_post_meta($post->ID);
        $title = get_the_title($post->ID);
        if (!empty($options['gallery_images'])) {
            $gallery = explode(",", $options['gallery_images'][0]);
            $count = count($gallery);
            if ($count < 2) return;
            $i = 0;
            echo "<section class='ukds-product-info-meta widget'>";
            echo "<h2 class='widget-title'>" . __('Image Gallery', $this->plugin_name) . "</h2>";
            echo "<div class='product-image-gallery' ukds-ui='gallery'>";
            foreach( $gallery as $image ):
                $image = esc_url($image);
                echo "<a href='$image' class='product-gallery-item' title='$title - " . __('Image') . " "  . ($i+1) . " '><img src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' ukds-src='$image'  alt='$title' />" . ($count > 3 && $i==2 ? "<span><span>$count " . __('images', $this->plugin_name) . "</span></span>" : "") . "</a>";
                $i++;
            endforeach;
            echo "</div>";
            echo "</section>";
        }
    }

    public function ltd_product_excerpt($length = 200, $scrollTo = "") {
        global $post;

        $excerpt = wp_strip_all_tags( $post->post_excerpt, true );
        if ($excerpt == "") {
            $content = get_the_content();
            $snippet = preg_replace('/\s+?(\S+)?$/', '', substr($content, 0, $length));
            if (strlen($content) > strlen($snippet)) {
                $snippet.='&hellip;';
                if ($scrollTo != "") {
                    $snippet .= "<a href='javascript:void(0);' data-scroll='" . $scrollTo . "'>more</a>";
                }
            }
            $excerpt = $snippet;
        }
        echo '<div class="product-short-desc">';
        echo $excerpt;
        echo '</div>';
    }

    public function ltd_product_offer_tag() {
        global $post;
        $options = get_post_meta($post->ID);
        if (!empty($options['short_offer_text'][0])) {
            echo '<div class="offer-text">';
            echo '<span class="offer-tag">' .  __('Offer', $this->plugin_name) . '</span><span class="offer-label">' . esc_html($options['short_offer_text'][0]) . '</span>';
            echo '<div class="ukds-clearfix"></div>';
            echo '</div>';
        }
    }

    public function ltd_product_page_share() {

    }

    public function ltd_product_special_offer_long() {
        global $post;
        $options = get_post_meta($post->ID);
        if (!empty($options['long_offer_text'][0])) {
            echo "<div class='product-long-offer'>";
            echo ltd_sanitise_meta_text($options['long_offer_text'][0]);
            echo "</div>";
        }

    }

    public function ltd_product_full_info() {
        global $post;
        $options = get_post_meta($post->ID);
        echo "   <h2 class='ukds-featured-offer-title'>" . get_the_title() . "</h2>";
        if (!empty($options['short_offer_text'][0])) echo "   <h4>" . $options['short_offer_text'][0] . "</h4>";
        echo "   <div class='ukds-product-info-content'>";
        echo get_the_content($post->ID);
        echo "   </div>";
    }

    public function ltd_product_minimum_age() {
        global $post;
        $options = get_post_meta($post->ID);
        if (!empty($options['minimum_age'])) {
            echo "<section class='ukds-product-info-meta widget'>";
            echo "<h2 class='widget-title'>" . __('Minimum Age', $this->plugin_name) . "</h2>";
            echo "<div class='textwidget'>";
            echo esc_html($options['minimum_age'][0]);
            echo "</div>";
            echo "</section>";
        }
    }

    public function ltd_product_tagline() {
        global $post;
        $options = get_post_meta($post->ID);
        if (!empty($options['tagline'])) {
            echo "<div class='product-tagline'>";
            echo esc_html($options['tagline'][0]);
            echo "</div>";
        }
    }

    public function ltd_product_important_notice() {
        global $post;
        $options = get_post_meta($post->ID);
        if (!empty($options['important_notice'])) {
            echo "<section class='ukds-product-info-meta widget'>";
            echo "<h2 class='widget-title'>" . __('Important Notice', $this->plugin_name) . "</h2>";
            echo "<div class='textwidget'>";
            echo ltd_sanitise_meta_text($options['important_notice'][0]);
            echo "</div>";
            echo "</section>";
        }
    }

    public function ltd_product_meta() {
        global $post;
        $options = get_post_meta($post->ID);

        $term_list = wp_get_post_terms(get_the_ID(), 'ukds-product-category', array("fields" => "all"));
        $start_date = strtotime($options['start_date'][0]);
        $end_date = strtotime($options['end_date'][0]);

        echo "<section class='ukds-product-info-meta widget'>";
        echo "<h2 class='widget-title'>"  . __('Details', $this->plugin_name) . "</h2>";
        echo "<div class='textwidget'>";
        echo "<table>";
        echo "<tbody>";
        echo "<tr>";
        echo "<td>". __('Starts', $this->plugin_name) ."</td>";
        echo "<td>" . date_i18n('l, j F Y', $start_date) . "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>". __('Ends', $this->plugin_name) ."</td>";
        echo "<td>" . date_i18n('l, j F Y', $end_date) . "</td>";
        echo "</tr>";
        if (isset($options['running_time'])) {
            echo "<tr>";
            echo "<td>". __('Running Time', $this->plugin_name) ."</td>";
            echo "<td>" . esc_html($options['running_time'][0]) . "</td>";
            echo "</tr>";
        }
        if (!empty($term_list[0])) {
            $tax = get_taxonomy($this->plugin_options['config']['product_category_taxonomy']);
            $tax_slug = $tax->rewrite['slug'];
            echo "<tr>";
            echo "<td>". __('Category', $this->plugin_name) ."</td>";
            echo "<td><a href='/$tax_slug/" . $term_list[0]->slug . "/' title='" . $term_list[0]->name . " " . __('Tickets', $this->plugin_name) . "'>" . $term_list[0]->name . "</a></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</section>";

        if ( ! empty($options['venue_id'] ) ) {
            $venueArgs = array(
                'post_type'			=> 'ukds-venues',
                'meta_key'			=> 'venue_id',
                'meta_value'		=> $options['venue_id'],
                'posts_per_page'	=> 1,
                'paged'				=> false,
            );

            $venueQuery = new WP_Query($venueArgs);
            if ($venueQuery->have_posts()) {
                while ($venueQuery->have_posts()) :
                    $venueQuery->the_post();
                    $venueOptions = get_post_meta($venueQuery->post->ID);
                    echo "   <section class='ukds-product-info-meta widget'>";
                    echo    "<h2 class='widget-title'>"  . __('Venue', $this->plugin_name) . "</h2>";
                    echo    "<div class='textwidget'>";
                    echo     "<table>";
                    echo     "<tbody>";
                    echo     "<tr>";
                    echo     "<td>". __('Venue', $this->plugin_name) ."</td>";
                    echo     "<td><a href='" . get_the_permalink() . "' title='" . get_the_title() . "'>" . get_the_title() . "</a></td>";
                    echo     "</tr>";
                    if (!empty($venueOptions['address'])) {
                        echo     "<tr>";
                        echo     "<td>"  . __('Address', $this->plugin_name) .  "</td>";
                        echo     "<td>" . esc_html($venueOptions['address'][0]) . ", " . esc_html($venueOptions['city'][0]) . ", " . esc_html($venueOptions['postcode'][0]) . "<br /><a href='https://maps.google.com/maps?q=" . urlencode(get_the_title()) . "+" . urlencode($venueOptions['postcode'][0]) . "' data-ui='popover-map'>" . __('View Google Map', $this->plugin_name) . "</a></td>";
                        echo     "</tr>";
                    }
                    if (!empty($venueOptions['nearest_tube'])) {
                        echo     "<tr>";
                        echo     "<td>"  . __('Nearest Tube', $this->plugin_name) . "</td>";
                        echo     "<td>" . esc_html($venueOptions['nearest_tube'][0]) . "</td>";
                        echo     "</tr>";
                    }
                    if (!empty($venueOptions['seating_plan'])) {
                        echo     "<tr>";
                        echo     "<td>" .  __('Seating Plan', $this->plugin_name) ."</td>";
                        echo     "<td><a data-ui='popover' href='" . esc_html($venueOptions['seating_plan'][0]) . "'>" . __('View Seating Plan', $this->plugin_name) . "</a></td>";
                        echo     "</tr>";
                    }
                    echo     "</tbody>";
                    echo     "</table>";
                    echo    "</div>";
                    echo "   </section>";
                endwhile;
            }
            wp_reset_postdata();
       }

    }

    function ltd_archive_product_per_page() {
        if (is_post_type_archive($this->plugin_options['config']['venue_post_type'])) {
            $per_page = get_query_var('ltd_venue_per_page');
            if ($per_page == "") {
                $per_page = LTD_Tickets_Cookies::get("venue_per_page");
            }     
        } else {
            $per_page = get_query_var('ltd_product_per_page');
            if ($per_page == "") {
                $per_page = LTD_Tickets_Cookies::get("product_per_page");
            }
        }
        ?>
        <select id="ukds-per-page">
            <option value="12" <?php echo ($per_page == "12" ? "selected" : ""); ?>>12</option>
            <option value="24" <?php echo ($per_page == "24" ? "selected" : ""); ?>>24</option>
            <option value="36" <?php echo ($per_page == "36" ? "selected" : ""); ?>>36</option>
            <option value="-1" <?php echo ($per_page == "-1" ? "selected" : ""); ?>><?php _e('All', $this->plugin_name); ?></option>
        </select>
        <input type="hidden" id="ItemsPerPage" name="ItemsPerPage" value="<?php echo $per_page; ?>" />

        <?php 

    }

    function ltd_archive_product_order() {
        if (is_post_type_archive($this->plugin_options['config']['venue_post_type'])) {
            $item_order = get_query_var('ltd_venue_order');
            if ($item_order == "") {
                $item_order = LTD_Tickets_Cookies::get("venue_order");
            }           
        } else {
            $item_order = get_query_var('ltd_product_order');
            if ($item_order == "") {
                $item_order = LTD_Tickets_Cookies::get("product_order");
            }
        }
        ?>
        <select id="ukds-order">
            <option value="OrderAlphaAsc" <?php echo ($item_order == "OrderAlphaAsc" ? "selected" : ""); ?>><?php _e('Alphabetical (Asc)', $this->plugin_name); ?></option>
            <option value="OrderAlphaDesc" <?php echo ($item_order == "OrderAlphaDesc" ? "selected" : ""); ?>><?php _e('Alphabetical (Desc)', $this->plugin_name); ?></option>
            <?php if (!is_post_type_archive($this->plugin_options['config']['venue_post_type'])) : ?>
            <option value="OrderPriceAsc" <?php echo ($item_order == "OrderPriceAsc" ? "selected" : ""); ?>><?php _e('Cheapest', $this->plugin_name); ?></option>
            <option value="OrderPriceDesc" <?php echo ($item_order == "OrderPriceDesc" ? "selected" : ""); ?>><?php _e('Most Expensive', $this->plugin_name); ?></option>
            <option value="OrderEndingSoon" <?php echo ($item_order == "OrderEndingSoon" ? "selected" : ""); ?>><?php _e('Ending Soon', $this->plugin_name); ?></option>
            <option value="OrderComingSoon" <?php echo ($item_order == "OrderComingSoon" ? "selected" : ""); ?>><?php _e('Coming Soon', $this->plugin_name); ?></option>
            <?php endif; ?>
        </select>
        <input type="hidden" id="ItemOrder" name="ItemOrder" value="" />

        <?php 

    }

    function ltd_archive_title($title) {
        if (is_tax( $this->plugin_options['config']['product_category_taxonomy'] )) {
            $title = single_tag_title( '', false );
            $title.= " ";
            $title.= __( 'Tickets', $this->plugin_name );
        }
        if (is_post_type_archive( $this->plugin_options['config']['product_post_type'] )) {
            $title = get_the_title($this->plugin_options['config']['product_archive']);
        }
        if (is_post_type_archive( $this->plugin_options['config']['venue_post_type'] )) {
            $title = get_the_title($this->plugin_options['config']['venue_archive']);
        }

        return $title;
    }

    function ltd_product_venue_link($postId) {

        $args = array(
            'post_type'         => $this->plugin_options['config']['venue_post_type'],
            'posts_per_page'    => 1,
            'meta_key'          => 'venue_id',
            'meta_value'        => get_post_meta($postId, 'venue_id', true)
        );

        $posts = get_posts($args);
        if (isset($posts[0])) {
            $title = get_the_title($posts[0]->ID);
            echo "<a href='" . get_the_permalink($posts[0]->ID) . "' title='" . $title . "'>" . $title . "</a>";
        }
    }

    function ltd_featured_image(  ) {
        global $post;
        $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'ltd-product-image' );
        if (isset($large_image_url[0])) {
            echo "<img src='" . esc_url($large_image_url[0]) . "' alt='" . get_the_title($post->ID) . "' itemprop='image' />";
        } else {
            echo "<img src='" . plugins_url( 'templates/images/missing-image.png', dirname(__FILE__)  ) . "' alt='" . get_the_title($post->ID) . "' itemprop='image' />";
        }

    }
}