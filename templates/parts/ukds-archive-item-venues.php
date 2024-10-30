<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ltd-tickets
 */

$default_class = "ukds-col-lg-4 ukds-col-md-4 ukds-col-sm-4 ukds-col-xs-6 ukds-col-xxs ukds-col-product-grid";
$options = get_post_meta($post->ID);

?>
<div class="<?php echo $default_class; ?>" itemscope="" itemtype="http://schema.org/Product">
    <a href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title(); ?>" itemprop="url" class="ukds-product-grid-item">
        <span class="ukds-product-grid-image">
            <?php do_action( 'ltd_tickets_featured_image' ); ?>
        </span>
        <span class="ukds-product-grid-details">
            <span class="ukds-product-grid-title" itemprop="name">
            <?php echo get_the_title(); ?>
            </span>
            <?php do_action('ltd_tickets_venue_address'); ?>
            <?php do_action('ltd_tickets_venue_postcode'); ?>
        </span>
    </a>
</div>