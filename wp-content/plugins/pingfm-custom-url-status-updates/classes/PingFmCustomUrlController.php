<?php

/**
 * Drives the Ping.fm Custom URL plugin. Handles all routing of various plugin
 * activities both inside and outside of the WordPress architecture.
 * Responsible for hooking into actions, generating settings pages, building
 * RSS feeds and widget content, and more.
 */
class PingFmCustomUrlController implements PingFmCustomUrlConstants {
    /**
     * Constructor
     */
    public function __construct() {
        register_activation_hook(PCUSU_PLUGIN_FILE, array($this, 'activate_plugin'));
        add_action('plugins_loaded', array($this, 'register_action_hooks'));
    }

    /**
     * ------------------------------------------------------------------------
     * CALLBACKS
     * ------------------------------------------------------------------------
     */

    /**
     * Registers all of our methods with the WordPress action/filter API. This
     * ensures that everything gets executed at the appropriate time/place
     * within the WordPress architecture.
     */
    public function register_action_hooks() {
        $options = PingFmCustomUrlOptions::factory();

        // Add some stuff to the admin area
        add_action('admin_menu', array($this, 'insert_admin_menu_link'));
        add_action('admin_init', array($this, 'initialize_admin'));

        if ($options->need_upgrade) {
            if (!isset($_GET['page']) || ('wp-pingfm-settings' != $_GET['page'])) {
                require_once(PCUSU_PLUGIN_DIR . 'classes/PingFmCustomUrlHtml.php');
                add_action('admin_notices', array('PingFmCustomUrlHtml', 'print_upgrade_notice'));
            }
        }
        else if (!$options->upgrade_error) {
            // Add the widget and associated controls for it
            $this->initialize_widget();

            // Handle all the query string and redirection stuff
            add_action('init', array($this, 'setup_query_vars_rewrite_rules'));
            add_action('init', array($this, 'register_custom_post_type'));
            add_action('parse_query', array($this, 'do_page_redirect'));
        }
    }

    /**
     * Does some basic housekeeping and is run when the plugin is first
     * installed and also when it's re-activated.
     */
    public function activate_plugin() {
        require_once(PCUSU_PLUGIN_DIR . 'classes/PingFmCustomUrlUpdater.php');

        $updater = new PingFmCustomUrlUpdater();
        $updater->check_need_upgrade();

        $this->register_custom_post_type();

        add_filter('rewrite_rules_array', array($this, 'insert_rewrite_rules'));
        flush_rewrite_rules();
    }

    /**
     * Sets up the widget in the system. Also inserts some custom CSS into the
     * <head> of pages if the user specified some styles on the settings page.
     */
    public function initialize_widget() {
        $id   = 'wp-pingfm';
        $name = 'Ping.fm';
        $args = array(
            'classname'   => 'wp-pingfm-widget',
            'description' => 'Your most recent Ping.fm status updates'
        );

        wp_register_sidebar_widget($id, $name, array($this, 'register_widget'), $args);
        wp_register_widget_control($id, $name, array($this, 'register_widget_control'));

        if (is_active_widget(array($this, 'register_widget'))) {
            if (!empty(PingFmCustomUrlOptions::factory()->user_css)) {
                add_action('wp_head', array($this, 'insert_widget_css'));
            }
        }
    }

    /**
     * Creates the widget and causes it to appear as a draggable box in the
     * admin interface.
     *
     * @param args the bits surrounding the widget box
     */
    public function register_widget($args) {
        extract($args);

        $options = PingFmCustomUrlOptions::factory();

        echo $before_widget;
        echo $before_title, $options->widget_title, $after_title;

        echo $this->get_widget_content(
            $options->widget_limit,
            $options->widget_prefix,
            $options->widget_links
        );

        echo $after_widget;
    }

    /**
     * Registers some form controls for changing widget settings via the admin
     * interface. The output is included directly inside the main <form>, whose
     * action POSTs back to itself.
     */
    public function register_widget_control() {
        require_once(PCUSU_PLUGIN_DIR . 'classes/PingFmCustomUrlHtml.php');

        $options = PingFmCustomUrlOptions::factory();

        if (!empty($_POST)) {
            if (isset($_POST['pingfm_widget_limit'])) {
                $options->widget_limit = absint($_POST['pingfm_widget_limit']);
            }

            if (isset($_POST['pingfm_widget_prefix'])) {
                $options->widget_prefix = sanitize_text_field($_POST['pingfm_widget_prefix']);
            }

            if (isset($_POST['pingfm_widget_title'])) {
                $options->widget_title = sanitize_text_field($_POST['pingfm_widget_title']);
            }

            $options->widget_links = isset($_POST['pingfm_widget_links']);
            $options->save();
        }

        PingFmCustomUrlHtml::print_widget_settings();
    }

