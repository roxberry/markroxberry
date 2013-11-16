<?php

/**
 * Constants used throughout the plugin
 */
interface PingFmCustomUrlConstants {
    /**
     * Version numbers and names
     */
    const PLUGIN_VERSION      = '2.0.1';
    const DB_VERSION          = '2.0';
    const DB_TABLE_SUFFIX     = 'pingfm';
    const CUSTOM_POST_TYPE    = 'pingfm';
    const OPTION_NAME         = 'pingfm_options';

    /**
     * Error messages
     */
    const ERROR_PERMALINK_404 = 'No status updates found for the ID you provided.';
    const ERROR_DIRECT_ACCESS = 'No direct access allowed. Post something from Ping.fm instead.';
    const ERROR_ZERO_RECORDS  = 'Hey, we searched all over the database, but we couldn\'t find any status updates. Have you posted any yet?';

    /**
     * Admin status messages
     */
    const MSG_TOKEN_SAVED     = 'New key generated and saved.';
    const MSG_UPGRADE_ERROR   = 'Aw, Snap! Something went wrong with the upgrade.';
    const MSG_UPGRADE_SUCCESS = 'Hooray! Upgrade completed successfully.';
    const MSG_REPORT_SENT     = 'Error report sent. We\'ll be in touch.';
    const MSG_SETTINGS_SAVED  = 'Settings saved.';
}

/**
 * The options object that gets serialized and saved to the database. There
 * aren't any accessors/mutators because it's nothing more than a bucket-o-data
 * class.
 */
class PingFmCustomUrlOptions implements PingFmCustomUrlConstants {
    public $default_author;
    public $default_category;
    public $default_status;
    public $default_tags;
    public $inline_images;
    public $need_upgrade;
    public $plugin_version;
    public $title_structure;
    public $token;
    public $upgrade_error;
    public $upgrade_methods;
    public $user_css;
    public $widget_limit;
    public $widget_links;
    public $widget_prefix;
    public $widget_title;

    /**
     * Private constructor to prevent use of 'new' (use factory() instead)
     */
    private function __construct() {
        $this->default_author   = 1;
        $this->default_category = 0;
        $this->default_status   = 'publish';
        $this->default_tags     = 'pingfm, social, networking, alpacas';
        $this->inline_images    = true;
        $this->need_upgrade     = false;
        $this->plugin_version   = self::PLUGIN_VERSION;
        $this->title_structure  = 'Status update on {date} at {time}';
        $this->token            = PingFmCustomUrlUtils::generate_unique_token();
        $this->upgrade_error    = false;
        $this->upgrade_methods  = array();
        $this->user_css         = PingFmCustomUrlUtils::get_default_user_css();
        $this->widget_limit     = 10;
        $this->widget_links     = true;
        $this->widget_prefix    = 'From Ping.fm:';
        $this->widget_title     = 'Status Updates';
    }

    /**
     * Factory method to get an instance of this object. Will try to get stored
     * copy from the database/WordPress options cache first. If that doesn't
     * exist, a new object will be instantiated and returned.
     *
     * @return instance of PingFmCustomUrlOptions
     */
    public static function factory() {
        $instance = get_option(self::OPTION_NAME);

        if (!is_object($instance)) {
            $c = __CLASS__;

            $instance = new $c;
            $instance->save();
        }

        return $instance;
    }

    /**
     * Saves this object out to the database (but only if our local copy is
     * different). The WordPress update_option() handles all the complexity of
     * checking for differences and selectively updating.
     */
    public function save() {
        update_option(self::OPTION_NAME, $this);
    }
}

class PingFmCustomUrlUtils implements PingFmCustomUrlConstants {
    /**
     * Adds a new ping to the database, taking into account the different rules
     * for processing each post method. If media is present and that setting is
     * enabled, will attempt to download the image locally and attach it to the
     * post.
     *
     * @param ping a slightly modified array of Ping.fm Custom URL fields
     */
    public static function insert_ping($ping) {
        $options = PingFmCustomUrlOptions::factory();
        $message_type = 'message';

        if ($options->inline_images && !empty($ping['media'])) {
            $message_type = 'raw_message';
        }

        $args = array(
            'comment_status' => get_option('default_comment_status'),
            'ping_status'    => get_option('default_ping_status'),
            'post_author'    => $options->default_author,
            'post_category'  => array($options->default_category),
            'post_content'   => $ping[$message_type],
            'post_date'      => (!empty($ping['date'])) ? $ping['date'] : current_time('mysql'),
            'post_status'    => $options->default_status,
            'post_title'     => $ping['title'],
            'post_type'      => 'post',
            'tags_input'     => $options->default_tags,
        );

        if ('blog' != $ping['method']) {
            $args['post_type']    = self::CUSTOM_POST_TYPE;
            $args['post_content'] = make_clickable($ping[$message_type]);
            $args['post_status']  = 'publish';

            if ($options->title_structure) {
                $args['post_title'] = self::get_replaced_title(
                    $ping[$message_type],
                    $options->title_structure,
                    $args['post_date']
                );
            }
            else {
                $args['post_title'] = $ping[$message_type];
                $args['post_name']  = self::get_short_title($ping[$message_type]);
            }
        }

        $post_id = wp_insert_post($args);

        if ($options->inline_images && !empty($ping['media'])) {
            require_once(ABSPATH . 'wp-admin/includes/admin.php');

            $media_html = media_sideload_image($ping['media'], $post_id, $args['post_title']);

            if (!is_wp_error($media_html)) {
                $args['ID']                    = $post_id;
                $args['post_content_filtered'] = $args['post_content'];
                $args['post_content']          = $media_html . "\n\n" . $args['post_content'];

                wp_insert_post($args);
            }
        }

        return $post_id;
    }

