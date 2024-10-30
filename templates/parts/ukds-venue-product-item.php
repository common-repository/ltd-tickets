<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ukds
 */

$default_class = "ukds-col-product-grid";
$endDate = strtotime(get_post_meta(get_the_ID(), 'end_date', true));
$nowDate = strtotime('now');
$inPast = ($endDate < $nowDate ? true : false);
$special = get_post_meta(get_the_ID(), "short_offer_text", true);
if ($special == "" && $endDate == $nowDate) $special = __('Ends Today!', LTD_PLUGIN_NAME);

?>

<div class="<?php echo $default_class; ?>" itemscope="" itemtype="http://schema.org/Product">
    <div class="ukds-product-grid-item">
        <a href="<?php echo get_the_permalink(); ?>" class="ukds-product-grid-image" title="<?php echo get_the_title(); ?>" itemprop="url">
            <?php do_action( 'ltd_tickets_featured_image' ); ?>
            <?php if ($special != "") : ?>
            <span class='ukds-product-grid-special'>
                <?php echo $special; ?>
            </span>
            <?php endif; ?>
        </a>
        <span class="ukds-product-grid-details">
            <span class="ukds-product-grid-title" itemprop="name">
                <a href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title(); ?>" itemprop="url">
                    <?php echo get_the_title(); ?>
                </a>
            </span>
            <span class="ukds-product-grid-venue">
                <?php do_action("ltd_tickets_product_venue_link", get_the_ID()); ?>
            </span>
            <a href="<?php echo get_the_permalink(); ?>" class="ukds-product-grid-price">
                <?php
                if ($inPast) :
                    echo "<span ukds-ui='finished'></span>" . __('Show has finished', LTD_PLUGIN_NAME) . ".";
                else :
                    do_action("ltd_tickets_product_grid_from_price", get_the_ID());
                endif;
                ?>
            </a>
        </span>
    </div>
</div>