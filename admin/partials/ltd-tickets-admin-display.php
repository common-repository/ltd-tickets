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
    <h2><?php _e( 'London Theatre Direct Integration Settings' ); ?></h2>
    <?php settings_errors(); ?>
    <form method="post" name="ltd_settings" action="options.php">
        <input type="hidden" name="admin_page" value="config" />
        <?php
            //Grab all options
            $options = get_option($this->plugin_name);


            // Plugin Config
            $config = $options['config'];

            // Config Settings
            $config_disable_styles              = $config['disable_styles'];
            $config_partner_type                = $config['partner_type'];
            $config_redirect_time               = $config['redirect_time'];
            $config_api_target                  = $config['api_target'];
            $config_product_archive             = $config['product_archive'];
            $config_venue_archive               = $config['venue_archive'];


            // Partner Config
            $partner = $options['partner'];

            // AWIN Keys
            $partner_awin_id                    = $partner['awin_id'];
            $partner_awin_clickref              = $partner['awin_clickref'];

            // Whitelabel ID
            $partner_whitelabel_id              = $partner['whitelabel_id'];

            // API Keys
            $partner_api_key_live               = $partner['api_key_live'];
            $partner_api_key_sandbox            = $partner['api_key_sandbox'];


            // Design Config
            $styles = $options['styles'];

            // Design Settings
            $style_primary_colour 	            = $styles['primary_colour'];
			$style_secondary_colour             = $styles['secondary_colour'];
			$style_primary_button_background	= $styles['primary_button_background'];
			$style_primary_button_text_colour	= $styles['primary_button_text_colour'];
            $style_primary_button_css_class     = $styles['primary_button_css_class'];
            $style_secondary_button_background  = $styles['secondary_button_background'];
            $style_secondary_button_text_colour = $styles['secondary_button_text_colour'];
            $style_secondary_button_css_class   = $styles['secondary_button_css_class'];
            $style_layout                       = $styles['layout'];
            $style_custom_css                   = $styles['custom_css'];
            $style_max_width                    = $styles['layout_max_width'];

            // Advanced Settings
            $config_product_post_type           = $config['product_post_type'];
            $config_product_category_taxonomy   = $config['product_category_taxonomy'];
            $config_venue_post_type             = $config['venue_post_type'];

            // Template Config
            $templates = $options['templates'];
            $template_product_template            = $templates['product_template'];
            $template_product_archive             = $templates['product_archive'];
            $template_venue_template              = $templates['venue_template'];
            $template_venue_archive               = $templates['venue_archive'];
            $template_category_template           = $templates['category_template'] ;
            $template_booking_template            = $templates['booking_template'];
            $template_basket_template             = $templates['basket_template'];
            $template_checkout_template           = $templates['checkout_template'];
            $template_confirmation_template       = $templates['confirmation_template'];

            $error_msg = array();
            $api_key_sandbox_css = "";
            $api_key_live_css = "";
            $whitelabel_input_css = "";
            if ($config_partner_type == "api") {
                $api = new LTD_Tickets_Integration($this->plugin_name, $this->version);
                if ($partner_api_key_sandbox != "") {
                    if ($api->heartbeat($partner_api_key_sandbox,$options['api']['url_sandbox']) === true) {
                        $api_key_sandbox_css = 'success';
                    } else {
                        $api_key_sandbox_css = 'error';
                        $error_msg[] = __("You have entered an invalid Sandbox API Key.", $this->plugin_name);
                    }
                } else {
                    $api_key_sandbox_css = "error";
                    $error_msg[] = __("You have not entered a Sandbox API Key.", $this->plugin_name);
                }
                if ($partner_api_key_live != "") {
                    if ($api->heartbeat($partner_api_key_live,$options['api']['url_live']) === true) {
                        $api_key_live_css = "success";
                    } else {
                        $api_key_live_css = "error";
                        $error_msg[] = __("You have entered an invalid Live API Key.", $this->plugin_name);
                    }
                } else {
                    $api_key_live_css = "error";
                    $error_msg[] = __("You have not entered a Live API Key.", $this->plugin_name);
                }
            } else if ($config_partner_type == "whitelabel") {
                if ($partner_whitelabel_id == "") {
                    $whitelabel_input_css = "error";
                    $error_msg[] = __("You have not entered a whitelabel sub domain.", $this->plugin_name);   
                }
            }


			settings_fields($this->plugin_name);
	        do_settings_sections($this->plugin_name);

            if (!empty($error_msg)) : ?>
                <div class="inline-notice notice-error">
                    <h3><?php _e("Warning", $this->plugin_name); ?></h3>
                    <p><strong><?php _e("Please correct the following errors before continuing:", $this->plugin_name); ?></strong></p>
                    <ul>
                    <?php foreach($error_msg as $msg) : ?>
                        <li>- <?php echo $msg; ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif;

        ?>


        <h2><?php _e( 'Plugin Settings', $this->plugin_name ); ?>
        </h2>
        <h2 class="nav-tab-wrapper" id="ukds-tabs">
            <a href="#top#partner" class="nav-tab nav-tab-active" id="partner-tab"><?php _e("Partner Settings", $this->plugin_name); ?></a>
            <?php if($config_partner_type != "") : ?>
                <a href="#top#behaviour" class="nav-tab" id="behaviour-tab"><?php _e("Behaviour + Design", $this->plugin_name); ?></a>
                <a href="#top#import" class="nav-tab" id="import-tab"><?php _e("Import Settings", $this->plugin_name); ?></a>
                <a href="#top#advanced" class="nav-tab" id="advanced-tab"><?php _e("Advanced Settings", $this->plugin_name); ?></a>
            <?php endif; ?>
        </h2>


        <div id="partner" class="ukdstab active">
            <div class="metabox-holder">
                <div class="postbox" id="partner-type">
                    <h3 class="hndle"><?php _e( "Partner Type" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <p><?php _e( "Click the option that best suits you:" ); ?></p>

                        <input id="<?php echo $this->plugin_name; ?>-partner_awin" class="<?php echo $this->plugin_name; ?>-partner-picker" name="<?php echo $this->plugin_name; ?>[config_partner_type]" type="radio" value="awin" <?php echo ( $config_partner_type == "awin" ? " checked " : "" ); ?> />
                        <label class="partner-type-select partner-type-awin" for="<?php echo $this->plugin_name; ?>-partner_awin">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="AWIN" />
                            <span><?php _e( 'AWIN Partner' , $this->plugin_name  ); ?></span>
                        </label>

                        <input id="<?php echo $this->plugin_name; ?>-partner_whitelabel" class="<?php echo $this->plugin_name; ?>-partner-picker" name="<?php echo $this->plugin_name; ?>[config_partner_type]" type="radio" value="whitelabel" <?php echo ( $config_partner_type == "whitelabel" ? " checked " : "" ); ?> />
                        <label class="partner-type-select partner-type-whitelabel" for="<?php echo $this->plugin_name; ?>-partner_whitelabel">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="Whitelabel" />
                            <span><?php _e( 'Whitelabel Partner' , $this->plugin_name  ); ?></span>
                        </label>

                        <input id="<?php echo $this->plugin_name; ?>-partner_api" class="<?php echo $this->plugin_name; ?>-partner-picker" name="<?php echo $this->plugin_name; ?>[config_partner_type]" type="radio" value="api" <?php echo ( $config_partner_type == "api" ? " checked " : "" ); ?> />
                        <label class="partner-type-select partner-type-api" for="<?php echo $this->plugin_name; ?>-partner_api">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="Whitelabel" />
                            <span><?php _e( 'API User' , $this->plugin_name  ); ?></span>
                        </label>

                        <input id="<?php echo $this->plugin_name; ?>-partner_none" class="<?php echo $this->plugin_name; ?>-partner-picker" name="<?php echo $this->plugin_name; ?>[config_partner_type]" type="radio" value="none" <?php echo ( $config_partner_type == "none"  ? " checked " : "" ); ?> />
                        <label class="partner-type-select partner-type-none" for="<?php echo $this->plugin_name; ?>-partner_none">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="Not a partner" />
                            <span><?php _e( 'Not a partner' , $this->plugin_name  ); ?></span>
                        </label>

                    </div>
                </div>
            </div>
            <div class="metabox-holder partner-option" id="partner-awin">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "AWIN Details" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><span>AWIN ID</span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-partner_awin_id" name="<?php echo $this->plugin_name; ?>[partner_awin_id]" type="text" value="<?php echo (!empty($partner['awin_id']) ? esc_attr($partner['awin_id']) : ""); ?>" placeholder="<?php echo $options['default']['awin_id']; ?>" class="regular-text"><br>
                                        <span class="description"><?php _e( 'Enter your AWIN partner ID.', $this->plugin_name ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span>Click Reference Prefix <em>(optional)</em></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-partner_awin_clickref" name="<?php echo $this->plugin_name; ?>[partner_awin_clickref]" type="text" value="<?php echo (!empty($partner['awin_clickref']) ? esc_attr($partner['awin_clickref']) : "ltd_wp_plugin"); ?>" placeholder="eg: ltd_wp_plugin" class="regular-text"><br>
                                        <span class="description"><?php _e( "Enter a 'click reference' prefix.  This will appear in AWIN." , $this->plugin_name ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input class="button button-primary" type="submit" value="Save All Changes" /><span class="spinner" style="float:none;position:relative;"></span>
                                        <br /><br />
                                        <p>Not part of the AWIN programme?  <a href="http://www.awin1.com/cread.php?awinmid=3&awinaffid=140777&clickref=&p=https%3A%2F%2Fui.awin.com%2Fmerchant-profile%2F610" target="_blank">Join now</a>.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="metabox-holder partner-option" id="partner-whitelabel">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "Whitelabel Details" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><span>Whitelabel Subdomain</span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-partner_whitelabel_id" name="<?php echo $this->plugin_name; ?>[partner_whitelabel_id]" type="text" value="<?php echo esc_attr($partner_whitelabel_id); ?>" placeholder="your_company" class="regular-text <?php echo $whitelabel_input_css; ?>">.londontheatredirect.com<br>
                                        <span class="description"><?php _e( "This is the unique identifier for your whitelabel, eg: https://<strong>your_company</strong>.londontheatredirect.com/" , $this->plugin_name ); ?></span><br /><br />
                                        Your whitelabel URL: <strong><span class="hint" ukds-ui="stringsync" ukds-format="https://$1.londontheatredirect.com" ukds-default="your_company" ukds-sync="#<?php echo $this->plugin_name; ?>-partner_whitelabel_id">https://<?php echo ($partner_whitelabel_id != "" ? esc_attr($partner_whitelabel_id) : 'your_company'); ?>.londontheatredirect.com</span></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input class="button button-primary" type="submit" value="Save All Changes" /><span class="spinner" style="float:none;position:relative;"></span>
                                        <br /><br />
                                        <p>Not got a London Theatre Direct whitelabel?  <a href="https://partners.londontheatredirect.com/#solution-whitelabel" target="_blank">Create one now</a>.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="metabox-holder partner-option" id="partner-api">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "API Credentials" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><span>Sandbox API Key</span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-partner_api_key_sandbox" name="<?php echo $this->plugin_name; ?>[partner_api_key_sandbox]" type="text" value="<?php echo esc_attr($partner_api_key_sandbox); ?>" placeholder="" class="regular-text <?php echo esc_attr($api_key_sandbox_css); ?>"><br>
                                        <span class="description"><?php _e( 'Enter your <em>Sandbox</em> API Key. This is required to use the plugin as an API Partner.' , $this->plugin_name ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span>Live API Key</span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-partner_api_key_live" name="<?php echo $this->plugin_name; ?>[partner_api_key_live]" type="text" value="<?php echo esc_attr($partner_api_key_live); ?>" placeholder="" class="regular-text <?php echo esc_attr($api_key_live_css); ?>"><br>
                                        <span class="description"><?php _e( 'Enter your <em>Live</em> API Key. This is required to use the plugin as an API Partner.' , $this->plugin_name ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input class="button button-primary" type="submit" value="Save All Changes" /><span class="spinner" style="float:none;position:relative;"></span>
                                        <br /><br />
                                        <p>Not got API Keys yet?  <a href="https://iodocs.londontheatredirect.com/member/register" target="_blank">Get your keys</a>.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="metabox-holder partner-option" id="partner-none">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "Not a partner?" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <p><?php _e( "You do not have to be a partner to use this plugin, but to gain the maximum benefit it is strongly advised. If you would like to start earning commission from sales made through the plugin then you must first become a partner. To become a direct partner visit our <a href='https://partners.londontheatredirect.com/?utm_source=wp_plugin&utm_campaign=sign_up' target='_blank'>Partner Sign up Website</a>.  Alternatively, if you are already an AWIN publisher, sign up to <a href='http://www.awin1.com/cread.php?awinmid=3&awinaffid=140777&clickref=&p=https%3A%2F%2Fui.awin.com%2Fmerchant-profile%2F610' target='_blank'>our affiliate programme</a>." , $this->plugin_name ); ?></p>
                        <input class="button button-primary" type="submit" value="Continue Anyway" /><span class="spinner" style="float:none;position:relative;"></span>
                    </div>
                </div>
            </div>

        </div>

        <div id="behaviour" class="ukdstab">
            <div class="metabox-holder" id="behaviour-api">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "Target API" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><span>Select API to target</span></th>
                                    <td>
                                        <select id="<?php echo $this->plugin_name; ?>-config_api_target" name="<?php echo $this->plugin_name; ?>[config_api_target]" type="text" class="regular-text">
                                            <option value="sandbox" <?php if($config_api_target == "sandbox") echo 'selected'; ?>>Sandbox API</option>
                                            <option value="live" <?php if($config_api_target == "live") echo 'selected'; ?>>Live API</option>
                                        </select><br />
                                        <span class="description"><?php _e( 'Select which API you would like the plugin to use. Make sure this is set to <strong>Live API</strong> when your website is in production mode.' , $this->plugin_name ); ?></span>
                                        <div>
                                            <br /><?php _e("<strong>Please Note:</strong> <strong><em>Products imported from the Sandbox API may not be up to date and could be missing important information and media images</em></strong>.<br />If you develop your website using the Sandbox API then make sure you delete and re-import Products, Venues and Categories from the Live API before publishing your website."); ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input class="button button-primary" type="submit" value="Save All Changes" /><span class="spinner" style="float:none;position:relative;"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <!--<div class="metabox-holder" id="behaviour-redirect">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "Redirect Behaviour" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <td  width="100%">
                                        <table width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="left" width="33.3%" style="display:table-cell;width:33.3%;padding:10px">Quick</td>
                                                    <td align="center" width="33.3%" style="display:table-cell;width:33.3%;padding:10px">Basket</td>
                                                    <td align="right" width="33.3%" style="display:table-cell;width:33.3%;padding:10px">Full</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <input type="range" min="1" max="3" step="1" value="<?php echo (!empty($config_redirect_time) ? $config_redirect_time : 2); ?>" id="<?php echo $this->plugin_name; ?>-config_redirect_time" name="<?php echo $this->plugin_name; ?>[config_redirect_time]" class="ranger" />
                                        <div class="redirection-explanations" style="margin-top:15px;display:block">
                                            <span class="description" data-value="1" style="display:none"><?php _e('This is the quickest integration. Customers will be transferred across to London Theatre Direct at the Calendar stage.', $this->plugin_name); ?>
                                            </span>
                                            <span class="description" data-value="2"><?php _e('<strong>Default:</strong> Tickets are selected and added to the shopping basket on your website, then customers are transferred to London Theatre Direct to review their tickets and complete the transaction.', $this->plugin_name); ?>
                                            </span>
                                            <span class="description" data-value="3" style="display:none"><?php _e('<strong style="color:red">Requires SSL</strong> - Customer details are taken on your website.  Only use this option if you have a secure certificate installed on your domain and your website can run on https.', $this->plugin_name); ?>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="button button-primary" type="submit" value="Save All Changes" /><span class="spinner" style="float:none;position:relative;"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>-->

         <?php /*   <div class="metabox-holder" id="page-selection">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "Page Selection" , $this->plugin_name ); ?></h3>
                    <?php 
                    $args = array(
                        'post_type'         => 'page',
                        'posts_per_page'    => -1,
                        'publish_status'    => array('publish', 'pending')
                    );

                    $posts = get_posts($args);
                    ?>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><span>Tickets Page</span></th>
                                    <td>
                                        <select id="<?php echo $this->plugin_name; ?>-config_product_archive" name="<?php echo $this->plugin_name; ?>[config_product_archive]" type="text" class="regular-text">
                                            <option value="">Please select a page</option>
                                            <?php foreach($posts as $post) : ?>
                                                    <option value="<?php echo $post->ID; ?>" <?php echo ($config_product_archive === $post->ID ? 'selected' : ''); ?>><?php echo $post->post_title; ?></option>
                                            <?php endforeach; ?>
                                        </select><br />
                                        <span class="description"><?php _e( 'This will be the main tickets archive page, where all products can be displayed' , $this->plugin_name ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span>Venues Page</span></th>
                                    <td>
                                        <select id="<?php echo $this->plugin_name; ?>-config_venue_archive" name="<?php echo $this->plugin_name; ?>[config_venue_archive]" type="text" class="regular-text">
                                            <option value="">Please select a page</option>
                                            <?php foreach($posts as $post) : ?>
                                            <option value="<?php echo $post->ID; ?>" <?php echo ($config_venue_archive === $post->ID ? 'selected' : ''); ?>><?php echo $post->post_title; ?></option>
                                            <?php endforeach; ?>
                                        </select><br />
                                        <span class="description"><?php _e( 'This will be the main venues archive page, where all venues can be displayed' , $this->plugin_name ); ?></span>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>
                                        <input class="button button-primary" type="submit" value="Save All Changes" /><span class="spinner" style="float:none;position:relative;"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            */ ?>

            <div class="metabox-holder" id="behaviour-design">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "Design Options" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><span><?php _e( "Disable plugin styles" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input type="checkbox" style="margin-top:6px" id="<?php echo $this->plugin_name; ?>-disable-styles" name="<?php echo $this->plugin_name; ?>[config_disable_styles]" <?php echo ($config_disable_styles == 1 ? " checked " : ""); ?> value="1" /><br /><br />
                                        <span class="description"><?php _e('Check this box if you\'d prefer to manage the plugin colours through your theme CSS', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr ukds-state="1" ukds-toggle-checked="#<?php echo $this->plugin_name; ?>-disable-styles">
                                    <th><span><?php _e( "Design Layout" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-style_layout_fluid" class="<?php echo $this->plugin_name; ?>-layout-picker" name="<?php echo $this->plugin_name; ?>[style_layout]" type="radio" value="fluid" <?php echo  ($style_layout == "fluid" ? " checked " : ""); ?> /><label class="layout-option layout-option-fluid" for="<?php echo $this->plugin_name; ?>-style_layout_fluid">&nbsp;</label>
                                        <input id="<?php echo $this->plugin_name; ?>-style_layout_boxed" class="<?php echo $this->plugin_name; ?>-layout-picker" name="<?php echo $this->plugin_name; ?>[style_layout]" type="radio" value="boxed" <?php echo  ($style_layout == "boxed" ? " checked " : ""); ?> /><label class="layout-option layout-option-boxed" for="<?php echo $this->plugin_name; ?>-style_layout_boxed">&nbsp;</label>
                                        <br />
                                        <br />
                                        <div ukds-toggle-checked="[name='ltd-tickets[style_layout]']" ukds-toggle-val="boxed">
                                            <p><?php _e("Maximum Width", $this->plugin_name); ?></p>
                                            <input id="<?php echo $this->plugin_name; ?>-style_layout_max_width" class="regular-text" name="<?php echo $this->plugin_name; ?>[style_max_width]" value="<?php echo esc_attr($style_max_width); ?>" />
                                            <br />
                                        </div>
                                        <span class="description"><?php _e('Select whether you would like the plugin layout to fill the full width of your theme or use it\'s own grid.', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr ukds-state="1" ukds-toggle-checked="#<?php echo $this->plugin_name; ?>-disable-styles">
                                    <th><span><?php _e("Primary Colour", $this->plugin_name); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-style_primary_colour" class="<?php echo $this->plugin_name; ?>-color-picker" name="<?php echo $this->plugin_name; ?>[style_primary_colour]" type="text" value="<?php echo esc_attr($style_primary_colour); ?>"  /><br />
                                        <span class="description"><?php _e('Select a primary colour - this should be your main brand colour.', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr ukds-state="1" ukds-toggle-checked="#<?php echo $this->plugin_name; ?>-disable-styles">
                                    <th><span><?php _e("Secondary Colour", $this->plugin_name); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-style_secondary_colour" class="<?php echo $this->plugin_name; ?>-color-picker" name="<?php echo $this->plugin_name; ?>[style_secondary_colour]" type="text" value="<?php echo esc_attr($style_secondary_colour); ?>" /><br />
                                        <span class="description"><?php _e('Select a secondary colour - this can be an accent colour from your brand or website.', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr ukds-state="1" ukds-toggle-checked="#<?php echo $this->plugin_name; ?>-disable-styles">
                                    <th><span><?php _e("Primary Button", $this->plugin_name); ?></span></th>
                                    <td>
                                         <div style="float:left;" class="button-style">
                                             <p><?php _e("Button Background", $this->plugin_name); ?></p>
                                             <input id="<?php echo $this->plugin_name; ?>-primary_button_background" class="<?php echo $this->plugin_name; ?>-color-picker" name="<?php echo $this->plugin_name; ?>[style_primary_button_background]" type="text" value="<?php echo esc_attr($style_primary_button_background); ?>" /><br />
                                         </div>
                                        <div style="float:left;margin-left:50px" class="button-style">
                                            <p><?php _e("Button Text", $this->plugin_name); ?></p>
                                            <input id="<?php echo $this->plugin_name; ?>-style_primary_button_text_colour" class="<?php echo $this->plugin_name; ?>-color-picker" name="<?php echo $this->plugin_name; ?>[style_primary_button_text_colour]" type="text" value="<?php echo esc_attr($style_primary_button_text_colour); ?>" /><br />
                                        </div>
                                        <div style="clear:both;"></div>
                                        <p><?php _e("CSS Class", $this->plugin_name); ?></p>
                                        <input id="<?php echo $this->plugin_name; ?>-style_primary_button_css_class" class="regular-text" name="<?php echo $this->plugin_name; ?>[style_primary_button_css_class]" placeholder="eg: btn btn-primary" value="<?php echo esc_attr($style_primary_button_css_class); ?>" /><br />
                                        <span class="description"><?php _e('If you already have primary buttons on your website you can add their CSS classes here and inherit their design.  Adding CSS classes will overwrite Background and Text colours set above.', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr ukds-state="1" ukds-toggle-checked="#<?php echo $this->plugin_name; ?>-disable-styles">
                                    <th><span><?php _e("Secondary Button", $this->plugin_name); ?></span></th>
                                    <td>
                                        <div style="float:left;" class="button-style">
                                            <p><?php _e("Button Background", $this->plugin_name); ?></p>
                                            <input id="<?php echo $this->plugin_name; ?>-style_secondary_button_background" class="<?php echo $this->plugin_name; ?>-color-picker" name="<?php echo $this->plugin_name; ?>[style_secondary_button_background]" type="text" value="<?php echo esc_attr($style_secondary_button_background); ?>" /><br />
                                        </div>
                                        <div style="float:left;margin-left:50px" class="button-style">
                                            <p><?php _e("Button Text", $this->plugin_name); ?></p>
                                            <input id="<?php echo $this->plugin_name; ?>-style_secondary_button_text_colour" class="<?php echo $this->plugin_name; ?>-color-picker" name="<?php echo $this->plugin_name; ?>[style_secondary_button_text_colour]" type="text" value="<?php echo esc_attr($style_secondary_button_text_colour); ?>" /><br />
                                        </div>
                                        <div style="clear:both;"></div>
                                        <p><?php _e("CSS Class", $this->plugin_name); ?></p>
                                        <input id="<?php echo $this->plugin_name; ?>-style_secondary_button_css_class" class="regular-text" name="<?php echo $this->plugin_name; ?>[style_secondary_button_css_class]" placeholder="eg: btn btn-secondary" value="<?php echo esc_attr($style_secondary_button_css_class); ?>" /><br />
                                        <span class="description"><?php _e('If you already have secondary buttons on your website you can add their CSS classes here and inherit their design.  Adding CSS classes will overwrite Background and Text colours set above.', $this->plugin_name); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span><?php _e( "Custom CSS" ); ?></span></th>
                                    <td>
                                        <textarea id="<?php echo $this->plugin_name; ?>-style_custom_css" name="<?php echo $this->plugin_name; ?>[style_custom_css]" type="text" value="" placeholder="Paste custom CSS here..." class="regular-text" style="width:100%" rows="10" ><?php echo esc_attr($style_custom_css); ?></textarea><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input class="button button-primary" type="submit" value="Save All Changes" /><span class="spinner" style="float:none;position:relative;"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <div id="import" class="ukdstab">
            <h2><?php _e("Option 1", $this->plugin_name); ?></h2>
            <div class="metabox-holder">
                <div class="postbox" id="import-all">
                    <h3 class="hndle"><?php _e("Import All", $this->plugin_name); ?></h3>
                    <div class="inside">
                        <p><?php _e("This is the simplest option. Clicking '<strong>Import All</strong>' will import all <strong>Products</strong>, <strong>Venues</strong> and <strong>Product Categories</strong> from London Theatre Direct.", $this->plugin_name); ?></p>
                        <ol>
                            <li><p><?php _e("<strong>Products</strong> are imported into the '<strong>Products</strong>' post type, depicted in the left-hand navigation with a tickets icon.", $this->plugin_name); ?></p></li>
                            <li><p><?php _e("<strong>Product Categories</strong> are created as a taxonomy, linked to <strong>Products</strong>.", $this->plugin_name); ?></p></li>
                            <li><p><?php _e("<strong>Venues</strong> are imported into the '<strong>Venues</strong>' post type, depicted in the left-hand navigation, above the tickets icon.", $this->plugin_name); ?></p></li>
                        </ol>
                        <p><br /><?php _e("<strong>Please Note:</strong> running a full import using 'Import All' can take a few minutes, so please be patient.", $this->plugin_name); ?></p>
                        <p class="submit">
                            <input name="ImportAll" type="submit" class="button button-primary" value="Import All" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                        </p>
                    </div>
                </div>
            </div>
            <br />
            <div class="hr-or"><span><?php _e("Or", $this->plugin_name); ?></span></div>
            <br />
            <h2><?php _e("Option 2", $this->plugin_name); ?></h2>
            <div class="metabox-holder">
                <div class="postbox" id="import-categories">
                    <h3 class="hndle"><?php _e('Import Categories', $this->plugin_name); ?></h3>
                    <div class="inside">
                        <p><?php _e("This option will import <strong>only</strong> Product Categories - it's not much use on it's own, but if you plan on using London Theatre Direct's <strong>Product Categories</strong>, you must do import these before importing <strong>Products</strong>.", $this->plugin_name); ?></p>
                        <p class="submit">
                            <input name="ImportCategories" type="submit" class="button button-primary" value="Import Categories" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="metabox-holder">
                <div class="postbox" id="import-products">
                    <h3 class="hndle"><?php _e('Import Products', $this->plugin_name); ?></h3>
                    <div class="inside">
                        <p><?php _e("Using this setting you can choose to either '<strong>Import All Products</strong>' from London Theatre Direct, or by clicking '<strong>Fetch Products</strong>' you can selectively choose which products you'd like to import", $this->plugin_name); ?>.</p>
                        <div class="inline-notice notice-info">
                            <div ukds-ui="imported" ukds-type="products"><span>0</span> <?php _e('Products Already Imported', $this->plugin_name); ?><span class="spinner is-active" style="float:none;position:relative;top:-3px"></span></div>
                        </div>
                        
                        <p class="submit" data-ui="HideOnFetchProducts">
                            <input name="FetchProducts" type="submit" class="button button-secondary" value="Fetch Products" />
                            <input name="ImportProducts" type="submit" class="button button-primary" value="Import All Products" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                        </p>
                        <div id="import-products-container" class="import-container" data-ui="FetchProducts">
                            <div id="import-products-header" class="import-table-header">
                                <h3><?php _e("Product List", $this->plugin_name); ?></h3>
                                <p><?php _e("Select the Products you'd like to import then click '<strong>Import Selected Products</strong>', or click '<strong>Import All Products</strong>'.", $this->plugin_name); ?></p>
                            </div>
                            <div id="import-products-body" class="import-table-body">
                            </div>
                            <input name="ImportSelectedProducts" type="submit" class="button button-primary" value="Import Selected Products" />
                            <input name="ImportProducts" type="submit" class="button button-primary" value="Import All Products" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="metabox-holder">
                <div class="postbox" id="import-venues">
                    <h3 class="hndle"><?php _e('Import Venues', $this->plugin_name); ?></h3>
                    <div class="inside">
                        <p><?php _e("Using this setting you can choose to either '<strong>Import All Venues</strong>' from London Theatre Direct, or by clicking '<strong>Fetch Venues</strong>' you can selectively choose which products you'd like to import.", $this->plugin_name); ?></p>
                        <div class="inline-notice notice-info">
                            <div ukds-ui="imported" ukds-type="venues"><span>0</span> <?php _e('Venues Already Imported', $this->plugin_name); ?><span class="spinner is-active" style="float:none;position:relative;top:-3px"></span></div>
                        </div>
                        <p class="submit" data-ui="HideOnFetchVenues">
                            <input name="FetchVenues" type="submit" class="button button-secondary" value="Fetch Venues" />
                            <input name="ImportVenues" type="submit" class="button button-primary" value="Import All Venues" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                        </p>
                        <div id="import-venues-container" class="import-container" data-ui="FetchVenues">
                            <div id="import-venues-header" class="import-table-header">
                                <h3><?php _e("Venues List", $this->plugin_name); ?></h3>
                                <p><?php _e("Select the Venues you'd like to import then click '<strong>Import Selected Venues</strong>', or click '<strong>Import All Venues</strong>'.", $this->plugin_name); ?></p>
                            </div>
                            <div id="import-venues-body" class="import-table-body">
                            </div>
                            <input name="ImportSelectedVenues" type="submit" class="button button-primary" value="Import Selected Venues" />
                            <input name="ImportVenues" type="submit" class="button button-primary" value="Import All Venues" /><span class="spinner" style="float:none;position:relative;top:-3px"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div id="advanced" class="ukdstab">
            <div class="inline-notice notice-error">
                <h3><?php _e('Warning', $this->plugin_name); ?></h3>
                <p><?php _e("These settings control the post types and templates that the plugin will use.  They can be helpful for when retrofitting the plugin into a website that already sells tickets from multiple sources. <br ><strong>Only change these settings if you are familiar with Post Type and Taxonomies, and are confident writing your own Wordpress templates.</strong>", $this->plugin_name); ?></p>
                <br /><input class="button button-secondary" type="submit" id="RestoreDefaults" name="RestoreDefaults" value="Restore Defaults" />
            </div>


            <div class="metabox-holder" id="template-files">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "Post Types + Taxonomies" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <p><?php _e( "If you would like to use your own Post Types and Taxonomies you can change them here.  <br /><strong>Warning:</strong> Changing post types and taxonomies will also break the plugin's links to the template files below." ); ?></p>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><span><?php _e( "Product Post Type" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <?php  ?>
                                        <input id="<?php echo $this->plugin_name; ?>-config_product_post_type" name="<?php echo $this->plugin_name; ?>[config_product_post_type]" type="text" value="<?php echo esc_attr($config_product_post_type); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span><?php _e( "Venue Post Type" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-config_venue_post_type" name="<?php echo $this->plugin_name; ?>[config_venue_post_type]" type="text" value="<?php echo esc_attr($config_venue_post_type); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span><?php _e( "Product Categories Taxonomy" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-config_product_category_taxonomy" name="<?php echo $this->plugin_name; ?>[config_product_category_taxonomy]" type="text" value="<?php echo esc_attr($config_product_category_taxonomy); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input class="button button-primary" type="submit" value="Save All Changes" /><span class="spinner" style="float:none;position:relative;"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="metabox-holder" id="template-files">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( "Plugin Template Files" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <p><?php _e( "If you would prefer to create your own template files in your theme, enter the file names below. The plugin will look for template files in the root of your theme, if your template files aren't in your theme's root folder make sure you add the directory path, <br /><em>eg: templates/single-products.php.</em>" ); ?></p>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th><span><?php _e( "Product Template" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-template_product_template" name="<?php echo $this->plugin_name; ?>[template_product_template]" type="text" value="<?php echo esc_attr($template_product_template); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span><?php _e( "Product Archive Template" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-template_product_archive" name="<?php echo $this->plugin_name; ?>[template_product_archive]" type="text" value="<?php echo esc_attr($template_product_archive); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span><?php _e( "Venue Page Template" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-template_venue_template" name="<?php echo $this->plugin_name; ?>[template_venue_template]" type="text" value="<?php echo esc_attr($template_venue_template); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span><?php _e( "Venue Archive Template" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-template_venue_archive" name="<?php echo $this->plugin_name; ?>[template_venue_archive]" type="text" value="<?php echo esc_attr($template_venue_archive); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><span><?php _e( "Product Category Page Template" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-template_category_template" name="<?php echo $this->plugin_name; ?>[template_category_template]" type="text" value="<?php echo esc_attr($template_category_template); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr style="display:none">
                                    <th><span><?php _e( "Booking Page Template" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-template_booking_template" name="<?php echo $this->plugin_name; ?>[template_booking_template]" type="text" value="<?php echo esc_attr($template_booking_template); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr style="display:none">
                                    <th><span><?php _e( "Basket Page Template" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-template_basket_template" name="<?php echo $this->plugin_name; ?>[template_basket_template]" type="text" value="<?php echo esc_attr($template_basket_template); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr style="display:none">
                                    <th><span><?php _e( "Checkout Page Template" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-template_checkout_template" name="<?php echo $this->plugin_name; ?>[template_checkout_template]" type="text" value="<?php echo esc_attr($template_checkout_template); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr style="display:none">
                                    <th><span><?php _e( "Confirmation Page Template" , $this->plugin_name ); ?></span></th>
                                    <td>
                                        <input id="<?php echo $this->plugin_name; ?>-template_confirmation_template" name="<?php echo $this->plugin_name; ?>[template_confirmation_template]" type="text" value="<?php echo esc_attr($template_confirmation_template); ?>" placeholder="" ukds-disabled class="regular-text"><br>
                                        <span class="description"><?php _e( '' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input class="button button-primary" type="submit" value="Save All Changes" /><span class="spinner" style="float:none;position:relative;"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php require_once( 'ltd-tickets-debug-part.php' ); ?>

</div>

