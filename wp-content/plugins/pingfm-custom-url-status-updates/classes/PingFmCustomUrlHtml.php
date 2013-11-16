<?php

/**
 * Acts as a container class for all major blocks of HTML used by the plugin.
 * This class is only included on an as-needed basis, so normal plugin
 * operations should be quicker since we're not pulling in a ton of HTML on
 * every request. We break out of PHP mode when possible to increase
 * performance.
 */
class PingFmCustomUrlHtml implements PingFmCustomUrlConstants {
    /**
     * Displays a message that lets the user know that an upgrade is available.
     */
    public static function print_upgrade_notice() {

?>
<div id="message" class="error">
  <p>
    <strong>Hey, this is important!</strong> Head over to the <a href=
    "plugins.php?page=wp-pingfm-settings">Ping.fm plugin settings</a> to finish
    upgrading to version <?php echo self::PLUGIN_VERSION; ?>. Do it! Go!
  </p>
</div>
<?php

    }

    /**
     * Returns the help content used by the add_contextual_help() function to
     * display contextual admin help information in a little tab pulldown.
     *
     * @return contextual help (HTML) for the main settings page
     */
    public static function get_contextual_help() {
        $date_format = date_i18n(get_option('date_format'));
        $time_format = date_i18n(get_option('time_format'));

        $help_text = <<<EOF
<p>
  <strong>A Photographic Memory</strong>
</p>

<p>
  When you tick the box to <em>download images from photo pings</em>, something special happens.
  If you <a href="http://pingfm.pbworks.com/Posting-Photos">post a photo</a> with that setting
  enabled, we'll go out and download the photo to your local <a href="upload.php">media
  library</a>. Then we'll add the photo to your post and put the text of your message below it, a
  la Posterous, Tumblr, etc.
</p>

<p>
  How cool is that?! Too cool for school.
</p>

<p>
  <strong>Did Someone Say Widget CSS?</strong>
</p>

<p>
  The styles below will be inserted into your blog's pages when this plugin's widget is active.
  You may modify the rules below or copy and paste them into one of your other style sheets and
  delete them here&#8212;it's up to you! For reference, you can style the ID
  <code>#wp-pingfm</code> and the class <code>.wp-pingfm-widget</code>.
</p>

<p>
  Some themes will require different selectors in order to style the widget properly
  (especially when dealing with CSS specificity issues), but the default styles provided here
  should work well with the default WordPress theme. For themes with multiple sidebars or any
  other variations, you'll just have to experiment on your own.
</p>

<p>
  <strong>Valid Title Placeholders</strong>
</p>

<dl>
  <dt>
    <code>{date}</code>
  </dt>

  <dd>
    Same as Date Format from General Settings, e.g.
    <code>$date_format</code>
  </dd>

  <dt>
    <code>{time}</code>
  </dt>

  <dd>
    Same as Time Format from General Settings, e.g.
    <code>$time_format</code>
  </dd>

  <dt>
    <code>{excerpt}</code>
  </dt>

  <dd>
    The first 40 characters of your status update, e.g. <code>Heading to the Auld Dubliner for
    a pint...</code>
  </dd>
</dl>
EOF;

        return $help_text;
    }

