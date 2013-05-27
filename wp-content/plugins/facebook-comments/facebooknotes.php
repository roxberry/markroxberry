<?php
/*
Plugin Name: Facebook Comments
Plugin URI: http://jameslow.com/2007/11/18/wordpress-plugin-facebook-comments/
Version: 0.36
Description: Copies comments from your imported Facebook Notes back into your blog entries. Took Admam Hill's (http://www.adamhill.ca/?page_id=179) / Thomas Albright's version (http://thomasalbright.info/index.php/2007/05/27/thomasalbrightinfo-facebook) and then edited by James Low (http://jameslow.com). Made to work with Feedburner feeds by Aaron Harp (http://www.aaronharp.com)
Author: James Low
Author URI: http://jameslow.com
*/

function facebook_new_comment ( $commentdata ) {
	// This is copied from the comment-functions.php file, and then certain things got changed:
	$filter = get_option('facebook_filter_comments');
	if ($filter) {
		$commentdata = apply_filters('preprocess_comment', $commentdata);
	}

	$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
	$commentdata['user_ID']         = (int) $commentdata['user_ID'];

	$commentdata['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
	$commentdata['comment_agent']     = $_SERVER['HTTP_USER_AGENT'];

	// We want to use the original comment date, not the time now.
	//$commentdata['comment_date']     = current_time('mysql');
	//$commentdata['comment_date_gmt'] = current_time('mysql', 1);
	
	if ($filter) {
		$commentdata = wp_filter_comment($commentdata);
	}

	// Automatically approve these comments.
	$commentdata['comment_approved'] = 1;

	// Actually add to the database
	$comment_ID = wp_insert_comment($commentdata);
	
	// This is where Spam Karma 2 likes to screw with us...
	if ($filter) {
		do_action('comment_post', $comment_ID, $commentdata['comment_approved']);
	}

	if ( get_settings('comments_notify') && !get_option('facebook_dont_notify') ) {
		wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
	}

	return $comment_ID;
}

function facebook_get_url($url, $challenge) {
	// Facebook account details:
	$email = urlencode(get_option('facebook_email'));
	$pass = urlencode(get_option('facebook_password'));
	$url = urlencode($url);
	ob_flush();
	// We have to use CURL because it lets us set a custom useragent, which itself is needed because Facebook otherwise claims it's not cool enough to support text-mode stuff. Grr....
	// And, for who knows what reason, my webserver is crazy, meaning I can't even use PHP curl properly. (If yours does and you'd like to add this feature, go nuts!)
	// Thankfully, I *can* use it from the command line... backticks, here I come! ;)
	$result = `curl -k -b cookie.txt -c cookie.txt -A "Mozilla/4.0" -d challenge=$challenge -d md5pass= -d noerror=1 -d next=$url -d email=$email -d pass=$pass -e http://www.facebook.com -L https://login.facebook.com/login.php`;
	ob_clean();
	return $result;
}

function facebook_get_postid($url) {
	static $permalinks;
	if (!isset($permalinks)) {
		global $wpdb;
		$thisquery = "SELECT A.ID FROM $wpdb->posts A";
		$ids = $wpdb->get_col($thisquery);
		$permalinks = array();
		foreach ($ids as $id) {
			$permalinks[get_permalink($id)] = $id;
		}
	}
	return $permalinks[$url];
}

function import_facebook_page($notesurl, $verbose = false, $allpages = false, $start = 0) {
	$challenge = md5($time);	// This is just a random 32-digit hex number.
	$test_run = $_POST['test_run'];
	$notespage = facebook_get_url($notesurl . ($start == 0 ? '' : "&start=$start"), $challenge, $start);
	$dontimport = get_option('facebook_dont_import');
	//<li><a href="/notes.php?id=515655611&amp;start=10&amp;hash=3ab614b5df65b1c79833fa33d85e75c2">Next</a></li>
	$notenumber = 0;
	$url2 = "";
	$lines = explode("\n", $notespage);
	//0.34 use default URL 
	$defaultURL = get_option('facebook_default_url');
	// This was originally written in Perl, which is why there are so many bloody regexes everywhere.
	foreach ($lines as $line) {
		//James 0.21 removed space from note title
		if ( strstr($line, '<div class="note_title">') ) {
			// Get the title, original URL, and Facebook URL for each note
			$notenumber = $notenumber + 1;
			//James 0.21 removed space from note title
			$line = preg_replace("/.*?<div class=\"note_title\"><a href=\"([^\"]+)\">/", '($1)', $line);
			$line = preg_replace("/<\/a><\/div>.*/", "", $line);
			$tmp = preg_split("/[()]/", $line);
			$url[$notenumber] = $tmp[1];
			$line = preg_replace("/\(.*?\)/", "", $line);
			$title[$notenumber] = $line;
			$tmp = explode("?", $url[$notenumber]);
			$url2 = $tmp[1];
		} elseif ( strstr($line, "note_redirect.php?") && strstr($line, "View Original Post") && $wpURL[$notenumber] == "" ) {
			$line3 = preg_replace("/.*?note_redirect.php?([^\"]+)\".*/", "$1", $line);
			$line3 = urldecode($line3);
			//$wpURL[$notenumber] = preg_replace("/.*?url=([^\"]+)\&h.*/", "", $line3;
			$wpURL[$notenumber] = preg_replace("/.*?url=/", "", $line3);
			// the following line was adding by Aaron Harp to get the real URL if it's pointing to a Feedburner one
			if (strstr($wpURL[$notenumber], 'feedburner.com')) {
				$wpURL[$notenumber] = get_real_url($wpURL[$notenumber]);
			}
			if ($verbose) {
				print "Checking for comments on <a href=\"$wpURL[$notenumber]\">$wpURL[$notenumber]</a>...<br />\n";
			}
		}
		//0.26 This line had to be changed, because splitting the lines by a line feed stopped working.
		//elseif ( preg_match("/#comments\">[0-9]+ comment/", $line) ) {
		//if ( preg_match("/#comments\">[0-9]+ comment/", $line) ) {
		//0.30 change to new comment matching
		if ( preg_match('/>Comment<\/a>/', $line) ) {
			// This entry has comments. Perhaps we should follow its link!
			$getcomments[$notenumber] = "true";
			$l2 = facebook_get_url("http://facebook.com/".$url[$notenumber], $challenge);
			//James: Facebook now seems to not have line feeds, so split by sometime else
			//$l2 = explode("\n", $l2);
			//$l2 = explode('<div class="wallimage">', $l2);
			//0.35
			$l2 = explode('<div class="wall_posts"', $l2);

			foreach ($l2 as $line2) {
				//James: Facebook now seems to have more than one space after the profile link
				//if ( preg_match ("/class=\"profile_link\" >.*? wrote/", $line2) ) {
				//if ( preg_match ("/class=\"profile_link\" *>.*? wrote/", $line2) ) {
				//0.30 no longer contains profile_link
				//if ( preg_match ('/class="feed_comment_pic"/', $line2) ) {
				//0.35
				if ( preg_match("/.*<div class=\"wallcontent\".*/", $line2) ) {
					//$lines2 = explode("/td><td ", $line2);
					//0.35
					$lines2 = explode('<div class="wallcontent"', $line2);
					foreach ($lines2 as $l3) {
						//James: same as above
						//if ( preg_match ("/class=\"profile_link\" >.*? wrote/", $l3) ) {
						//if ( preg_match ("/class=\"profile_link\" *>.*? wrote/", $l3) ) {
						//0.30
						//if ( preg_match ('/class="feed_comment_pic" *\/><\/a>/', $l3) ) {
						//0.35						
						if ( preg_match ('/.*<div class=\"wallfrom\">?.*<\/div>.*/', $l3) ) {
							$l3 = str_replace("\n", "", $l3);
							//0.34 Allow for a default url for the author if you don't want to point to facebook profile
							switch($defaultURL) {
								case null:
									if (preg_match("/.*<a href=\"(.*?facebook\.com\/s\.php\?.*?id=.*?)\">.*/",$l3)) {
										$comment_author_url = preg_replace("/.*<a href=\"(.*?facebook\.com\/s\.php\?.*?id=.*?)\">.*/", '$1', $l3);
									} else {
										$comment_author_url = preg_replace("/.*<a href=\"(.*?facebook\.com\/profile\.php\?id=.*?)\">.*/", '$1', $l3);
									}
									break;
								default:
									$comment_author_url = get_option('facebook_default_url');
							}
							//if (preg_match("/.*<a href=\".*?facebook\.com\/s\.php\?.*?id=.*?\">([^<]+)<.*/",$l3)) {
							//0.35
							if (preg_match("/.*<span class=\"wallmeta\"><\/span><a href=\".*?facebook\.com\/s\.php\?.*?id=.*?\">([^<]+)<.*/",$l3)) {
								//$comment_author = preg_replace("/.*<a href=\".*?facebook\.com\/s\.php\?.*?id=.*?\">([^<]+)<.*/", '$1', $l3);
								//0.35
								$comment_author = preg_replace("/.*<span class=\"wallmeta\"><\/span><a href=\".*?facebook\.com\/s\.php\?.*?id=.*?\">([^<]+)<.*/", '$1', $l3);
							} else {
								//$comment_author = preg_replace("/.*<a href=\".*?facebook\.com\/profile\.php\?id=.*?\">([^<]+)<.*/", '$1', $l3);
								//0.34 update to ensure comment_author is always set even for the last comment on a post								
								//$comment_author = preg_replace("/.*<span class=\"wallmeta\"><\/span><a href=\"http:\/\/www.facebook\.com\/profile\.php\?id=.*?\">(.*?)<\/a>.*/", '$1', $l3);
								//0.35
								$comment_author = preg_replace("/.*<span class=\"wallmeta\"><\/span><a href=\"http:\/\/www.facebook\.com\/profile\.php\?id=.*?\">(.*?)<\/a>.*/", '$1', $l3);
							}			
							$when = preg_replace('/.*<span class="wallmeta">(.*?)<\/span>.*/', '$1', $l3);
							//Need to check this
							//0.34 update 
							$comment_content = preg_replace("/.*class=\"wall_actual_text\">(.*?)<\/div>.*/", '$1', $l3);
							
							//0.36 update
							//remove text_exposed_hide link that is used by facebook for long comments
							if(strstr($comment_content, "class=\"text_exposed_hide\"")) {
								$commentHalf1 = substr($comment_content, 0, strpos($comment_content, "<span class=\"text_exposed_hide\""));						
								$commentHalf2 = preg_replace("/.*<span class=\"text_exposed_show\">(.*?)<\/span>.*/", '$1', $comment_content);
								$comment_content = $commentHalf1 . $commentHalf2;
							} else {
								//do nothing
							}
							
							//$comment_content = preg_replace("/.*span class=\"text_exposed_show\">(.*?)<\/span>.*/", '$1', $comment_content);
							
							
							$comment_author = $comment_author . " " . get_option('facebook_add_to_author');
							$when = str_replace("at ", "", $when);
							$when = str_replace("on ", "", $when);
							$epochtime = strtotime($when);
							// Check if this comment exists already
							$addcomment = 1;
							global $wpdb;
							//$thisquery = "SELECT A.ID FROM $wpdb->posts A WHERE A.guid LIKE '$wpURL[$notenumber]'";
							//$comment_post_ID = $wpdb->get_var($thisquery);
							if ($wpURL[$notenumber] == '') {
								print "&nbsp;&nbsp;&nbsp;&nbsp;Error, wordpress URL for post not stored... ";
							} else {
								$comment_post_ID = facebook_get_postid($wpURL[$notenumber]);
							if ($comment_post_ID == 0 || $comment_post_ID == '') {
								print "&nbsp;&nbsp;&nbsp;&nbsp;Error, original comment post $comment_post_ID not found... ";
							} else {
								$continue = true;
								if ($dontimport) {
									$comment_post = get_post($comment_post_ID);
									$continue = $comment_post->comment_status != 'closed';
								}
								if ($verbose) {
									print "&nbsp;&nbsp;&nbsp;&nbsp;Comment found for post $comment_post_ID... ";
								}
								if ($continue) {
									$doescommentexist = $wpdb->get_var("SELECT count(A.comment_post_ID) FROM $wpdb->comments A WHERE A.comment_post_ID = '$comment_post_ID' AND A.comment_approved = '1' AND A.comment_author LIKE '$comment_author'");
									if ($doescommentexist) {
										// All that the above SQL tells us is that this person already has a comment for this entry in the database. However, we don't know whether they commented more than once on this particular note/entry. So, we've got a bit more work to do.
										//James 0.21 this was broken in the original, get_row only returns one row, so it wouldn't work if someone comments more than once.
										//$thesecomments = $wpdb->get_row("SELECT A.comment_content FROM $wpdb->comments A WHERE A.comment_post_ID = '$comment_post_ID' AND A.comment_approved = '1' AND A.comment_author LIKE '$comment_author'", ARRAY_N);
										$thesecomments = $wpdb->get_col("SELECT A.comment_content FROM $wpdb->comments A WHERE A.comment_post_ID = '$comment_post_ID' AND A.comment_approved = '1' AND A.comment_author LIKE '$comment_author'",0);

										foreach ($thesecomments as $thiscomment) {
											// We want to strip down the comments, both from the database and from Facebook, to the bare minimum and then compare them. So no HTML, no whitespace, no anything that isn't A-Z, 0-9, or underscore.
											$tc = urldecode($thiscomment);
											$tc = preg_replace("/<[^>]*>/", "", $tc);
											$tc = preg_replace("/\W/", "", $tc);
											$fc = urldecode($comment_content);
											$fc = preg_replace("/<[^>]*>/", "", $fc);
											$fc = preg_replace("/\W/", "", $fc);
											if (strcmp($tc, $fc) == 0) {
												// If these are the same comment, don't add it again.
												if ($verbose) {
													print "already exists in Wordpress database.";
												}
												$addcomment = 0;
											}
										}
									} 
									if ($addcomment == 1) {
										print "not in Wordpress database. ";
										if ($test_run) {
											if ($verbose) {
												print "Test run, skipping add...";
											}
										} else {
											if ($verbose) {
												print "Adding to Wordpress...";
											}
											// Insert the comment
											if (get_option('facebook_fake_author_email')) {
												$comment_author_email = get_option('facebook_fake_author_email');
											} else {
												$comment_author_email = $email;
											}
											$comment_date = date("Y-m-d H:i:s", $epochtime);
											$comment_date_gmt = gmdate("Y-m-d H:i:s", $epochtime);
											$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_date');
											$comment_ID = facebook_new_comment ($commentdata);
											if ($verbose) {
												print "OK!";
											}
										}
									}  // Done adding the comment now.
								} else {
									if ($verbose) {
										print "Comments closed for this post, skipping.";
									}
								}
							}
							}
							if ($verbose) {
								print "<br />\n";
							}
						}  // end "if line contains profile_link"
					} // next table cell
				} // end "if line contains profile_link"
			} // next line of file
		} // Done with this note
	} // Next line of file
	
	if ($allpages) {
		if ( preg_match("/notes.php?.*\">Next/", $notespage) ) {
			if ($verbose) {
				print "<br />Getting next page...<br />\n";
			}
			import_facebook_page($notesurl, $verbose, $allpages, $start+10);
		}
	}
	
}

function import_facebook_comments() {
	// Start buffering	
	ob_start();
	import_facebook_page(get_option('facebook_notesURL'), get_option('facebook_plugin_verbose'), get_option('facebook_all_pages'));
	// Turn off buffering
	ob_end_flush();
	return;
}

function facebook_activate() {
	// Add options
	add_option('facebook_add_to_author', " (via Facebook)", "Text to append to the comment author's name");
	//0.34 add ability to add default author url
	add_option('facebook_default_url', " (via Facebook)", "Link to use instead of the facebook profile link");
	add_option('facebook_auto_freq', "86400", "How often to automatically check Facebook to import comments");
	// All other options are empty by default and so we'll add them when they have data, later.
	
	// Schedule
	
	if (!wp_next_scheduled('import_facebook_comments_hook')) {
		$freq = get_option('facebook_auto_freq');
		if ($freq == 86400) {
			// Daily
			wp_schedule_event( time(), 'daily', 'import_facebook_comments_hook' );
		} elseif ($freq == 3600) {
			// Hourly
			wp_schedule_event( time(), 'hourly', 'import_facebook_comments_hook' );
		} else {
			// Turn off automatic checking.
		}
	}
	
}

function get_real_url($url) {
  $ch = curl_init();    // initialize curl handle
  curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
  curl_setopt($ch, CURLOPT_NOBODY, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15); // times out after 15s
  if (!curl_exec($ch)) { return false; }
  return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
}

function get_facebook_auth() {
	add_submenu_page("options-general.php", "Facebook Comments", "Facebook Comments", 5, "facebooknotes.php", facebook_options_subpanel);
}

function facebook_options_subpanel() {
	if (! get_option('facebook_add_to_author')) {
		
	} 
	if (isset($_POST['manual_run']) || isset($_POST['test_run'])) {
		update_option('facebook_plugin_verbose', '1');
		print '<div class="updated"><p><strong>';
		print "Manually importing comments... <br /><br />\n";
		import_facebook_comments();
		print '</strong></p></div>';
		update_option('facebook_plugin_verbose', '');
	}
	if (isset($_POST['info_update'])) {?>
		<div class="updated"><p><strong>
		<?php 
		update_option('facebook_email', $_POST['email']);
		update_option('facebook_password', $_POST['pass']);
		update_option('facebook_add_to_author', $_POST['author']);
		update_option('facebook_default_url', $_POST['authorurl']);
		update_option('facebook_filter_comments', $_POST['filter']);
		update_option('facebook_fake_author_email', $_POST['fakemail']);
		update_option('facebook_notesURL', $_POST['notesURL']);
		update_option('facebook_all_pages', $_POST['allpages']);
		update_option('facebook_dont_notify', $_POST['dontnotify']);
		update_option('facebook_dont_import', $_POST['dontimport']);
		// Scheduling
		$freq = $_POST['freq'];
		if (get_option('facebook_auto_freq') !== $freq) {
			// If the interval has changed:
			update_option('facebook_auto_freq', $freq);
			// Remove whatever the old schedule was, and add a new one (if appropriate)
			wp_clear_scheduled_hook('import_facebook_comments_hook');
			if ($freq == 86400) {
				// Daily
				wp_schedule_event( time(), 'daily', 'import_facebook_comments_hook' );
			} elseif ($freq == 3600) {
				// Hourly
				wp_schedule_event( time(), 'hourly', 'import_facebook_comments_hook' );
			} else {
				// Turn off automatic checking.
			}
		}
		
		// The idea here is that we can add a button to manually run the script and show the results as text -- i.e. verbose -- or we can have the default running of the script (cron'd, probably) without printing anything.
		update_option('facebook_plugin_verbose', '');
		print "Options updated.";
    		?>
		</strong>
		</p></div><?php		} ?>
<div class=wrap>
  <form method="post">
    <h2>Facebook Comments</h2>
      <fieldset name="authset">
      <h3>Facebook Login</h3>
Before this plugin can start working, you have to authorize it to access your Facebook account: <br />
	<fieldset name="email">
	<legend>  <?php _e('Email', 'Localization name') ?>: </legend>
	<input type="text" name="email" id="email" size="20" value="<?php echo get_option('facebook_email') ?>" /> &nbsp;&nbsp;&nbsp; 
	</fieldset>
	<fieldset name="password">
	<legend>  <?php _e('Password', 'Localization name') ?>: </legend>
	<input type="password" name="pass" id="pass" size="20" value="<?php echo get_option('facebook_password') ?>" />
	</fieldset>
      </fieldset>
      	<fieldset name="notesURL">
	<legend><?php _e('Notes <acronym title="Uniform Resource Locator">URL</acronym>: &nbsp;&nbsp;<small>Example: http://network.facebook.com/notes.php?id=your_id_number</small>', 'Localization name') ?></legend>
	<input type="text" name="notesURL" id="notesURL" size="40" value="<?php echo get_option('facebook_notesURL') ?>" />
	</fieldset>
<br />

	<fieldset name="options">
	<h3>General Options</h3>
	<fieldset name="freq">
	<legend><?php _e("Automatically import comments from Facebook: ", 'Localization name') ?></legend>
	<select name="freq">
	<option value="86400"<?php if (get_option('facebook_auto_freq') == 86400) { print " selected"; } ?>>Daily
	<option value="3600"<?php if (get_option('facebook_auto_freq') == 3600) { print " selected"; } ?>>Hourly
	<option value="0"<?php if (get_option('facebook_auto_freq') != 86400 && get_option('facebook_auto_freq') != 3600) { print " selected"; } ?>>Never
	</select>
	</fieldset>
	<fieldset name="authortext">
	<legend>  
	<?php _e("Add text to comment author's name", 'Localization name') ?>: </legend>
	<input type="text" name="author" id="author" size="20" value="<?php echo get_option('facebook_add_to_author') ?>" />
	</fieldset>
	<fieldset name="authorurl">
	<legend>  
	<?php _e("Add default url to comment", 'Localization name') ?>: </legend>
	<input type="text" name="authorurl" id="authorurl" size="30" value="<?php echo get_option('facebook_default_url') ?>" />
	</fieldset>
	<fieldset name="fakemail">
	<legend><?php _e("Email address to use as commenter's email", 'Localization name') ?>: </legend>
	<input type="text" name="fakemail" id="fakemail" size="20" value="<?php echo get_option('facebook_fake_author_email') ?>" />
	</fieldset>
	<fieldset name="filter">
	<input type="checkbox" name="filter" id="filter" <?php echo (get_option('facebook_filter_comments')==true?"checked=\"checked\"":"") ?> /> <?php _e('Run imported comments through filters. <br /><small>(This can interfere with other plugins such as spam filters, so unless you need your Facebook comments filtered by another plugin for something other than spam, it\'s best to leave this option turned off.) <a href="http://www.adamhill.ca/?page_id=179#options">More details...</a></small>', 'Localization name') ?>
	</fieldset>
	<fieldset name="allpages">
	<input type="checkbox" name="allpages" id="allpages" <?php echo (get_option('facebook_all_pages')==true?"checked=\"checked\"":"") ?> /> <?php _e('Import for all pages of Facebook notes.', 'Localization name') ?>
	</fieldset>
	<fieldset name="dontnotify">
	<input type="checkbox" name="dontnotify" id="dontnotify" <?php echo (get_option('facebook_dont_notify')==true?"checked=\"checked\"":"") ?> /> <?php _e("Don't notify admins for comments from Facebook.", 'Localization name') ?>
	</fieldset>
	<fieldset name="dontimport">
	<input type="checkbox" name="dontimport" id="dontimport" <?php echo (get_option('facebook_dont_import')==true?"checked=\"checked\"":"") ?> /> <?php _e("Don't import comments for posts that are closed.", 'Localization name') ?>
	</fieldset>
<div class="submit">
	<input type="submit" name="manual_run" value="<?php _e('Import Comments Manually', 'Localization name') ?> »" /> &nbsp;&nbsp;
	<input type="submit" name="info_update" value="<?php _e('Update options', 'Localization name') ?> »" /> &nbsp;&nbsp;
	<input type="submit" name="test_run" value="<?php _e('Test Import', 'Localization name') ?> »" />
	</div>
  </form>
 </div><?php
}

add_action('activate_facebooknotes.php', 'facebook_activate');
add_action('admin_menu', 'get_facebook_auth');
add_action('import_facebook_comments_hook', 'import_facebook_comments');

?>