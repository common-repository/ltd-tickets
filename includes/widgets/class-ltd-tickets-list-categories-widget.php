<?php

/**
 * Product Categories Widget.
 *
 * Builds and controls the Product Category Widget.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */
class Ltd_Tickets_List_Categories_Widget extends WP_Widget
{

    private $plugin_options;

    function __construct() {
        parent::__construct(
            'ltd_tickets_product_category_widget',
            __('LTD Product Categories', 'ltd-tickets' ),
            array (
                'description' => __( 'Creates a widgetised list of product categories.', 'ltd-tickets' )
            )
        );
        $this->plugin_options = get_option('ltd-tickets');

    }

    function form( $instance ) {

        $defaults = array(
            'title'         => '',
            'hide_empty'    => 1
        );
        $instance = wp_parse_args( $instance, $defaults );
        $title = $instance[ 'title' ];
        $hide_empty = $instance[ 'hide_empty' ];

?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
                <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" <?php echo ($hide_empty == 1 ? 'checked' : ''); ?> value="1" />
                    Hide empty categories
                </label>
            </p>

        <?php
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] =  $new_instance[ 'title' ];
        $instance[ 'hide_empty' ] = $new_instance[ 'hide_empty' ];

        return $instance;
    }

    function widget( $args, $instance ) {
        echo $args['before_widget'];
        echo $args['before_title'] . $instance[ 'title' ] .  $args['after_title'];

        $tax = get_taxonomy($this->plugin_options['config']['product_category_taxonomy']);
        $tax_slug = $tax->rewrite['slug'];

        $terms = get_terms(
            array (
                 'taxonomy'          => $this->plugin_options['config']['product_category_taxonomy'],
                 'hide_empty'        => ($instance[ 'hide_empty' ] == 1 ? true : false),
                 'parent'            => 0
            )
        );
        if (!empty($terms)) {
            echo "<ul>";
            foreach($terms as $term) {
                echo "<li><a href='/$tax_slug/" . $term->slug . "/'>" . $term->name . "</a></li>";
            }
            echo "</ul>";
        }
        echo $args['after_widget'];
    }

}