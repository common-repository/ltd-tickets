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
    <h2><?php _e( 'Data Synchonisation Settings' ); ?></h2>
    <?php settings_errors(); ?>
    <form method="post" name="ltd_settings" action="options.php">
        <input type="hidden" name="admin_page" value="sync" />
        <?php
            //Grab all options
            $options = get_option($this->plugin_name);
            $sync_options = $options['sync'];

            settings_fields($this->plugin_name);
            do_settings_sections($this->plugin_name);
        ?>

            <div id="synchronisation" class="ukdstab active">
            <div class="metabox-holder">
                <div class="postbox" id="partner-type">
                    <h3 class="hndle"><?php _e( "Auto Import Settings" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><span><?php _e( "Automatically Import New Products" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input type="checkbox" style="margin-top:6px" id="<?php echo $this->plugin_name; ?>-import-products" name="<?php echo $this->plugin_name; ?>[import_products]" <?php echo ($sync_options['import_products'] == 1 ? " checked " : ""); ?> value="1" /><br /><br />
                                        <span class="description"><?php _e('Check this box if you would like to automatically import new products from London Theatre Direct as they become available.', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr class="" ukds-toggle-checked="#<?php echo $this->plugin_name; ?>-import-products">
                                    <th><span><?php _e( "Imported Product Status" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <select name="<?php echo $this->plugin_name; ?>[import_products_status]" id="<?php echo $this->plugin_name; ?>-import-products-status">
                                            <option value="draft" <?php echo ($sync_options['import_products_status'] == "draft" ? 'selected' : ''); ?>><?php _e( "Draft" , $this->plugin_name ); ?></option>
                                            <option value="pending" <?php echo ($sync_options['import_products_status'] == "pending" ? 'selected' : ''); ?> ><?php _e( "Pending" , $this->plugin_name ); ?></option>
                                            <option value="publish" <?php echo ($sync_options['import_products_status'] == "publish" ? 'selected' : ''); ?>><?php _e( "Published" , $this->plugin_name ); ?></option>
                                        </select><br /><br />
                                        <span class="description"><?php _e('Select the status you would like imported products to be created with.<br /><br /><strong>Pending</strong> will import and create products but leave them \'pending\' (recommended if you plan on adding your own content, better for SEO).<br /><strong>Published</strong> will import, create and publish new products automatically (better for autonomy, worse for SEO).', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span><?php _e( "Automatically Import New Venues" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input type="checkbox" style="margin-top:6px" id="<?php echo $this->plugin_name; ?>-import-venues" name="<?php echo $this->plugin_name; ?>[import_venues]" <?php echo ($sync_options['import_venues'] == 1 ? " checked " : ""); ?> value="1" /><br /><br />
                                        <span class="description"><?php _e('Check this box if you would like to automatically import new venues from London Theatre Direct as they become available.', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr class="" ukds-toggle-checked="#<?php echo $this->plugin_name; ?>-import-venues">
                                    <th><span><?php _e( "Imported Venue Status" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <select name="<?php echo $this->plugin_name; ?>[import_venues_status]" id="<?php echo $this->plugin_name; ?>-import-venues-status">
                                            <option value="draft" <?php echo ($sync_options['import_venues_status'] == "draft" ? 'selected' : ''); ?>><?php _e( "Draft" , $this->plugin_name ); ?></option>
                                            <option value="pending" <?php echo ($sync_options['import_venues_status'] == "pending" ? 'selected' : ''); ?>><?php _e( "Pending" , $this->plugin_name ); ?></option>
                                            <option value="publish" <?php echo ($sync_options['import_venues_status'] == "publish" ? 'selected' : ''); ?>><?php _e( "Published" , $this->plugin_name ); ?></option>
                                        </select><br /><br />
                                        <span class="description"><?php _e('Select the status you would like imported venues to be created with.<br /><br /><strong>Pending</strong> will import and create venues but leave them \'pending\' (recommended if you plan on adding your own content, better for SEO).<br /><strong>Published</strong> will import, create and publish new venues automatically (better for autonomy, worse for SEO).', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span><?php _e( "Automatically Import New Categories" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input type="checkbox" style="margin-top:6px" id="<?php echo $this->plugin_name; ?>-import-categories" name="<?php echo $this->plugin_name; ?>[import_categories]" <?php echo ($sync_options['import_categories'] == 1 ? " checked " : ""); ?> value="1" /><br /><br />
                                        <span class="description"><?php _e('Check this box if you would like to automatically new categories.', $this->plugin_name); ?></span>
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td>
                                        <input class="button button-primary" type="submit" value="<?php  _e( "Save All Changes" , $this->plugin_name ); ?>" />
                                        <span class="spinner" style="float:none;position:relative;"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="postbox" id="update-settings">
                    <h3 class="hndle"><?php _e( "Update Settings" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><span><?php _e( "Update Frequency" , $this->plugin_name ); ?></span></th>
                                <td>
                                    <select name="<?php echo $this->plugin_name; ?>[update_frequency]" id="<?php echo $this->plugin_name; ?>-update-frequency">
                                        <option value="hourly" <?php echo ($sync_options['update_frequency'] == "hourly" ? 'selected' : ''); ?> <?php echo ($options['config']['partner_type'] == 'api' ? '' : 'disabled'); ?>><?php _e( "Hourly (only available to API Users)" , $this->plugin_name ); ?></option>
                                        <option value="daily" <?php echo ($sync_options['update_frequency'] == "daily" ? 'selected' : ''); ?>><?php _e( "Daily (recommended)" , $this->plugin_name ); ?></option>
                                        <option value="weekly" <?php echo ($sync_options['update_frequency'] == "weekly" ? 'selected' : ''); ?>><?php _e( "Weekly" , $this->plugin_name ); ?></option>
                                        <option value="none" <?php echo ($sync_options['update_frequency'] == "none" ? 'selected' : ''); ?>><?php _e( "Only Manually" , $this->plugin_name ); ?></option>
                                    </select><br /><br />
                                </td>
                            </tr>
                            <tr>
                                <th><span><?php _e( "Product Update Values" , $this->plugin_name ); ?></span></th>
                                <td>
                                    <p><?php _e('Select which values you would like to be automatically updated.<br /><strong>Please Note:</strong> If you make changes to a product value that is checked below then those changes will be overwritten when updates occur.', $this->plugin_name); ?></p><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_tagline]" <?php echo ($sync_options['product_update_tagline'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Tagline" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_main_image_url]" <?php echo ($sync_options['product_update_main_image_url'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Main Image URL" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_image_gallery]" <?php echo ($sync_options['product_update_image_gallery'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Image Gallery" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_running_time]" <?php echo ($sync_options['product_update_running_time'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Running Time" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_minimum_age]" <?php echo ($sync_options['product_update_minimum_age'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Minimum Age" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_start_date]" <?php echo ($sync_options['product_update_start_date'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Start Date" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_end_date]" <?php echo ($sync_options['product_update_end_date'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "" , $this->plugin_name ); ?>End Date</label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_important_notice]" <?php echo ($sync_options['product_update_important_notice'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Important Notice" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_minimum_price]" <?php echo ($sync_options['product_update_minimum_price'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Minimum Price" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_current_price]" <?php echo ($sync_options['product_update_current_price'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Current Price" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_offer_price]" <?php echo ($sync_options['product_update_offer_price'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Offer Price" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_short_offer_text]" <?php echo ($sync_options['product_update_short_offer_text'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Short Offer Text" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_long_offer_text]" <?php echo ($sync_options['product_update_long_offer_text'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Long Offer Text" , $this->plugin_name ); ?></label><br />
                                    <br /><hr /><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[product_update_content]" <?php echo ($sync_options['product_update_content'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Full Product Description" , $this->plugin_name ); ?></label><p style="color:#900"><strong><?php _e('Warning'); ?>:</strong> <?php _e('If you tick \'Full Product Description\' then changes you make to product descriptions will be overwritten whenever the plugin synchronises!'); ?></p><br />
                                    <hr />
                                </td>
                            </tr>
                            <tr>
                                <th><span><?php _e( "Venue Update Values" , $this->plugin_name ); ?></span></th>
                                <td>
                                    <p><?php _e('Select which values you would like to be automatically updated.<br /><strong>Please Note:</strong> Any changes that you make to venue values checked below will be lost when updates occur.', $this->plugin_name); ?></p><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[venue_update_address]" <?php echo ($sync_options['venue_update_address'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Address" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[venue_update_city]" <?php echo ($sync_options['venue_update_city'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "City" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[venue_update_postcode]" <?php echo ($sync_options['venue_update_postcode'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Postcode" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[venue_update_nearest_tube]" <?php echo ($sync_options['venue_update_nearest_tube'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Nearest Tube" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[venue_update_nearest_train]" <?php echo ($sync_options['venue_update_nearest_train'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Nearest Train" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[venue_update_seating_plan]" <?php echo ($sync_options['venue_update_seating_plan'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Seating Plan" , $this->plugin_name ); ?></label><br />
                                    <label><input type="checkbox" name="<?php echo $this->plugin_name; ?>[venue_update_image_url]" <?php echo ($sync_options['venue_update_image_url'] == 1 ? " checked " : ""); ?> value="1" /> <?php  _e( "Image URL" , $this->plugin_name ); ?></label><br />
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td>
                                    <input class="button button-primary" type="submit" value="<?php  _e( "Save All Changes" , $this->plugin_name ); ?>" />
                                    <span class="spinner" style="float:none;position:relative;"></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="postbox" id="manual-update">
                    <h3 class="hndle"><?php _e( "Manual Update" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><span><?php _e( "Update All" , $this->plugin_name ); ?></span></th>
                                <td>
                                    <p><br /><?php _e("<strong>Please Note:</strong> running a full import using 'Update All' can take a few minutes, so please be patient.", $this->plugin_name); ?></p>
                                    <p class="submit">
                                        <input name="UpdateAll" type="submit" class="button button-primary" value="<?php  _e( "Update All" , $this->plugin_name ); ?>" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th><span><?php _e( "Update Products" , $this->plugin_name ); ?></span></th>
                                <td>
                                    <p class="submit" data-ui="HideOnFetchProducts">
                                        <input name="FetchUpdateProducts" type="submit" class="button button-secondary" value="<?php  _e( "Fetch Imported Products" , $this->plugin_name ); ?>" />
                                        <input name="UpdateProducts" type="submit" class="button button-primary" value="<?php  _e( "Update All Products" , $this->plugin_name ); ?>" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                                    </p>
                                    <div id="import-products-container" class="import-container" data-ui="FetchProducts">
                                        <div id="import-products-header" class="import-table-header">
                                            <h3><?php _e("Product List", $this->plugin_name); ?></h3>
                                            <p><?php _e("Select the Products you'd like to update then click '<strong>Update Selected Products</strong>', or click '<strong>Update All Products</strong>'.", $this->plugin_name); ?></p>
                                        </div>
                                        <div id="import-products-body" class="import-table-body">
                                        </div>
                                        <input name="UpdateSelectedProducts" type="submit" class="button button-primary" value="<?php  _e( "Update Selected Products" , $this->plugin_name ); ?>" />
                                        <input name="UpdateProducts" type="submit" class="button button-primary" value="<?php  _e( "Update All Products" , $this->plugin_name ); ?>" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><span><?php _e( "Update Venues" , $this->plugin_name ); ?></span></th>
                                <td>
                                    <p class="submit" data-ui="HideOnFetchVenues">
                                        <input name="FetchUpdateVenues" type="submit" class="button button-secondary" value="<?php  _e( "Fetch Imported Venues" , $this->plugin_name ); ?>" />
                                        <input name="UpdateVenues" type="submit" class="button button-primary" value="<?php  _e( "Update All Venues" , $this->plugin_name ); ?>" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                                    </p>
                                    <div id="import-venues-container" class="import-container" data-ui="FetchVenues">
                                        <div id="import-venues-header" class="import-table-header">
                                            <h3><?php _e("Venues List", $this->plugin_name); ?></h3>
                                            <p><?php _e("Select the Venues you'd like to update then click '<strong>Update Selected Venues</strong>', or click '<strong>Update All Venues</strong>'.", $this->plugin_name); ?></p>
                                        </div>
                                        <div id="import-products-body" class="import-table-body">
                                        </div>
                                        <input name="UpdateSelectedVenues" type="submit" class="button button-primary" value="<?php  _e( "Update Selected Venues" , $this->plugin_name ); ?>" />
                                        <input name="UpdateVenues" type="submit" class="button button-primary" value="<?php  _e( "Update All Venues" , $this->plugin_name ); ?>" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>


            </div>
        </div>

    </form>
    <?php require_once( 'ltd-tickets-debug-part.php' ); ?>

</div>