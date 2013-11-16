<?php
/**
 * @package Change_WP_URL
 * @author Chris Nowak
 * @version 1.0
 */
/*
Plugin Name: Change WordPress URL
Plugin URI: http://karmaprogressive.com/2010/05/our-wordpress-change-url-plugin/
Description: A lot of times, new WordPress sites are set up on development servers, to be later moved to the actual domain. WordPress doesn't have any easy way of changing the WordPress URL throughout all of your pages, posts, etc. This plugin changes all instances of the old URL to the new one.
Author: Chris Nowak - KARMA Progressive Interactive
Version: 1.0
Author URI: http://facebook.com/chrisnowak
*/

function admin_cwpu_options(){
echo '<div class="wrap"><h2>Change your WordPress URL</h2>';
if($_REQUEST['submit']){
	exec_cwpu();
} else {print_cwpu_form();}
echo '</div>';
}


function modify_menu(){
	add_options_page(
	'Change WP URL',
	'Change WP URL',
	'manage_options',
	__FILE__,
	'admin_cwpu_options'
	);
}

add_action('admin_menu','modify_menu');



function exec_cwpu() {

	global $wpdb;
	
	$oldurl = get_option('siteurl');
	$newurl = $_REQUEST['newurl'];

	//UPDATE wp_options SET option_value = replace(option_value, 'http://www.old-domain.com', 'http://www.new-domain.com') WHERE option_name = 'home' OR option_name = 'siteurl'
	//UPDATE wp_posts SET guid = replace(guid, 'http://www.old-domain.com','http://www.new-domain.com')
	//UPDATE wp_posts SET post_content = replace(post_content, 'http://www.old-domain.com', 'http://www.new-domain.com')
	
	//update options table
	$wpdb->query("UPDATE ".$wpdb->prefix."options SET option_value = replace(option_value, '".$oldurl."', '".$newurl."') WHERE option_name = 'home' OR option_name = 'siteurl'");
	//update blog posts/news guids 
	$wpdb->query("UPDATE ".$wpdb->prefix."posts SET guid = replace(guid, '".$oldurl."', '".$newurl."')");
	//update blog posts/news content
	$wpdb->query("UPDATE ".$wpdb->prefix."posts SET post_content = replace(post_content, '".$oldurl."', '".$newurl."')");

	echo '<h3>Your database has been updated. You can move your files if you need to, and use the new URL. Thanks for using!</h3>';

}







function print_cwpu_form(){
	$oldurl = get_option('siteurl');			

?>

<h3><span style="color:red">BE CAREFUL:</span> know how to use this plugin and what it does before you attempt to use it.</h3>

<p><ol>
	<li>Backup your database. Either get a plugin to do this, or do it with phpMyAdmin</li>
	<li>Use this BEFORE you move your install to the new location: this should be the last thing you do before moving</li>
	<li>Enter your new URL <strong>WITH NO TRAILING SLASH</strong> and click "Update"</li>
	<li>Once you run this, the "old" WordPress URL will stop working, so move all of your files and database to the new location (if necessary), go to your "new" URL and re-login</li>
	<li>Go to your Settings->General tab, and update the Blog Address, if necessary (only if your blog url is different from base url)</li>
	<li>Then deactivate and delete this plugin! Thanks for using!</li>
</ol></p><br /><br />

<p>Old URL: <?=$oldurl;?></p>

<form method="post">
	<label for="link">New URL:</label>
	<input type="text" name="newurl" value="http://www." style="width:500px" />

	<input type="submit" name="submit" value="Update URL" />
</form>

<?
} // print_form
?>