    /**
     * Builds the mammoth settings page. Some parts of this are a little nasty
     * because of the mixing of PHP and HTML, but in the end, it's the best
     * overall way to do it.
     */
    public static function print_main_settings() {
        $options = PingFmCustomUrlOptions::factory();
        settings_errors('pingfm_options');

?>
<div class="wrap" id="wp-pingfm-settings">
  <div id="icon-pingfm" class="icon32">
    <br />
  </div>

  <h2>
    Ping.fm Custom URL Settings
  </h2>

  <form method="post" action="options.php" id="pingfm_settings_form">
    <input type="hidden" name="pingfm_options[button_action]" id="pingfm_button_action" value="" />

    <?php settings_fields('pingfm_plugin_options'); ?>

    <?php if ($options->upgrade_error) { ?>
    <h3>
      You Shouldn't Be Seeing This
    </h3>

    <p>
      <strong>This is an error message, and error messages suck.</strong>
    </p>

    <p>
      That's why you shouldn't be seeing this one right now. On a most
      unfortunate note, we encountered an error while attempting to upgrade
      the old pings in your database.
    </p>

    <p>
      Your data is safe, but you won't be able to use the latest version of
      this plugin until the situation gets sorted out.
    </p>

    <p>
      If you want to be really helpful, click the button below to report the
      problem. Your email address will be included in the report, but we'll
      only use it to get in touch with you so that we can help you fix
      whatever it is that's causing this plugin to puke.
    </p>

    <p>
      Alright: less talk, more click. Get on it!
    </p>

    <p>
      <input type="button" id="pingfm_button" class="button" value="Report This Little Bugger" />
    </p>
    <?php } else if ($options->need_upgrade) { ?>
    <h3>
      Really Important Upgrade Information
    </h3>

    <p>
      <strong>We need to convert your existing pings to a WordPress 3.0 format.</strong>
    </p>

    <p>
      The amount of time needed for the conversion will depend on the number of status updates
      you're got in your database. The whole process shouldn't take more than a minute
      or two.
    </p>

    <p>
      As with any upgrade operation that touches the database, it would be wise to make a backup
      of your database before continuing.
    </p>

    <p>
      Don't worry&#8212;it shouldn't hurt a bit.
    </p>

    <p>
      <input type="button" id="pingfm_button" class="button" value="Let The Conversion Begin!" />
    </p>
    <?php } else { ?>
    <h3>
      Your Super Secret URL
    </h3>

    <ol>
      <li>Log in to your <a href="http://ping.fm">Ping.fm</a> account and head over to the
        <a href="https://ping.fm/custom/">Custom URL Settings</a>.
      </li>

      <li>Check <em>Update custom URL</em> in order to activate the text box.
      </li>

      <li>Copy and paste the URL below into the <em>Custom URL</em> box.
      </li>

      <li>Hit <em>Submit</em>.
      </li>
    </ol>

    <p>
      <input type="text" id="pingfm_secret_url" value="<?php echo PingFmCustomUrlUtils::get_post_url($options->token); ?>"
      class="code" style="width:100%;" />
    </p>

    <p>
      Be sure not to give this URL out to anyone. Whoever has it (and knows what to do with it)
      will be able to post content to your blog. If you think that someone might have gotten access
      to your URL/key somehow, use the button below to generate a new one. And try to keep the new
      one safe, will ya?
    </p>

    <p>
      <code><?php echo $options->token; ?></code> (that's your current key)
    </p>

    <p class="submit">
      <input type="button" id="pingfm_button" class="button" value="Generate New Key" />
    </p>

    <h3>
      All About That Thing You Ping
    </h3>

    <p>
      The options below are grouped by the type of posting method they apply to. With Ping.fm,
      there's the notion that different social networks can accept different types of posts. It's
      a pretty complex and powerful feature, so I'd encourage you to read the <a href=
      "https://pingfm.pbworks.com/Posting-Methods">official documentation</a> to fully understand
      how it works.
    </p>

    <div id="poststuff" class="metabox-holder">

      <div class="postbox">
        <h3>
          All Posting Methods
        </h3>

        <div class="inside">

          <p>
            <label>
              <input type="checkbox"
                name="pingfm_options[inline_images]"
                id="pingfm_inline_images"
                value="Y"
                <?php echo ($options->inline_images) ? 'checked="checked"' : ''; ?> />
              Download images from photo pings and display them
            </label>
          </p>

        </div>
      </div>

      <div class="postbox">
        <h3>
          Status Updates and Micro-blogging
        </h3>

        <div class="inside">

          <h4>
            Cascading Style Something Something
          </h4>

          <p>
            The styles below should be used to style up the widget that displays status updates and
            micro-blogs. Not sure what to put in the box? Click the help tab above for some information
            that will have your problems cascading away.
          </p>

          <p>
            <label for="pingfm_user_css">Use these styles:</label> [ <a href="#" id="pingfm_restore_css">restore defaults</a> ]
            <br />
            <br />
            <textarea name="pingfm_options[user_css]" id="pingfm_user_css" rows="6" cols="80"><?php echo esc_html($options->user_css); ?></textarea>
            <textarea id="pingfm_default_user_css" style="display:none;"><?php echo PingFmCustomUrlUtils::get_default_user_css(); ?></textarea>
          </p>

          <h4>
            Really Simple Syndication = Simply Awesome
          </h4>

          <p>
            An RSS feed of your most recent status updates/micro-blogs is generated for you
            automatically. The global WordPress setting for syndication feeds determines how many updates
            are shown in your Ping.fm feed. To change that setting, <a href="options-reading.php">click
            here</a>.
          </p>

          <p>
            <img src="<?php echo get_bloginfo('wpurl'); ?>/wp-includes/images/rss.png" alt="RSS icon"
            style="vertical-align:-2px;" /> Your feed URL: <a href=
            "<?php echo PingFmCustomUrlUtils::get_feed_url(); ?>"><?php echo PingFmCustomUrlUtils::get_feed_url(); ?></a>
          </p>

          <h4>
            Title Substitution Pattern
          </h4>

          <p>
            Problem: when you post a status update or micro-blog, it contains just a message and no
            title. WordPress posts usually have titles, though. (Most themes expect one, and that's how
            the slug is determined.) Solution: we'll make our own stinkin' titles!
          </p>

          <p>
            Specify a title structure below. A few placeholders are available. Click the help tab above
            to find out the exact strings to use as placeholders. Any other input will be used as-is.
          </p>

          <table class="form-table">
            <tr>
              <th>
                <label for="pingfm_title_structure">Title Structure</label>
              </th>
              <td>
                <input type="text" name="pingfm_options[title_structure]" id="pingfm_title_structure"
                class="regular-text" value="<?php echo esc_attr($options->title_structure); ?>" />
              </td>
            </tr>
          </table>

        </div>
      </div>

      <div class="postbox">
        <h3>
          Just Blogging
        </h3>

        <div class="inside">

          <h4>
            Default Values for Blog Posts
          </h4>

          <p>
            The options below apply to incoming pings sent with the blogging post method.
          </p>

          <table class="form-table">
            <tr>
              <th>
                Default Status
              </th>
              <td>
                <label>
                  <input type="radio"
                    name="pingfm_options[default_status]"
                    id="pingfm_default_status_publish"
                    value="publish"
                    <?php echo ('publish' == $options->default_status) ? 'checked="checked"' : ''; ?> />
                  Published
                </label>
                &#160;&#160;
                <label>
                  <input type="radio"
                    name="pingfm_options[default_status]"
                    id="pingfm_default_status_draft"
                    value="draft"
                    <?php echo ('draft' == $options->default_status) ? 'checked="checked"' : ''; ?> />
                  Draft
                </label>
              </td>
            </tr>

            <tr>
              <th>
                <label for="pingfm_default_author">Default Author</label>
              </th>
              <td>
                <?php

                wp_dropdown_users(array(
                    'name'     => 'pingfm_options[default_author]',
                    'selected' => $options->default_author
                ));

                ?>
              </td>
            </tr>

            <tr>
              <th>
                <label for="pingfm_default_category">Default Category</label>
              </th>
              <td>
                <?php

                wp_dropdown_categories(array(
                    'name'       => 'pingfm_options[default_category]',
                    'selected'   => $options->default_category,
                    'orderby'    => 'name',
                    'hide_empty' => 0
                ));

                ?>
              </td>
            </tr>

            <tr>
              <th>
                <label for="pingfm_default_tags">Default Tags</label>
              </th>
              <td>
                <input type="text" name="pingfm_options[default_tags]" id="pingfm_default_tags" class=
                "regular-text" value="<?php echo esc_attr($options->default_tags); ?>" /> <span class=
                "description">Separated by commas</span>
              </td>
            </tr>

          </table>

        </div>
      </div>
    </div>

    <p class="submit">
      <input type="submit" class="button-primary" value="Save Changes" />
    </p>
    <?php } ?>
  </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#pingfm_secret_url').click(function() {
        $(this).select();
    });