    /**
     * Sets up a couple things that are needed by the settings page.
     */
    public function initialize_admin() {
        // Registers field that WordPress should look for via the settings API
        register_setting('pingfm_plugin_options', self::OPTION_NAME, array($this, 'validate_options_page'));
    }

    /**
     * Validates options set on the settings page and also serves as a kind of
     * controller for admin actions related to this plugin. Handles non-standard
     * submit actions such as performing an upgrade and generating a new token.
     * Can be extended to do other things in the future as needed.
     */
    public function validate_options_page($arg) {
        if (is_object($arg)) {
            return $arg;
        }

        $type = 'updated';
        $options = PingFmCustomUrlOptions::factory();

        /**
         * 1 = Generate New Key
         * 2 = Let The Conversion Begin!
         * 3 = Report This Little Bugger
         */
        switch ($arg['button_action']) {
            case 1:
                $options->token = PingFmCustomUrlUtils::generate_unique_token();
                $message = self::MSG_TOKEN_SAVED;

                break;
            case 2:
                require_once(PCUSU_PLUGIN_DIR . 'classes/PingFmCustomUrlUpdater.php');

                ignore_user_abort(true);
                set_time_limit(0);

                $updater = new PingFmCustomUrlUpdater();
                $options = $updater->do_upgrade($options);

                if ($options->upgrade_error) {
                    $message = self::MSG_UPGRADE_ERROR;
                    $type = 'error';
                }
                else {
                    $message = self::MSG_UPGRADE_SUCCESS;
                }

                break;
            case 3:
                $to = 'matt@mattjacob.com';
                $subject = 'Ping.fm Custom URL Plugin Error Report';
                $body = $this->_build_error_report($options);

                wp_mail($to, $subject, $body);
                $message = self::MSG_REPORT_SENT;

                break;
            default:
                $options->default_author   = $arg['default_author'];
                $options->default_category = $arg['default_category'];
                $options->default_status   = $arg['default_status'];
                $options->default_tags     = sanitize_text_field($arg['default_tags']);
                $options->inline_images    = isset($arg['inline_images']);
                $options->title_structure  = sanitize_text_field($arg['title_structure']);
                $options->user_css         = strip_tags(stripslashes(trim($arg['user_css'])));

                $message = self::MSG_SETTINGS_SAVED;
        }

        $code = 'pingfm_' . $type;
        add_settings_error('pingfm_options', $code, $message, $type);

        return $options;
    }

    /**
     * Adds a 'settings' link to the navigation panel in the administrative
     * area. The link will show up under the 'plugins' heading.
     */
    public function insert_admin_menu_link() {
        require_once(PCUSU_PLUGIN_DIR . 'classes/PingFmCustomUrlHtml.php');

        // Add the link, and then add the admin CSS for the settings page
        $plugin_page = add_submenu_page(
            'plugins.php',
            'Ping.fm Settings',
            'Ping.fm Settings',
            'manage_options',
            'wp-pingfm-settings',
            array('PingFmCustomUrlHtml', 'print_main_settings')
        );

        add_action("admin_head-$plugin_page", array('PingFmCustomUrlHtml', 'print_admin_css'));
        add_contextual_help($plugin_page, PingFmCustomUrlHtml::get_contextual_help());

        // Add a link to the same page from the main plugins listing
        $plugin_action_links = 'plugin_action_links_' . plugin_basename(PCUSU_PLUGIN_FILE);
        add_filter($plugin_action_links, array($this, 'insert_admin_action_link'));
    }

