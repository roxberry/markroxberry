<?php
/*
	Plugin Name: WP Google Latitude
	Plugin URI: http://www.theitjuggler.com/2009/05/05/wordpress-plugin-google-latitude
	Description: Plugin to display Google Latitude badge. Now working in Internet Explorer, Safari, Firefox and Chrome.
	Version: 1.03
	Author: Steve "The IT Juggler" Ollis
	Author URI: http://www.theitjuggler.com/

	Copyright 2009, Steve Ollis

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function widget_wp_google_latitude($args) {

	// First we grab the Wordpress theme args, which we
	// use to display the widget
	extract($args);

	$options = get_option("wp_google_latitude_widget");
	if (!is_array( $options )) {
		$options['title'] = 'Where am I?';
		$options['width'] = '180';
		$options['height'] = '300';
		$options['maptype'] = 'road';
		$options['zoom'] = 'automatic';
		$options['googleid'] = '9073576785905927589';
	}

	// Display the widget!
	echo "<!--WP Google Latitude Start -->\n";
	echo $before_title;
	echo $options['title'];
	echo $after_title;
	$params = '?user='.$options['googleid'].'&type=iframe&maptype='.$options['maptype'];
	if ($options['zoom'] != '0') {
	  $params = $params."&z=".$options['zoom'];
	}
	echo "<!-- Google Public Location Badge -->\n";
	echo "<iframe src=\"http://www.google.com/latitude/apps/badge/api".$params."\" width=\"".$options['width']."\" height=\"".$options['height']."\" frameborder=\"0\" >\n";
	echo "</iframe>\n";
    echo "<!-- To disable location sharing, you *must* visit http://www.google.com/latitude/apps/badge and disable the Google Public Location badge. Removing this code snippet is not enough! -->\n";
	echo "<!--WP Google Latitude End -->\n";
}


//initially set the options
function control_wp_google_latitude() {
	$options = get_option("wp_google_latitude_widget");
	if (!is_array( $options )) {
		$options['title'] = 'Where am I?';
		$options['width'] = '180';
		$options['height'] = '300';
		$options['maptype'] = 'road';
		$options['zoom'] = 'automatic';
		$options['googleid'] = '9073576785905927589';
	}

	// If the user has submitted their options, grab them here.
	if ($_POST['WPGL-Submit']) {
		$options['title'] = htmlspecialchars($_POST['WPGL-Title']);
		$options['height'] = htmlspecialchars($_POST['WPGL-Height']);
		$options['width'] = htmlspecialchars($_POST['WPGL-Width']);
		$options['maptype'] = htmlspecialchars($_POST['WPGL-MapType']);
		$options['zoom'] = htmlspecialchars($_POST['WPGL-Zoom']);
		$options['googleid'] = htmlspecialchars($_POST['WPGL-GoogleID']);
		// And we also update the options in the Wordpress Database
		update_option("wp_google_latitude_widget", $options);
	}

	// Display the options for the widget here.
	?>
	<p>
		<label for="WPGL-Title">Title above widget:</label><br>
		<input type="text"
		  id="WPGL-Title"
		  name="WPGL-Title"
		  value="<?php echo $options['title'];?>" /><br>

		<label for="WPGL-Height">Height of widget:</label><br>
		<input type="text"
		  id="WPGL-Height"
		  name="WPGL-Height"
		  value="<?php echo $options['height'];?>" /><br>

		<label for="WPGL-Width">Width of widget:</label><br>
		<input type="text"
		  id="WPGL-Width"
		  name="WPGL-Width"
		  value="<?php echo $options['width'];?>" /><br>

		<label for="WPGL-MapType">Type of Map:</label><br>
		<select
		  id="WPGL-MapType"
		  name="WPGL-MapType">
		    <option <?php if ($options['maptype'] == "roadmap") echo "SELECTED ";?> value="roadmap">Road</option>
		    <option <?php if ($options['maptype'] == "terrain") echo "SELECTED ";?> value="terrain">Terrain</option>
		    <option <?php if ($options['maptype'] == "hybrid") echo "SELECTED ";?> value="hybrid">Hybrid</option>
		    <option <?php if ($options['maptype'] == "satellite") echo "SELECTED ";?> value="satellite">Satellite</option>
		  </select><br>

		<label for="WPGL-Zoom">Zoomlevel of Map:</label><br>
		<select
		  id="WPGL-Zoom"
		  name="WPGL-Zoom">
 		    <option <?php if ($options['zoom'] == 0) echo "SELECTED ";?> value="0">Automatic</option>
		    <option <?php if ($options['zoom'] == 10) echo "SELECTED ";?>value="10">City</option>
		    <option <?php if ($options['zoom'] == 7) echo "SELECTED ";?>value="7">Region</option>
		    <option <?php if ($options['zoom'] == 5) echo "SELECTED ";?>value="5">Country</option>
		    <option <?php if ($options['zoom'] == 3) echo "SELECTED ";?>value="3">Continent</option>
		</select><br>

		<label for="WPGL-GoogleID">Your Google ID:</label>&nbsp;&nbsp;<a href="http://www.google.com/latitude/apps/badge" target="_blank">Get your Latitude id</a> first.<br>
		<input type="text"
		  id="WPGL-GoogleID"
		  name="WPGL-GoogleID"
		  value="<?php echo $options['googleid'];?>" /><br>
		<input type="hidden"
		  id="WPGL-Submit"
		  name="WPGL-Submit"
		  value="1" />
	</p>
	<?php
}

//uninstall all options
function wp_google_latitude_uninstall () {
	delete_option('wp_google_latitude_widget');
}


// widget and control
function wp_google_latitude_widget_init() {
	register_sidebar_widget("WP Google Latitude","widget_wp_google_latitude");
	register_widget_control("WP Google Latitude","control_wp_google_latitude");
}

add_action("plugins_loaded", "wp_google_latitude_widget_init");

?>
