<?php
/*
Plugin Name: SubPage Slider
Plugin URI: http://www.cameronpreston.com/projects/plugins
Description: A plugin widget that does a slideshow of subpages of any given page
Version: 0.2
Author: Cameron Preston
Author URI: http://www.cameronpreston.com
*/

//Check to make sure there are no crashes with other classes with the same name
if (!class_exists('subpage_slider'))
{
	class subpage_slider extends WP_Widget {
		
		function subpage_slider() { // constructor
			$widget_ops = array('classname' => 'subpage_slider', 'description' => __( "Widget sliding through subpages for a specific page") );
		    $this->WP_Widget('subpage_slider', __('Subpage Slider'), $widget_ops);
		}
		
		function widget($args, $instance) { //outputs widget
			global $wpdb;
			extract($args);
			$char_limit = $options['char_limit'];
			$page_num = isset($options['page_num'])?$instance['page_num']:1;
			$ajax_refresh = isset($options['ajax_refresh'])?$instance['ajax_refresh']:1;
			//print "widget page_num:".$ajax_refresh."<br>";
			//Check if array is set
			if (!array_key_exists('page_id1', $instance) || $instance['page_id1'] == '') {
				echo $before_widget . 'You have not selected a page for this widget yet.' . $after_widget;
			} else {
				global $subpageslider_instances;
				$subpageslider_instances++;
				$parms = "ajax_refresh=$ajax_refresh&page_num=$page_num";
				if ($ajax_refresh == 1) {
					if($subslide_page = subpageslider_page($parms, $instance)) {
						global $subpageslider_instances;
						$subpageslider_instances++;
						extract($args);
						echo $before_widget;
						//print "before_widget!";
						if($title) echo $before_title . $title . $after_title . "\n";
						echo $subslide_page;
						//print "after_widget";
						echo $after_widget;
					}
				} //end if
	    	} //end else
		}
		
		function update($new_instance, $old_instance) { //updates widget; 
			$new_instance['page_id1'] = $_POST['page-dropdown1'];
			$new_instance['page_id2'] = $_POST['page-dropdown2'];
			$new_instance['page_id3'] = $_POST['page-dropdown3'];
			$new_instance['page_id4'] = $_POST['page-dropdown4'];
			$new_instance['page_id5'] = $_POST['page-dropdown5'];
			$new_instance['page_id6'] = $_POST['page-dropdown6'];
			$new_instance['page_id7'] = $_POST['page-dropdown7'];
			$new_instance['page_id8'] = $_POST['page-dropdown8'];
			$new_instance['page_id9'] = $_POST['page-dropdown9'];
			$new_instance['page_id10'] = $_POST['page-dropdown10'];
			$p1 = get_page($instance['page_id1']);
			$p2 = get_page($instance['page_id2']);
			$p3 = get_page($instance['page_id3']);
			$p4 = get_page($instance['page_id4']);
			$p5 = get_page($instance['page_id5']);
			$p6 = get_page($instance['page_id6']);
			$p3 = get_page($instance['page_id7']);
			$p4 = get_page($instance['page_id8']);
			$p5 = get_page($instance['page_id9']);
			$p6 = get_page($instance['page_id10']);
			$new_instance['title'] = $p1->post_title;
			$new_instance['page_num'] = $_POST['page_num'];
			$instance['pageTitle'] = strip_tags(stripslashes($new_instance['pageTitle']));
			$instance['linkFirst'] = strip_tags(stripslashes($new_instance['linkFirst']));
			$instance['linkNext'] = strip_tags(stripslashes($new_instance['linkNext']));

			return $new_instance;
		}	
		
			//$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'pageTitle'=>'Change the World', 'page_id1' => '', 'page_id2' => '', 'page_id3' => '', 'page_id4' => '', 'page_id5' => '', 'page_id6' => '' ) );
		function form($instance) { //Creates form 
			$defaults = array( 'title' => '', 'pageTitle'=>'Parent Page', 'page_id1'=>'', 'linkFirst'=>'Take the tour', 'linkNext'=>'Next Page');
			$instance = wp_parse_args( (array) $instance, $defaults );
			$title = strip_tags($instance['title']);
			$pageTitle = strip_tags($instance['pageTitle']);
			$linkFirst = strip_tags($instance['linkFirst']);
			$linkNext = strip_tags($instance['linkNext']);
			$page_id1 = $instance['page_id1'];
			$page_id2 = $instance['page_id2'];
			$page_id3 = $instance['page_id3'];
			$page_id4 = $instance['page_id4'];
			$page_id5 = $instance['page_id5'];
			$page_id6 = $instance['page_id6'];
			$page_id7 = $instance['page_id7'];
			$page_id8 = $instance['page_id8'];
			$page_id9 = $instance['page_id9'];
			$page_id10 = $instance['page_id10'];
			$p1 = get_page($page_id1);
			$p2 = get_page($page_id2);
			$p3 = get_page($page_id3);
			$p4 = get_page($page_id4);
			$p5 = get_page($page_id5);
			$p6 = get_page($page_id6);
			$p7 = get_page($page_id7);
			$p8 = get_page($page_id8);
			$p9 = get_page($page_id9);
			$p10 = get_page($page_id10);
			$page_num = $instance['page_num'];

			# Output the options
			echo '<p style="text-align:right;float:right;"><label for="' . $this->get_field_name('pageTitle') . '">' . __('Page Title:') . ' <input style="width: 150px;" id="' . $this->get_field_id('pageTitle') . '" name="' . $this->get_field_name('pageTitle') . '" type="text" value="' . $pageTitle . '" /></label></p>';
			echo '<p style="text-align:right;float:right;"><label for="' . $this->get_field_name('linkFirst') . '">' . __('First Link:') . ' <input style="width: 150px;" id="' . $this->get_field_id('linkFirst') . '" name="' . $this->get_field_name('linkFirst') . '" type="text" value="' . $linkFirst . '" /></label></p>';
			$pTitle = get_page_by_title($pageTitle);
			?>
			<?php
			for ($i = 1; $i <= 10; $i++ ) //SET THE AMOUNT OF PAGE CHOICES
			{
			$page_id = $instance["page_id".$i];
			print "<p style='text-align:right;float:right;'>".$i."<select name='page-dropdown$i'>\n";
			print "<option value=null>\n";
			print attribute_escape(__('Select page to display')) ."</option> \n";
			  $pages = get_pages('child_of=' . $pTitle->ID . '&parent=' . $pTitle->ID); 
			  foreach ($pages as $pagg) {
			  	$option = ':<option value="' . $pagg->ID . '"';
			  	if ($pagg->ID == $page_id) {
				  	$option .= ' selected="selected"';
			  	}
				$option .= '>' . $pagg->post_title;
				$option .= '</option>';
				echo $option;	
			  }
			 ?>
			</select></p><br />
			<?php }?>
			
			<input type="text" id="title" name="title" style="visibility: hidden;" value="<?php echo $pageTitle; ?>" />
			<input type="text" id="page_num" name="title" style="visibility: hidden;" value="1" />
			
			<?php
		}
		
		function add_widget() { //register the widget
			register_widget('subpage_slider');
		}
	} // end class subpage_slider
	
}