    /**
     * Adds a 'settings' link into the action column of the plugin on the
     * plugins listing page in the administrative area. This helps users find
     * the settings page.
     *
     * @param links a reference to the current array of links
     * @return      an array reference w/new element prepended
     */
    public function insert_admin_action_link($links) {
        // Add a link to this plugin's settings page
        $settings_link = '<a href="plugins.php?page=wp-pingfm-settings">Settings</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    /**
     * Outputs the user-defined widget CSS that gets inserted into every blog
     * page, assuming that the user actually specified some styles.
     */
    public function insert_widget_css() {
        echo "<style type='text/css'>\n", PingFmCustomUrlOptions::factory()->user_css, "</style>\n";
    }

    /**
     * Filters the query vars and the rewrite rules using a pair of simple
     * callback functions.
     */
    public function setup_query_vars_rewrite_rules() {
        add_filter('query_vars',          array($this, 'insert_query_vars'));
        add_filter('rewrite_rules_array', array($this, 'insert_rewrite_rules'));
    }

    /**
     * Registers the custom post type that WordPress 3.0 uses for all kinds of
     * cool stuff. The documentation on custom post types was a little lacking,
     * so this method will probably need to be revisited sometime in the near
     * future once more is known.
     */
    public function register_custom_post_type() {
        $labels = array(
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Ping',
            'edit_item'          => 'Edit Ping',
            'name'               => 'Pings',
            'new_item'           => 'New Ping',
            'not_found'          => 'No pings found',
            'not_found_in_trash' => 'No pings found in Trash',
            'parent_item_colon'  => '',
            'search_items'       => 'Search Pings',
            'singular_name'      => 'Ping',
            'view_item'          => 'View Ping',
        );

        $args = array(
            'capability_type'     => 'post',
            'exclude_from_search' => false,
            'hierarchical'        => false,
            'labels'              => $labels,
            'menu_icon'           => PCUSU_HTTP_PATH . '/images/pingfm-logo-menu.png',
            'menu_position'       => null,
            'publicly_queryable'  => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => self::CUSTOM_POST_TYPE, 'with_front' => false),
            'show_in_nav_menus'   => true,
            'show_ui'             => true,
            'supports'            => array('title', 'editor', 'author', 'thumbnail'),
        );

        register_post_type(self::CUSTOM_POST_TYPE, $args);
    }

    /**
     * Adds some query string variables to the list of allowed query string
     * parameters. The plugin depends on these for proper page routing and
     * operation.
     *
     * @param vars the current query string vars
     * @return     the updated query string vars
     */
    public function insert_query_vars($vars) {
        // PA = ping action: post|status|feed
        // PK = ping key: unique token
        // PI = ping ID: PK from *_pingfm.id table

        array_unshift($vars, 'pa', 'pk', 'pi');
        return $vars;
    }

    /**
     * Creates the mod_rewrite rules to use with the plugin and adds them onto
     * the front of the current set of rules.
     *
     * @param wp_rewrite a reference to an instantiated WP_Rewrite object
     */
    public function insert_rewrite_rules($rules) {
        $new_rules = array(
            'pingfm/post/([a-zA-Z0-9]{32})/?$' => 'index.php?pa=post&pk=$matches[1]',
            'pingfm/status/([0-9]+)/?$'        => 'index.php?pa=status&pi=$matches[1]',
            'pingfm/feed/?$'                   => 'index.php?pa=feed',
        );

        return $new_rules + $rules;
    }

    /**
     * Redirects to proper page based on query string. Redirect is used loosely
     * here, because we're actually just outputting some content (HTML/XML) and
     * then killing script execution.
     *
     * @param wp_query a reference to an instantiated WP_Query object
     */
    public function do_page_redirect($wp_query) {
        if (isset($wp_query->query_vars['pa'])) {
            switch ($wp_query->query_vars['pa']) {
                case 'post':
                    $this->_handle_incoming_ping($wp_query->query_vars['pk']);
                    break;
                case 'status':
                    $this->_display_single_ping($wp_query->query_vars['pi']);
                    break;
                case 'feed':
                    $this->_generate_rss_feed();
                    break;
            }

            exit;
        }
    }

    /**
     * ------------------------------------------------------------------------
     * PUBLIC METHODS
     * ------------------------------------------------------------------------
     */

    /**
     * Builds the core HTML of the widget as a <ul>. Also used for the
     * non-widgetized "template tag" function.
     *
     * @param limit     the number of recent status updates to get
     * @param prefix    something to put before each update
     * @param permalink whether or not to link to archived statuses
     * @return          the HTML for an unordered list
     */
    public function get_widget_content($limit, $prefix, $permalink) {
        $limit = (int) $limit;
        $prefix = trim($prefix);
        $permalink = (bool) $permalink;

        $html = "<ul>\n";
        $pings = $this->get_pings("numberposts=$limit");

        if (!empty($pings)) {
            foreach ($pings as $p) {
                $post_content = ($p->post_content_filtered) ? $p->post_content_filtered : $p->post_content;
                $time_diff = human_time_diff(strtotime($p->post_date_gmt), strtotime(gmdate('Y-m-d H:i:s')));
                $timestamp = sprintf('<span class="updated-time">%s ago</span>', $time_diff);

                $html .= '<li>';
                $html .= ($prefix) ? $prefix . ' ' : '';
                $html .= $post_content . '<br />';
                $html .= ($permalink) ? sprintf('<a href="%s">%s</a>', get_permalink($p->ID), $timestamp) : $timestamp;
                $html .= "</li>\n";
            }
        }
        else {
            $html .= '<li>' . self::ERROR_ZERO_RECORDS . "</li>\n";
        }

        $html .= "</ul>\n";
        return $html;
    }

