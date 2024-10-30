<?php
/**
 * Admin Notice for imported and synced data.
 *
 * Handles the admin notice indicating imported and update posts and taxonomies.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class LTD_Tickets_Import_Notice {


	public function __construct( $imports, $type, $plugin_name) {


        if ($type == "background") {
            switch($imports) {
                case "import_all" :
                    $message = __("The 'Import All' function can take some time, so will run as a background process to prevent server timeouts. Products, Venues and Product Categories will be asynchronously imported over the next few minutes.", $plugin_name);
                    break;
                case "import_products" :
                    $message = __("The 'Import All Products' function can take some time, so will run as a background process to prevent server timeouts. Products will be asynchronously imported over the next few minutes.", $plugin_name);
                    break;
                case "import_venues" :
                    $message = __("The 'Import All Venues' function can take some time, so will run as a background process to prevent server timeouts. Venues will be asynchronously imported over the next few minutes.", $plugin_name);
                    break;
                case "import_selected_products" :
                    $message = __("The 'Import Selected Products' function can take some time, so will run as a background process to prevent server timeouts. The products you selected will be asynchronously imported over the next few minutes.", $plugin_name);
                    break;
                case "import_selected_venues" :
                    $message = __("The 'Import Selected Venues' function can take some time, so will run as a background process to prevent server timeouts. The venues you selected will be asynchronously imported over the next few minutes.", $plugin_name);
                    break;
                case "sync_all" :
                    $message = __("The 'Update All' function can take some time, so will run as a background process to prevent server timeouts. Product, Venues and Product Categories will be asynchronously updated over the next few minutes.", $plugin_name);
                    break;
                case "sync_products" :
                    $message = __("The 'Update All Products' function can take some time, so will run as a background process to prevent server timeouts. Products will be asynchronously updated over the next few minutes.", $plugin_name);
                    break;
                case "sync_venues" :
                    $message = __("The 'Update All Venues' function can take some time, so will run as a background process to prevent server timeouts. Venues will be asynchronously updated over the next few minutes.", $plugin_name);
                    break;
            }
        } else {

            $note = ($type == "import" ? __("imported", $plugin_name) : __("updated", $plugin_name) );
            $possible_issue = false;
            $message = __("You have successfully ", $plugin_name);
            $message .= $note . ":";
            if (isset($imports['categories'])) {
                $message.= "<br />";
                $message.= $imports['categories'] . " " . __("Categories", $plugin_name);
                if ($imports['categories'] == 0) $possible_issue = true;
            }
            if (isset($imports['products'])) {
                $message.= "<br />";
                $message.= $imports['products'] . " " . __("Products", $plugin_name);
                if ($imports['products'] == 0) $possible_issue = true;
            }

            if (isset($imports['venues'])) {
                $message.= "<br />";
                $message.= $imports['venues'] . " " . __("Venues", $plugin_name);
                if ($imports['venues'] == 0) $possible_issue = true;
            }

            if ($possible_issue) {
                $message.= "<br /><br />";
                $message.= __("Not imported what you expected? Check the <a href='/wp-admin/admin.php?page=$plugin_name-log'>Log</a> for import errors.");

            }

        }


        add_settings_error(
            'import_notice',
            esc_attr( 'settings_updated' ),
            $message,
            'updated'
        );


    }

}