function subpageslider_page($args, $instance)
{
	global $subpageslider_instances;

	if (!$subpageslider_instances) { $numinstance = $subpageslider_instances = 1; }
	else {
		$subpageslider_instances++;
		}

	$key_value = explode('&', $args);
	$options = array();  // $options Array is created to hold all the args
	foreach($key_value as $value) {
		$x = explode('=', $value);
		$options[$x[0]] = $x[1]; // $options['key'] = 'value';
	}
	$options_default = array(
		'ajax_refresh' => 1,
		'page_num' => 1,
		'page_end' => false
	);

	$options = array_merge($options_default, $options);
	$numpage = $options['page_num'];
	$subslide_page = subslide_get_page($instance,$numpage);
	if(!$subslide_page)
		return;
	$link_first = $instance['linkFirst'];
	$page = $instance['page_id'];
	if ($page == null) {$page = $instance['page_id1'];} // No AJAX refreshes yet

	$p = get_page($page); // Retreives page array and outputs it to p
	$disp = "<p><q>". $before_title . $p->post_title . $after_title ."</q>";
	$disp .= wpautop(wptexturize($p->post_content));


		if($options['ajax_refresh'] == 1) {
			$disp .= "<script type=\"text/javascript\">\n<!--\ndocument.write(\"";
			$disp .= '<p class=\"subpageslider_nextquote\" id=\"subpageslider_nextquote-'.$numpage.'\"><a class=\"subpageslider_refresh\" style=\"cursor:pointer\" onclick=\"pagesliders_refresh('.$numpage.', '.$instance['page_id1'].', '.$instance['page_id2'].', '.$instance['page_id3'].', '.$instance['page_id4'].', '.$instance['page_id5'].', '.$instance['page_id6'].', '.$instance['page_id7'].', '.$instance['page_id8'].', '.$instance['page_id9'].', '.$instance['page_id10'].', 0);\">'.__($link_first).' &raquo;<\/a><\/p>';
			$disp .= "\")\n//-->\n</script>\n";
		}
		if ($options['ajax_refresh'] == 2 && $options['page_end'] == true ) {
			$disp .= "<p class=\"subpageslider_nextquote\" id=\"subpageslider_nextquote-".$_REQUEST['refresh']."\"><a class=\"subpageslider_refresh\" style=\"cursor:pointer\" onclick=\"pagesliders_refresh('1', ".$instance['page_id1'].", ".$instance['page_id2'].", ".$instance['page_id3'].", ".$instance['page_id4'].", ".$instance['page_id5'].", ".$instance['page_id6'].", ".$instance['page_id7'].", ".$instance['page_id8'].", ".$instance['page_id9'].", ".$instance['page_id10'].",".$options['page_end'].",".$_REQUEST['refresh'].");\">&laquo;".__(' Back Home')." </a></p>";
			$disp = "<div id=\"subpageslider_randomquote-".$numpage."\" class=\"subpageslider_randomquote\">{$disp}</div>";
			echo $disp;
			return;
		}
		if ($options['ajax_refresh'] == 2) {
			$disp .= "<p class=\"subpageslider_nextquote\" id=\"subpageslider_nextquote-".$_REQUEST['refresh']."\"><a class=\"subpageslider_refresh\" style=\"cursor:pointer\" onclick=\"pagesliders_refresh(".$numpage.", ".$instance['page_id1'].", ".$instance['page_id2'].", ".$instance['page_id3'].", ".$instance['page_id4'].", ".$instance['page_id5'].", ".$instance['page_id6'].", ".$instance['page_id7'].", ".$instance['page_id8'].", ".$instance['page_id9'].", ".$instance['page_id10'].",0,".$_REQUEST['refresh'].");\">".__('Next Page')." &raquo;</a></p>";
			$disp = "<div id=\"subpageslider_randomquote-".$numpage."\" class=\"subpageslider_randomquote\">{$disp}</div>";
			echo $disp;
			return;
		}
		$disp = "<div id=\"subpageslider_randomquote-".$numpage."\" class=\"subpageslider_randomquote\">{$disp}</div>";

		$subpageslider_instances++;
		return $disp;
		return;
}
function subslide_get_page($instance,$numpage) { //currently not used
		$ppage_id = "page_id" . $numpage; // Adds the current page number to the page_id
		
		$p = get_page($instance[$ppage_id]); // Retreives page array and outputs it to p		
		return "test $numpage";
}
function subpageslider_js_head()
{
	if ( !defined('WP_PLUGIN_URL') )
		$wp_plugin_url = get_bloginfo( 'url' )."/wp-content/plugins";
	else
		$wp_plugin_url = WP_PLUGIN_URL;
	$requrl = $wp_plugin_url . "/subpage-slider/subpage-slider-ajax.php";
	$nextpage =  __('Next Page');
	$loading = __('Loading...');
	$error = __('Error getting page');
	$numpage = '3';
	?>

	<script type="text/javascript" src="<?php echo $wp_plugin_url; ?>/subpage-slider/subpage-slider.js"></script>
	<script type="text/javascript">
	  pagesliders_init(<?php echo "'{$requrl}', '{$nextpage}', '{$loading}', '{$error}'"; ?>);
	</script>
	<?php
}
add_action('wp_head', 'subpageslider_js_head' );


function subpageslider_css_head() 
{

	if ( !defined('WP_PLUGIN_URL') )
		$wp_plugin_url = get_bloginfo( 'url' )."/wp-content/plugins";
	else
		$wp_plugin_url = WP_PLUGIN_URL;
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo $wp_plugin_url; ?>/subpage-slider/subpage-slider.css"/>
	<?php
	
}
add_action('wp_head', 'subpageslider_css_head' );


//Instantiates class
if (class_exists('subpage_slider')) {
	$subpageslider_widget = new subpage_slider();
}

//Actions and Filters
if (isset($subpageslider_widget)) {
	//Actions
	add_action('widgets_init', array(&$subpageslider_widget, 'add_widget')); //register widget
	
	//Filters
	
}
?>