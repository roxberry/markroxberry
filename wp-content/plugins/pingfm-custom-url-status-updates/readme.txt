=== Ping.fm Custom URL ===
Contributors: mjacob
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NYC67XZ9JJKLN
Tags: ping.fm, pingfm, wp-pingfm, status, custom url, social networks
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 2.0.1

Receives blogs, micro-blogs, and status updates from Ping.fm and posts them to
your blog in the best way possible.

== Description ==

Great for having a personal archive of content sent through Ping.fm, displaying
status updates in your sidebar, or starting a mobile photo blog. While there's
plenty of room for customizing the plugin, sane defaults ensure that 90% of
people can get up and running quickly. Keep reading for a full list of features
below...

= Minimum System Requirements =

This plugin has required PHP 5 for quite a while, but the increased
requirements below are in line with the new standard coming in WordPress 3.2.
Other version of PHP/MySQL might work just fine, but I personally use even
later versions of both for development and testing. The minimums listed below
are just that---minimums. My advice is to use the latest stable versions you
can get your hands on.

*   WordPress 3.0
*   PHP 5.2.0
*   MySQL 5.0.15

= A Quick Note About Donations =

Starting with version 2.0.0 of this plugin, I added a PayPal donation link to
the information box in the upper-right corner of the page. Let me be clear: I'm
**not** soliciting donations, there is no charge for this plugin, and you are
**not** obligated to give me any money for any reason. However, people have
asked me if they could donate, and I wanted to facilitate that if you feel so
inclined.

I've spent countless hours developing and supporting this plugin, but it's
mostly a labor of love. I first created the plugin to meet my own needs, and
it's kind of taken off from there. If you feel that the plugin has been useful
to you in some way, I'd be honored to take your money and spend it on gadgets
and firearm accessories.

= Ye Olde Features Liste =

*   Works with Ping.fm's Custom URL feature to collect blog entries, micro-blog
    entries, and status updates and post them to your blog.

*   Photos posted through Ping.fm can be downloaded to your own blog and
    displayed as part of a normal post. It's a super-easy way to set up an
    awesome self-hosted mobile photo blog.

*   Status updates and micro-blogs can be administered through a native
    WordPress management screen while blogs are, well... normal blog entries!
    Every feature of this plugin is tightly integrated with WordPress to
    provide a native look and feel.

*   A beautiful settings page with predefined smart defaults and contextual
    help when you need it.

*   Status updates are posted to the sidebar and displayed with a widget. A
    standard template tag is also provided for those of you who are
    anti-widget.

*   The sidebar widget is customizable with the number of status updates to
    show and an optional string to prepend all updates with (e.g. you might use
    your first name if all your status updates begin with a verb like mine do:
    Matt is...). Sensible CSS and examples are provided for you to customize
    the look and feel of your widget.

*   Provides an RSS feed and permalinks for your pings so that you can share
    them with the rest of the world.

*   Blog entries and micro-blog entries can have certain metadata applied to
    them automatically. You can set the author, category, tags, and post status
    (published or draft) for all incoming pings.

*   All the cool features of Ping.fm: post to your self-hosted WordPress blog
    using SMS, IM, email, desktop gadgets, mobile apps, etc.

== Installation ==