    /**
     * A wrapper around the get_posts() function that sets up some default
     * arguments that are only applicable to this function. Accepts arguments
     * in the style of other WordPress functions that take a string or an array
     * and parse the options appropriately.
     *
     * @param args options to override posts that are returned
     * @return     posts retrieved by query
     */
    public function get_pings($args = null) {
        $defaults = array(
            'numberposts' => 10,
            'order'       => 'DESC',
            'orderby'     => 'date',
            'post_status' => 'publish',
            'post_type'   => self::CUSTOM_POST_TYPE,
        );

        $r = wp_parse_args($args, $defaults);
        return get_posts($r);
    }

    /**
     * ------------------------------------------------------------------------
     * PRIVATE METHODS
     * ------------------------------------------------------------------------
     */

    /**
     * Pulls together various pieces of information about the blog and the
     * server environment and formats everything in an acceptable way for plain
     * text email.
     *
     * @param options instance of PingFmCustomUrlOptions object
     * @return        formatted error report to use as email message body
     */
    private function _build_error_report($options) {
        global $wpdb;

        $report = '';
        $params = array(
            'name',
            'description',
            'url',
            'wpurl',
            'admin_email',
            'version',
            'language',
            'charset',
        );

        foreach ($params as $p) {
            $report .= sprintf("$p: %s\n", get_bloginfo($p));
        }

        $report .= sprintf("feed_url: %s\n", PingFmCustomUrlUtils::get_feed_url());
        $report .= sprintf("post_url: %s\n", PingFmCustomUrlUtils::get_post_url($options->token));
        $report .= sprintf("php_uname: %s\n", php_uname());
        $report .= sprintf("php_version: %s\n", PHP_VERSION);
        $report .= sprintf("mysql_version: %s\n", $wpdb->db_version());
        $report .= sprintf("plugin_version: %s\n", self::PLUGIN_VERSION);
        $report .= sprintf("\n%s\n", print_r($options, true));

        return $report;
    }

    /**
     * Outputs a permalink archive page to display a single status update.
     *
     * @param id the ID of the ping to show from DB table
     */
    private function _display_single_ping($id) {
        global $wpdb;

        $sql = "SELECT `post_id` FROM `$wpdb->postmeta` WHERE `meta_key` = 'previous_id' AND `meta_value` = %d";
        $sth = $wpdb->prepare($sql, $id);
        $post_id = $wpdb->get_var($sth);

        if (!empty($post_id)) {
            $permalink = get_permalink($post_id);
            header("Location: $permalink", true, 301);
            exit;
        }

        echo '<strong>ERROR:</strong> ' . self::ERROR_PERMALINK_404;
    }

    /**
     * Receives an incoming ping from Ping.fm and inserts it into the DB.
     *
     * @param token the secret authentication token
     */
    private function _handle_incoming_ping($token) {
        if (!empty($_POST)) {
            $options = PingFmCustomUrlOptions::factory();

            if ($token == $options->token) {
                $ping = array(
                    'location'    => '', // Any location updates posted with the message.
                    'media'       => '', // If media is posted, this will contain a URL to the media file.
                    'message'     => '', // The posted message content.
                    'method'      => '', // The method of the message being sent (blog, microblog, status).
                    'raw_message' => '', // If media is posted, this will contain the posted message WITHOUT the hosted media link.
                    'title'       => '', // If method is "blog" then this contain the blog's title.
                    'trigger'     => '', // If you post a message with a custom trigger, it will show here.
                );

                // Field descriptions from:
                // http://groups.google.com/group/pingfm-developers/web/working-with-a-custom-url

                foreach (array_keys($ping) as $k) {
                    if (isset($_POST[$k])) {
                        $ping[$k] = trim($_POST[$k]);
                    }
                }

                PingFmCustomUrlUtils::insert_ping($ping);
            }
        }
        else {
            echo '<strong>ERROR:</strong> ' . self::ERROR_DIRECT_ACCESS;
        }
    }

    /**
     * Generates an RSS feed containing the n most recent status updates.
     */
    private function _generate_rss_feed() {
        $feed_url = PingFmCustomUrlUtils::get_feed_url();
        header("Location: $feed_url", true, 301);
        exit;
    }
}

?>
