<?php
/**
 * Background Update Process.
 *
 * Controls the plugin synchronisation functions. Running large updates
 * this was stops the plugin stalling on cron jobs.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */

class LTD_Tickets_Background_Process_Update extends WP_Background_Process {

    use LTD_Tickets_Logging;
	/**
     * @var string
     */
	protected $action = 'ltd_background_process_update';

	/**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
	protected function task( $item ) {
        $LTD_Tickets_Data_Sync = new LTD_Tickets_Data_Sync( LTD_PLUGIN_NAME, LTD_PLUGIN_VERSION );



        switch( $item[0] ) {
            case "import_all" :
                $LTD_Tickets_Data_Sync->import_all();
                break;

            case "import_categories" :
                $LTD_Tickets_Data_Sync->import_categories();
                break;

            case "import_venues" :
                $LTD_Tickets_Data_Sync->import_venues();
                break;

            case "import_products" :
                $LTD_Tickets_Data_Sync->import_products();
                break;

            case "import_selected_venues" :
                $LTD_Tickets_Data_Sync->import_venues($item[1]);
                break;

            case "import_selected_products" :
                $LTD_Tickets_Data_Sync->import_products($item[1]);
                break;

            case "sync_all" :
                $LTD_Tickets_Data_Sync->sync_all();
                break;

            case "sync_categories" :
                $LTD_Tickets_Data_Sync->sync_categories();
                break;

            case "sync_venues" :
                $LTD_Tickets_Data_Sync->sync_venues();
                break;

            case "sync_products" :
                $LTD_Tickets_Data_Sync->sync_products();
                break;

        }

		return false;
	}

	/**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

}