<?php
$args = array(
	'prev_text'             => __('&nbsp;'),
	'next_text'             => __('&nbsp;'),
	'show_all'			    => true,
	'type'				    => 'list',
);

echo '<div class="ukds-col-md-12">';
echo '<div class="ukds-product-pagination">';
echo paginate_links( $args );
echo '</div>';
echo '</div>';