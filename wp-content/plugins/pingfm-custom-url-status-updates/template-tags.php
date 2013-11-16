<?php

/**
 * Gets the latest ping and returns it straight away without any further
 * processing or manipulation. The format will be exactly the same as what
 * the WordPress get_posts() function returns.
 */
function wp_pingfm_latest() {
    global $pcusu_controller;
    $pings = $pcusu_controller->get_pings('numberposts=1');

    if (!empty($pings)) {
        return $pings[0];
    }

    return false;
}

/**
 * Shows an unordered list of the most recent status updates. This template tag
 * is provided for people who are using non-widgetized templates.
 *
 * @param limit     the number of updates to show
 * @param prefix    some text to display before every ping
 * @param permalink whether or not to link to archived statuses
 */
function wp_pingfm_status($limit = 10, $prefix = '', $permalink = true) {
    global $pcusu_controller;
    echo $pcusu_controller->get_widget_content($limit, $prefix, $permalink);
}

?>
