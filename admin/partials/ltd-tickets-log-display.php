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
    <h2><?php _e( 'London Theatre Direct' ); ?></h2>

    <form method="post" name="ltd_settings" action="options.php">
        <input type="hidden" name="admin_page" value="log" />

        <?php
        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);
        $options = get_option($this->plugin_name);
        ?>

        <h2>
            <?php _e( 'Plugin Log', $this->plugin_name ); ?>
        </h2>



        <div id="logging" class="ukdstab active">
            <div class="metabox-holder">
                <div class="postbox" id="partner-type">
                    <h3 class="hndle"><?php _e( "Log" , $this->plugin_name ); ?></h3>
                    <div class="inside">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Timestamp</th>
                                    <th>Type</th>
                                    <th>Message</th>
                                    <th>URL</th>
                                    <th>Stack</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                    $log = $this->GetLog();
                                    $i = 0;
                                    foreach($log as $entry) {
                                ?>

                                <tr>
                                    <td>
                                        <?php echo $i+1; ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html($entry->timestamp); ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html($entry->type); ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html($entry->message); ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html($entry->url); ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html($entry->stack); ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html($entry->ip); ?>
                                    </td>

                                </tr>

                                <?php
                                        $i++;
                                    }

                                    if ($i == 0) {
                                ?>
                                <tr>
                                    <td colspan="7">
                                        The log is empty.
                                    </td>
                                </tr>

                                <?php } ?>

                            </tbody>
                        </table>
                        <br />
                        <input type="submit" name="ClearLog" id="ClearLog" value="Clear Log" class="button button-primary" />
                    </div>
                </div>
            </div>
        </div>

    </form>

    <?php require_once( 'ltd-tickets-debug-part.php' ); ?>

</div>