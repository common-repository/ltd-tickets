<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ukdesignservices.com
 * @since      1.0.0
 *
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/admin/partials
 */
?>

<div class="wrap">
    <h2><?php _e( 'LTD Tickets Shortcodes' ); ?></h2>
    <?php
        $options = get_option($this->plugin_name);

    ?>
    <form method="post" name="ltd_settings" action="options.php">
        <input type="hidden" name="admin_page" value="shortcodes" />

        <div id="shortcodes" class="ukdstab active">
            <div class="metabox-holder" id="product-shortcodes">
                <div class="postbox">
                    <h3 class="hndle">
                        <?php _e( "Book Tickets Button Generator" , $this->plugin_name ); ?>
                    </h3>
                    <div class="inside">
                        <p><?php _e( "If you already have product pages on your website, you can create use this tool to create a 'shortcode' for a 'Book Tickets' button that can be added to your existing product page.<br />This tool can also be used to create book buttons for products that you haven't imported." ); ?>
                        </p>
                        <table class="form-table shortcode-table">
                            <tbody>
                                <tr>
                                    <th>
                                        <span>Select a show</span>
                                    </th>
                                    <td>
                                        <select class="regular-text" ukds-ui="SelFetchProducts">
                                            <option value="">Loading...</option>
                                        </select>
                                        <span class="spinner is-active" style="float:none;position:relative;top:-3px"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <span>Button Text</span>
                                    </th>
                                    <td>
                                        <input id="product-shortcode-generation-button-text" type="text" value="Book Tickets" placeholder="Book Tickets" class="regular-text"/>
                                        <br />
                                        <span class="description"><?php _e( 'Enter the text you would like to appear on the button.' , $this->plugin_name ); ?>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="shortcode-generation-area" id="ProductShortcodeArea" style="display:none;">
                            <h3>Your Shortcode</h3>
                            <div class="shortcode-generation" id="product-shortcode-generation">[ltd_button id="<span ukds-ui="SelFetchProducts-sync"></span>" text="<span ukds-ui="stringsync" ukds-noencode="1" ukds-format="$1" ukds-default="Book Tickets" ukds-sync="#product-shortcode-generation-button-text">Book Tickets</span>"]</div>
                            <br />
                            <a href="javascript:void(0);"  class="button button-primary" onclick="ui.direct.makeCopy('product-shortcode-generation')">Copy to Clipboard</a>
                            <br />
                            <p class="description">
                                Just copy the code above and paste it into a post or page to create a 'Book Tickets' link to the selected show. <a href="https://en.support.wordpress.com/shortcodes/" target="_blank">Find out more about Shortcodes</a>
                            </p>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
      
    </form>
    <?php require_once( 'ltd-tickets-debug-part.php' ); ?>

</div>