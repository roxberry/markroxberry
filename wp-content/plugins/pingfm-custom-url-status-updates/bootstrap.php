<?php

/*
Plugin Name: Ping.fm
Plugin URI: http://mattjacob.com/pingfm-wordpress-plugin
Description: Integrates with Ping.fm's Custom URL feature to collect pings and post them to your blog. By default, status updates are posted to a sidebar widget and blog/micro-blog entries become full-blown WordPress posts. Many other options are configurable at the <a href="plugins.php?page=wp-pingfm-settings">settings page</a>.
Version: 2.0.1
Author: Matt Jacob
Author URI: http://mattjacob.com
License: GPL2

Copyright 2010  Matt Jacob  (email : matt@mattjacob.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// The prefix is an acronym stemming from the original name of this plugin
define('PCUSU_PLUGIN_FILE', __FILE__);
define('PCUSU_PLUGIN_DIR', dirname(__FILE__) . '/');
define('PCUSU_HTTP_PATH', WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__)));

// Let's make sure we're running a compatible version of PHP before we go any further
if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
    require_once(PCUSU_PLUGIN_DIR . 'classes/PingFmCustomUrlUtils.php');
    require_once(PCUSU_PLUGIN_DIR . 'classes/PingFmCustomUrlController.php');
    require_once(PCUSU_PLUGIN_DIR . 'template-tags.php');

    $pcusu_controller = new PingFmCustomUrlController();
}
else {
    register_activation_hook(__FILE__, 'pcusu_thwart_decepticon_php');
}

/**
 * Displays an error message if the user's PHP version isn't up to spec.
 */
function pcusu_thwart_decepticon_php() {
    $message = '<strong>Oh noes!</strong><br /><br />'
             . 'The Ping.fm Custom URL plugin requires PHP 5.2.0 or greater, '
             . 'but the installed version is ' . PHP_VERSION . '. You should '
             . 'upgrade your PHP installation (or ask your hosting company '
             . 'to do it for you).'
             ;
    deactivate_plugins(__FILE__);
    wp_die($message);
}

?>
