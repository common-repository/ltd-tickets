<?php

/**
 * The template for displaying all single venues.
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/templates
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */

get_header();
$plugin_options = get_option("ltd-tickets");
?>
<div class="<?php do_action('ltd_tickets_container_class');  ?> hentry" id="ukds-container">
    <div class="ukds-row">

    <div class="ukds-col-lg-12">
        <?php do_action( 'ltd_tickets_breadcrumbs' ); ?>
    </div>
    <?php
    while ( have_posts() ) :
		the_post();
		$options = get_post_meta($post->ID); ?>

        <div class="ukds-col-md-3  ukds-col-sm-5">
            <div class="venue-post-left">
                <?php do_action( 'ltd_tickets_featured_image' ); ?>
                <?php do_action( 'ltd_tickets_venue_address_full' ); ?>
                <?php do_action( 'ltd_tickets_venue_seating_plan' ); ?>
            </div>
        </div>

        <div class="ukds-col-md-6 ukds-col-sm-7 ">
            <?php the_title( '<h1 class="product_title entry-title">', (! empty($options['city']) ? ', ' . $options['city'][0] : '') . '</h1>' ); ?>
            <div class="product-post-content entry entry-content">
                <?php the_content(); ?>
            </div>
        </div>

        <div class="ukds-col-md-3 ukds-col-sm-12">
            <aside id="ukds-venue-product-list" class="widget-area" role="complementary">
                <?php
			        $productArgs = array(
				        'post_type'			=> 'ukds-products',
				        'meta_key'			=> 'venue_id',
				        'meta_value'		=> $options['venue_id'],
				        'posts_per_page'	=> -1,
				        'paged'				=> false,
			        );
			        $productQuery = new WP_Query($productArgs);
			        if ($productQuery->have_posts()) {
				        $i=0;
				        while ( $productQuery->have_posts() ) : $productQuery->the_post();
					        echo "<div class='widget'>";
						        include( 'parts/ukds-venue-product-item.php' );
						        $i++;
					        echo "</div>";
				        endwhile;
			        }
			        wp_reset_postdata();
                ?>
            </aside>
        </div>
    <?php endwhile; ?>
    </div>
</div>
<?php
get_footer();