    $('#pingfm_restore_css').click(function() {
        $('#pingfm_user_css').val($('#pingfm_default_user_css').val()).select();
        return false;
    });

    var pingfm_button = $('#pingfm_button');

    pingfm_button.click(function() {
        var pingfm_button_action = 0;

        switch (pingfm_button.val()) {
            case 'Generate New Key':
                pingfm_button_action = 1;
                break;
            case 'Let The Conversion Begin!':
                pingfm_button_action = 2;
                break;
            case 'Report This Little Bugger':
                pingfm_button_action = 3;
                break;
        }

        $('#pingfm_button_action').val(pingfm_button_action);
        $('#pingfm_settings_form').submit();
    });
});
</script>
<?php

    }

    /**
     * Builds the widget settings... widget? Existing values will be used to
     * pre-populate form fields when available.
     */
    public static function print_widget_settings() {
        $options = PingFmCustomUrlOptions::factory();

?>
<p>
  <label>
    Title:
    <input
      class="widefat"
      id="pingfm_widget_title"
      name="pingfm_widget_title"
      type="text"
      value="<?php echo $options->widget_title; ?>" />
  </label>
</p>

<p>
  <label>
    Prefix updates with:
    <input
      class="widefat"
      id="pingfm_widget_prefix"
      name="pingfm_widget_prefix"
      type="text"
      value="<?php echo $options->widget_prefix; ?>" />
  </label>
</p>

<p>
  <label>
    Number of updates to show:
    <input
      class="widefat"
      id="pingfm_widget_limit"
      name="pingfm_widget_limit"
      type="text"
      value="<?php echo $options->widget_limit; ?>" />
  </label>
  <br />
  <small>
    Between 5 and 10 seems to work well
  </small>
</p>

<p>
  <label>
    <input
      type="checkbox"
      name="pingfm_widget_links"
      id="pingfm_widget_links"
      value="Y"
      <?php echo ($options->widget_links) ? 'checked="checked"' : ''; ?> />
    Permalinkify timestamps
  </label>
</p>
<?php

    }

    /**
     * Outputs the CSS used by our settings page. By using this function as a
     * callback for admin_head-{page}, we can ensure the CSS only gets added to
     * the <head> of our settings page and nothing else.
     */
    public static function print_admin_css() {

?>
<style type="text/css">
#icon-pingfm { background: transparent url(<?php echo PCUSU_HTTP_PATH; ?>/images/pingfm-logo.png) no-repeat scroll -11px -5px; }
#poststuff h3 { font-size: 13px; }
#poststuff .postbox .inside h4 { font-size: 12px; margin: 20px 6px 8px; }
#poststuff .postbox .inside p { font-size: 12px; }
#poststuff .postbox .inside th { font-size: 12px; }
#poststuff .postbox .inside td { font-size: 12px; }
code { font-size: 12px; }
textarea { font-family: Consolas, Monaco, Courier, monospace; font-size: 12px; width: 100%; }
dl { margin: 1em 0; font-size: 12px; }
dl dt { float: left; width: 90px; }
</style>
<?php

    }
}

?>
