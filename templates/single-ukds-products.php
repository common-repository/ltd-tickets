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
?>
<div class="<?php do_action('ltd_tickets_container_class');  ?> hentry" id="ukds-container">
    <div class="ukds-row">
    <?php
    while ( have_posts() ) :
        the_post();
    ?>
    <div class="ukds-product-top">
        <div class="ukds-col-lg-12">
            <?php do_action( 'ltd_tickets_breadcrumbs' ); ?>
        </div>
        <div class="ukds-col-xs-5 ukds-product-top-left">
            <?php do_action( 'ltd_tickets_featured_image' ); ?>
        </div>
        <div class="ukds-col-xs-7 ukds-product-top-right">
            <?php the_title( '<h1 class="product_title entry-title">', '</h1>' ); ?>
            <?php do_action( 'ltd_tickets_product_tagline' ); ?>
            <?php do_action( 'ltd_tickets_product_offer_tag' ); ?>
            <?php do_action( 'ltd_tickets_product_from_price' ); ?>
            <?php do_action( 'ltd_tickets_booking_link'); ?>
        </div>
        <div class="ukds-clearfix"></div>
    </div>
    <div class="ukds-product-bottom  ">
        <div class='ukds-col-sm-7 ukds-col-sm-push-5 ukds-product-single-right entry-content'>
            <?php do_action('ltd_tickets_product_special_offer_long'); ?>
            <?php the_content(); ?>
            <?php do_action( 'ltd_tickets_booking_link'); ?>
        </div>
        <div class='ukds-col-sm-5 ukds-col-sm-pull-7 ukds-product-single-left'>
            <?php do_action('ltd_tickets_product_gallery'); ?>
            <?php do_action('ltd_tickets_product_meta'); ?>
            <?php do_action('ltd_tickets_product_minimum_age'); ?>
            <?php do_action('ltd_tickets_product_important_notice'); ?>
        </div>

    </div>
    <?php endwhile; ?>
    </div>
</div>
<?php
get_footer();