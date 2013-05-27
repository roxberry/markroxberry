<?php

/**
 * Robust upgrade functionality that can handle stacked database upgrades as
 * well as any kind of supporting upgrades needed to maintain plugin
 * compatibility. The class is aware of and will accurately handle upgrades
 * spanning multiple revisions.
 */
class PingFmCustomUrlUpdater implements PingFmCustomUrlConstants {
    /**
     * Checks to see if any upgrades are available between the installed plugin
     * version and the latest upgrader method found in this class (using
     * introspection). Note: not every release requires a database upgrade, so
     * it's very likely that this method will return false.
     */
    public function check_need_upgrade() {
        $options = PingFmCustomUrlOptions::factory();

        // A special case for upgrades prior to 2.0
        if (false !== get_option('pingfm_db_version')) {
            $options = $this->_upgrade_options_2_0_0($options);
        }

        $options->need_upgrade = false;
        $options->upgrade_methods = array();

        foreach (get_class_methods($this) as $m) {
            $needle = '_upgrade_release';

            // Only examine methods that match our explicit pattern
            if (false !== strpos($m, $needle)) {
                // Extract a version number from the upgrader method
                $version = str_replace('_', '.', substr($m, (strlen($needle) + 1)));

                if (version_compare($options->plugin_version, $version, '<')) {
                    $options->upgrade_methods[] = $m;
                }
            }
        }

        if (empty($options->upgrade_methods)) {
            $options->plugin_version = self::PLUGIN_VERSION;
        }
        else {
            // Sort the upgrades by version number so that they happen in the
            // correct order (in case of DB operations, etc.)
            sort($options->upgrade_methods);
            $options->need_upgrade = true;
        }

        $options->save();
    }

    /**
     * Runs through the needed upgrades and calls each method. Keeps going
     * until all upgrades are complete or an error is encountered. Also bumps
     * the recorded version number up as upgrades are performed successfully.
     *
     * @return latest version number if successful, otherwise false
     */
    public function do_upgrade($options) {
        if ($options->need_upgrade && !empty($options->upgrade_methods)) {
            foreach ($options->upgrade_methods as $u) {
                $options = $this->$u($options);

                if ($options->upgrade_error) {
                    break;
                }
            }
        }

        $options->need_upgrade = false;

        return $options;
    }

    /**
     * Special case method to get plugin up to version 2.0.0. Goes through all
     * stored plugin options and tries to convert them to the new object used
     * in 2.0.0 and above. Also discards some options that are no longer needed.
     */
    private function _upgrade_options_2_0_0($options) {
        foreach (array_keys(get_object_vars($options)) as $k) {
            $old_key = 'pingfm_' . $k;
            $options->$k = PingFmCustomUrlUtils::better_get_option($old_key);

            delete_option($old_key);
        }

        if ($options->title_structure) {
            $options->title_structure = preg_replace('/%([a-z]+)%/', '{$1}', $options->title_structure);
        }

        $options->plugin_version = '1.3.3';

        delete_option('pingfm_db_version');
        delete_option('pingfm_force_posts');
        delete_option('pingfm_generate_token');

        return $options;
    }

    /**
     * Performs a database upgrade to move existing plugin installations to
     * version 2.0.0 by converting records in our custom table to records in
     * the WordPress posts table using custom post types. Various checks are in
     * place to ensure the data is converted in its entirety.
     *
     * @return version number if successful, otherwise false
     */
    private function _upgrade_release_2_0_0($options) {
        global $wpdb;

        $table = $wpdb->prefix . self::DB_TABLE_SUFFIX;
        $sql = "SELECT `id`, `status` AS `post_content`, `timestamp` AS `post_date` FROM `$table`";
        $res = $wpdb->get_results($sql);

        $old_num_rows = $wpdb->num_rows;

        foreach ($res as $r) {
            $ping = array(
                'method'  => 'status',
                'date'    => $r->post_date,
                'message' => $r->post_content,
                'title'   => '',
                'media'   => '',
            );

            $post_id = PingFmCustomUrlUtils::insert_ping($ping);

            if ($post_id) {
                update_post_meta($post_id, 'previous_id', $r->id);
            }
        }

        $sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE `post_type` = 'pingfm'";
        $new_num_rows = $wpdb->get_var($sql);

        if ($old_num_rows == $new_num_rows) {
            $wpdb->query("DROP TABLE $table");
            $options->plugin_version = '2.0.0';
        }
        else {
            $options->upgrade_error = true;
        }

        return $options;
    }
}

?>