1.  Extract the .zip file you downloaded to a directory under
    `/wp-content/plugins/`. The default will be the full name of this plugin
    project, which is `pingfm-custom-url-status-updates`. Alternatively, you
    can search for this plugin from the WordPress admin area and install it
    that way, or just [click here](http://coveredwebservices.com/wp-plugin-install/?plugin=pingfm-custom-url-status-updates)
    to use Mark Jaquith's plugin installer tool.

2.  Activate the plugin through the 'Plugins' menu in WordPress.

3.  Go to WordPress Admin > Plugins > **Ping.fm Settings** to get your
    custom URL and then follow the instructions on that page to link up with
    your Ping.fm account. Essentially, you just want to copy and paste the
    given URL into your Ping.fm Custom URL settings. Feel free to change any
    other settings on that page (it is *your* blog, after all). If you need to
    specify a default category for incoming posts, and that category doesn't
    exist yet, go and create it first, and then come back and select it.

4.  Head over to WordPress Admin > Design > **Widgets** and add the 'Ping.fm'
    widget to your sidebar. If you click the 'Edit' link in the activated
    widget, you'll be able to configure a whole slew of options to your liking.
    If your theme is non-widgetized, or if you just have a bias against widgets
    for some reason, you can use the `wp_pingfm_status()` template tag instead.
    More information about that is on the (Other Notes)[other_notes/] page.

5.  Post something to your Ping.fm account and let the good times roll! If
    nothing shows up at first, it's probably because you haven't posted anything
    yet. (It seems obvious, but you'd be surprised.) Only updates made *after*
    you install the plugin will show up, and even then, only after you've told
    Ping.fm to post to your custom URL. And, on the Ping.fm side, be sure to
    check the appropriate boxes for the kinds of updates you'd like to send to
    your custom URL. Finally, if you don't think your status updates are
    showing up, make sure the widget has been added to your sidebar and that
    you've saved the changes.

6.  For those about to ping, we salute you!

== Frequently Asked Questions ==

= Can I change the number of status updates displayed? =

Yes, of course you can! Go to your sidebar widgets and hit the 'Edit' link for
the Ping.fm widget. This is all mentioned in the installation instructions, but
I can see that you're the impatient type that never reads anything. I bet you
didn't read the owner's manual for your car, either, huh?

= Why am I not seeing any updates in my sidebar? =

Well... have you posted any yet? Only status updates posted to Ping.fm *after*
installing the plugin and *after* configuring the custom URL will show up on
your blog. If you're confident you've done everything right, there might be
something else going on. You might have discovered a bug, or an "undocumented
feature" as we software engineers like to call them. Send me some sugar at
<matt@mattjacob.com> and I'll see what I can do.

= Why aren't pings coming through to my site? =

Read the question immediately preceding this one once more. Done? Now go through
the checklist below and make sure everything is kosher:

*   Is your site blocked off from others using HTTP Basic Authentication or some
    other method? Do you have firewall rules restricting users to a certain IP
    range?

*   Are you using any WordPress plugins that limit outside access? I've seen
    problems related to the Bad Behavior plugin in the past.

*   Can you hit the "secret posting URL" from your browser? You should get an
    error message, which is a good sign that the plugin is installed and
    working.

*   Do you have some boxes checked over at the
    [Custom URL Settings](https://ping.fm/custom/) page? Next to *Use for:*, you
    need at least one of those options selected (preferably all three).

*   Under the main [Ping.fm Service Settings](http://ping.fm/settings/), is the
    box checked next to the *Post messages to Custom URL* option? This is
    important.

*   If you're only posting status updates or micro-blogs from Ping.fm, is the
    widget activated and placed somewhere?

= Can I post blog entries or pages from my Ping.fm account? =

The original answer was a defiant "get off my lawn, you damn kids!" That lasted
for a whole point release. By popular demand---and thanks in no small part to
Rocco and Vinny, who were sent to convince me---the ability to post blog entries
has been available since 0.9.2. Support for creating pages might be added in a
future release. Give the people want they want, right?

= Will this plugin work with WordPress.com blogs? =

Definitely not. That would be goofy, because WordPress.com blogs don't allow
plugins. Plus, Ping.fm already has [support](http://ping.fm/blog/wordpress-bots-you-betcha/)
for WordPress.com hosted blogs anyway.

= Can you add feature X, Y, and Z? And Q? And L? =

I *could* add all kinds of features, but the real question is whether I
*should*. The intent and operation of this plugin was meant to be simple, but
go ahead and pitch your feature idea to me. If it's not too outrageous and I
think others would benefit from it, I'll probably add it to a future version of
the plugin.

== Screenshots ==

1.  A nicely-designed settings page stands at the ready to accept your input.
2.  Everything is clearly laid out and explained.
3.  More detailed help is only a tab click away at all times.
4.  The illustrious status widget. You feed it status updates, it feeds widgety
    goodness to all your blog's readers.
5.  A full-blown admin page to manage all your status updates and micro-blogs.

== Changelog ==

= 2.0.1 =

*   Fixed edge case where "**Fatal error**: Call to undefined function
    wp_create_category()" would be triggered if the `pingfm_options` record was
    manually deleted from the `wp_options` table.

= 2.0.0 =

*   Transitioned away from a separate database table holding status updates to
    WordPress's new "custom post types" that were introduced in 3.0. This had
    the added bonus of providing "real" single post pages for status updates
    and micro-blogs, and a native WordPress interface for managing it all.
*   Added true support for images sent from Ping.fm. They're now downloaded
    locally and given first-class treatment (like any other uploaded media).
*   Redesigned the entire settings page and rewrote much of the help content.
*   Rewrote much of the code and split it out into separate classes for
    performance and organizational reasons.
*   Fixed timestamp offset bug (again). Hopefully fixed for good this time.
*   Added robust upgrade functionality designed to be extended to future
    releases as well as this one.

= 1.3.3 =

*   Fixed timezone handling throughout the plugin. Be sure to set a named
    timezone (the city closest to you) in *General Settings*, or else your
    timestamps might not be correct.
*   Fixed invalidly-nested `</li>` element in the widget.
*   Minor code beautification. Very minor.

= 1.3.2 =

*   Further tweaking of the way rewrite rules are filtered and flushed. In some
    cases, rewrite rules weren't taking affect immediately after a brand-new
    installation of the plugin, but that should be remedied with this release.

= 1.3.1 =

*   Tweaked the rewrite rules a little more. There were some edge cases where
    other things would overwrite our rules, but that should be fixed now.

= 1.3.0 =

*   Added support for displaying images associated with pings. A corresponding
    option has been added to the settings page.
*   Added specific check for PHP 4 to prevent code from even being parsed if
    an insufficient version of PHP is found.
*   Failure for PHP 4 now handled gracefully instead of with a syntax error.
*   Moved flushing of rewrite rules to only happen on plugin activation and
    not on every page load. This should have a noticeable positive impact on
    performance. The only downside is that the plugin will have to be
    deactivated and reactivated if pretty permalinks are enabled (a worthy
    trade-off in my opinion).
*   Cleaned up the code in various places to make it more readable (especially
    with regard to the error message pseudo-constants).

= 1.2.1 =

*   Primarily a documentation release. Lots of stuff in the README was
    clarified, fortified, beautified, and just plain shifted around.
*   Fixed a small bug related to a new option that was added in 1.2.0. After
    saving the options twice, the problem would have fixed itself anyway.
*   Added screenshots for the benefit of the WordPress Plugin Directory.
*   Moved the changelog off of my site and into the Plugin Directory to give
    people a more central point of contact.

= 1.2.0 =

*   Added option to force all status updates to become actual posts.
*   Added option in widget to "permalinkify timestamps" (archive page is
    editable via `tmpl-single.php`).
*   Invented new English word: *permalinkify*.
*   Tried to make the settings page clearer and easier to understand.
*   Did some code housekeeping.
*   Added Ping.fm logo to settings page.

= 1.1.0 =

*   Complete code rewrite (PHP 4 procedural to PHP 5 OO) and new way of storing
    settings in the database.
*   Did away with external `ping.php` file for receiving updates.
*   Added native WordPress URL hooks and internal support for `mod_rewrite`.
*   Added RSS feed containing 20 most recent status updates.
*   Added archive/permalink pages for individual pings (mostly in conjunction
    with RSS feed, but can be used elsewhere as well). Page is templated for
    easy customization.
*   Provided interface for user-supplied widget styles (CSS).
*   Widget HTML/CSS makes more sense. Widget container has an ID of `wp-pingfm`
    and a class of `wp-pingfm-widget`.
*   Added `wp_pingfm_status()` for people using non-widgetized themes. See
    *Other Notes* section for docs.

= 1.0.1 =

*   CSS is now targeting `#pingfm` in a less specific way (because it broke on
    themes with multiple sidebars).
*   Fixed random bug only seen in WebKit where a space preceding a status update
    was considered significant whitespace. (Don't ask me---I don't build it, I
    just code to it.)

= 1.0.0 =

*   The first non-beta release!
*   Modified `CREATE TABLE` for installer after reports of it failing on MySQL
    versions < 4.1.
*   Added check for widget to determine if database table was created properly.
*   Tweaked a few textual things here and there.

= 0.9.7 =

*   This version was never released publicly and the changes made for it were
    rolled into 1.0.0.

= 0.9.6 =

*   Added [this page](http://mattjacob.com/pingfm-wordpress-plugin) as a home
    base for the plugin on my site.
*   Made the README more readable (hopefully). The documentation should be
    easier to understand now.
*   Fixed the `mod_rewrite` example to include the `[L]` flag. The entire line
    didn't get copied from my conf file the first time around.

= 0.9.5 =

*   Minor textual changes and a speed enhancement (eliminated a DB call using
    some crafty coding).

= 0.9.4 =

*   Added widget options to specify colors used in injected style sheet (CSS).
*   Reorganized, streamlined, and beautified some code.

= 0.9.3 =

*   Fortified README with some additional information and a `mod_rewrite`
    example for shortening the inevitably long URL.
*   The plugin now auto-detects the directory it's running from. Anything under
    `/wordpress/wp-content/plugins` should work reasonably well. If the
    WordPress Plugin Directory wants to create a really long folder structure,
    so be it.

= 0.9.2 =

*   Added support for all types of Ping.fm updates. Blog and micro-blog posts
    now show up as actual entries in WordPress.

= 0.9.1 =

*   The initial public release! After being hacked together with loving care
    during a couple late nights, the code finally sees the light of day. Cue
    angelic choir and bright background light...

== Upgrade Notice ==

= 2.0.0 =

This version brings the awesome sauce and slathers it all over your WordPress
blog. No more janky DB tables, an admin interface for managing pings, local
image hosting, better performance, minor bug fixes... What are you waiting
for?!

== Template Tag Usage ==

void **wp\_pingfm\_status** ( [ int `$limit` [, string `$prefix` [, bool `$permalink` ]]] )

Displays status updates in an unordered list (`<ul>`). All parameters are
optional.

= Parameters =

*limit*
> The number of status updates to show. Default value is 10. Depending on your
> needs and the location on the page, a value between 5 and 15 is usually pretty
> reasonable.

*prefix*
> Some text to display before every status update. Default value is nothing.
> This can be useful if all of your status updates begin with a verb. For
> example, you might post "going to the store" from Ping.fm. If your prefix is
> "Matt is", the final output will be "Matt is going to the store". Neat, huh?

*permalink*
> Whether to show timestamps as permalinks, a la Twitter. The archive page is
> editable via the `tmpl-single.php` template file.

= Example =

If you're using the Twenty Ten theme that ships with WordPress 3.0, you might
put the following into your `sidebar.php` file:

    <li id="pingfm" class="widget-container">
        <h3 class="widget-title">Status Updates</h3>
        <?php wp_pingfm_status(); ?>
    </li>

mixed **wp\_pingfm\_latest** ( void )

Retrieves the single most recent ping (status update) from the database.

= Return Values =

A `stdClass` object representing the latest ping, if it exists. Otherwise,
false. The object will contain the properties listed below. For more
information, dig through the source of the WordPress `get_posts()` function.

    ID
    post_author
    post_date
    post_date_gmt
    post_content
    post_title
    post_excerpt
    post_status
    comment_status
    ping_status
    post_password
    post_name
    to_ping
    pinged
    post_modified
    post_modified_gmt
    post_content_filtered
    post_parent
    guid
    menu_order
    post_type
    post_mime_type
    comment_count
    filter
