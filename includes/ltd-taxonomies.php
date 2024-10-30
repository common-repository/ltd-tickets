<?php
/**
 * UKDS Taxonomies
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/helpers
 * @author     Ben Campbell <ben@ukds.co>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
	Register Product Category Taxonomy
*/
$labels = array(
	'name'              => _x( 'Product Categories', 'taxonomy general name' ),
	'singular_name'     => _x( 'Product Category', 'taxonomy singular name' ),
	'search_items'      => __( 'Search Product Categories' ),
	'all_items'         => __( 'All Product Categories' ),
	'parent_item'       => __( 'Parent Product Category' ),
	'parent_item_colon' => __( 'Parent Product Category:' ),
	'edit_item'         => __( 'Edit Product Category' ),
	'update_item'       => __( 'Update Product Category' ),
	'add_new_item'      => __( 'Add New Product Category' ),
	'new_item_name'     => __( 'New Product Category Name' ),
	'not_found'          => __( 'No Product Categories found.'),
	'menu_name'         => __( 'Product Categories' ),
);
$args = array(
	'hierarchical'          => true,
	'labels'                => $labels,
	'show_ui'               => true,
	'show_admin_column'     => true,
	'query_var'             => true,
	'rewrite'           	=> array( 'slug' => 'whats-on' ),
);
register_taxonomy( 'ukds-product-category', 'ukds-products', $args );