    /**
     * Creates a 32-character unique token that's hard to guess. This is the
     * basis of the plugin's authentication scheme to make sure only authorized
     * users are pinging the blog.
     *
     * @return the MD5 hash of a random, unique string
     */
    public static function generate_unique_token() {
        return md5(uniqid(rand(), true));
    }

    /**
     * Returns the default CSS that the plugin ships with (to style up the
     * widget). We define this function here instead of in the HTML class
     * because we also need this string in order to initialize the options
     * object, and the HTML class isn't included on every page load.
     *
     * @return default styles without surrounding <style> tags
     */
    public static function get_default_user_css() {
        return <<<EOF
#sidebar #wp-pingfm .updated-time { color: #999; }
#sidebar #wp-pingfm ul { margin-left: 0; margin-right: 0; }
#sidebar #wp-pingfm ul li { color: #333; margin: 0 0 10px 0; font-size: 11px; }
#sidebar #wp-pingfm ul li:before { content: ""; }
EOF;
    }

    /**
     * Takes a potentially long blog title, and if it's more than 40 characters
     * long, shortens it using intelligent word wrapping. Optionally, will also
     * sanitize the shortened title for use as a post slug.
     *
     * @param title the long title to shorten
     * @param slug  whether this is going to be a post slug
     * @return      the shortened (and maybe slugged) title
     */
    public static function get_short_title($title, $slug = true) {
        $new_title = $title;

        if (strlen($title) > 40) {
            $new_title = explode('|', wordwrap($title, 40, '|'));
            $new_title = $new_title[0];

            if ($slug) {
                $new_title = sanitize_title($new_title);
            }
            else {
                $new_title = $new_title . '...';
            }
        }

        return $new_title;
    }

    /**
     * Given a blog entry title and a format string, substitutes predefined
     * variables in the format string with live values. If no format is
     * specified, the unmodified title is returned.
     *
     * @param title  the raw title to be transformed
     * @param format the format string with substitution tokens
     * @return       the new title after replacements
     */
    public static function get_replaced_title($title, $format, $datetime) {
        $new_title = $title;

        if ($format) {
            $excerpt = self::get_short_title($title, false);
            $date_ = mysql2date(get_option('date_format'), $datetime);
            $time_ = mysql2date(get_option('time_format'), $datetime);

            $new_title = str_replace(
                array('{excerpt}', '{date}', '{time}'),
                array($excerpt, $date_, $time_),
                $format
            );
        }

        return $new_title;
    }

    /**
     * An improved version of the WordPress get_option() function. This method
     * actually returns null when an option isn't set, and it also converts Y/N
     * values to real booleans.
     *
     * @param option option name to look up in WP options table
     * @return       the option's value, or null if it doesn't exist
     */
    public static function better_get_option($option) {
        $value = get_option($option);

        if (false === $value) {
            return null;
        }
        else {
            if ('Y' == strtoupper($value)) {
                $value = true;
            }
            else if ('N' == strtoupper($value)) {
                $value = false;
            }
        }

        return $value;
    }

    /**
     * Constructs the full URL to the RSS feed that WordPress automatically
     * generates for custom post types. Takes into account whether mod_rewrite
     * is enabled.
     *
     * @return the URL of our custom post type's RSS feed
     */
    public static function get_feed_url() {
        global $wp_rewrite;

        $url  = get_bloginfo('rss2_url');
        $url .= $wp_rewrite->using_permalinks() ? '?' : '&';
        $url .= 'post_type=' . self::CUSTOM_POST_TYPE;

        return $url;
    }

    /**
     * Constructs the full URL that Ping.fm should POST to. Takes into account
     * whether mod_rewrite is enabled.
     *
     * @return a URL that can be set as Ping.fm's Custom URL
     */
    public static function get_post_url($token) {
        global $wp_rewrite;

        $url  = get_bloginfo('url') . '/';
        $url .= $wp_rewrite->using_permalinks() ? 'pingfm/post/' : '?pa=post&pk=';
        $url .= $token;

        return $url;
    }
}

?>
