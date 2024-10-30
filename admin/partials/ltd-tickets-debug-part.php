<?php
/**
 * Debug information
 *
 * @link       https://ukdesignservices.com
 * @since      1.0.0
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/admin/partials
 */
?>
<?php
if( WP_DEBUG === true ) { ?>
<div class="metabox-holder" id="debug">
    <div class="postbox">
        <h3 class="hndle">
            <?php _e( "Debug" , $this->plugin_name ); ?>
        </h3>
        <div class="inside">
            <p>You are seeing this because you have WP_DEBUG enabled. You can disable this in your wp-config.php file.</p>
            <?php highlight_string(var_export($options, true)); ?>
        </div>
    </div>
</div>
<?php } ?>