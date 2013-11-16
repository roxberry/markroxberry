<?php
/*
Plugin Name: Social Links Sidebar
Plugin URI: http://www.commareus.com/
Description: Social Links Sidebar is a sidebar widget that displays icon links to your profile pages on other social networking sites. Forked from <a href="http://abisso.org/projects/about-me/">About Me</a>
Author: Commareus
Version: 1.0
Author URI: http://www.commareus.com

/*  Social Links Copyright 2008  Kareem Sultan  (email : kareemsultan@gmail.com) */
/*
   (c) 2009 Alessio Caiazza
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

   http://www.gnu.org/licenses/gpl.txt

*/

//call install function upon activation
register_activation_hook(__FILE__,'social_links_sidebar_install');

//TO DO use these definitions instead
define('SOCIAL_LINKS_VERSION', '1.0.5');
define('SOCIAL_LINKS_DB_VERSION', '1.1');

define('KEY_SITE_ID',0);
define('KEY_IMAGE',1);
define('KEY_URL_TEMPLATE',2);
define('KEY_INSTRUCTION',3);
define('KEY_DISPLAY_NAME',4);

//$sl_db_version = "1.0";
$plugindir = get_bloginfo('wpurl').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));

$definitions = array(
array(0,'facebook.png','%userid%','Enter your complete Facebook profile URL','Facebook'),
array(1,'myspace.png','%userid%','Enter your complete MySpace URL.','MySpace'),
array(2,'linkedin.png','%userid%','Enter your complete LinkedIn URL.','LinkedIn'),
array(3,'picasa.png','http://picasaweb.google.com/%userid%','Enter your Picasa(Google) username.','Picasa Web Album'),
array(4,'flickr.png','http://flickr.com/photos/%userid%','Enter your flickr username','Flickr'),
array(5,'youtube.png','http://www.youtube.com/%userid%','Enter your YouTube username','YouTube'),
array(6,'twitter.png','http://twitter.com/%userid%','Enter your Twitter username','Twitter'),
array(7,'pownce.png','http://pownce.com/%userid%','Enter your Pownce username','Pownce'),
array(8,'plurk.png','http://www.plurk.com/user/%userid%','Enter your Plurk username','Plurk'),
array(9,'digg.png','http://www.digg.com/users/%userid%','Enter your Digg username.','Digg'),
array(10,'delicious.png','http://delicious.com/%userid%','Enter your Delicious username','Delicious'),
array(11,'blogmarks.png','http://blogmarks.net/user/%userid%','Enter your BlogMarks username.','BlogMarks'),
array(12,'stumbleupon.png','http://%userid%.stumbleupon.com','Enter your Stumble Upon username','Stumble Upon'),
array(13,'lastfm.png','http://www.last.fm/user/%userid%','Enter your Last.fm username','Last.fm'),
array(14,'amazon.png','%userid%','Enter your complete Amazon Wishlist URL','Amazon Wishlist'),
array(15,'blog.png','%userid%','Enter the complete blog URL.','Blog'),
array(16,'jeqq.png','http://www.jeqq.com/user/view/profile/%userid%','Enter your Jeqq username','Jeqq'),
array(17,'dapx.png','%userid%','Enter your complete Dapx URL.','Dapx'),
array(18,'xing.jpg','%userid%','Enter your complete Xing URL.','Xing'),
array(19,'sixent.png','http://%userid%.sixent.com/','Enter your Sixent username','Sixent'),
array(20,'technorati.jpg','http://technorati.com/people/technorati/%userid%/','Enter your Technorati username.','Technorati'),
array(21,'friendfeed.png','http://friendfeed.com/%userid%','Enter your FriendFeed username.','FriendFeed'),
array(22,'identica.png','http://identi.ca/%userid%','Enter your Identi.ca username.','Identi.ca'),
array(23,'bitbucket.png','http://bitbucket.org/%userid%','Enter your Bitbucket username.','Bitbucket'),
array(24,'github.png','http://github.com/%userid%','Enter your Github username.','Github'),
array(25,'hellotxt.gif','http://hellotxt.com/user/%userid%','Enter your Hellotxt username.','Hellotxt'),
array(26,'hyves.png','http://%userid%.hyves.nl','Enter your Hyves.nl username.','Hyves.nl'),
array(27,'disqus.png','http://disqus.com/people/%userid%','Enter your Disqus username.','Disqus'),
array(28,'redgage.png','http://www.redgage.com/%userid%','Enter your RedGage username.','RedGage'),
array(29,'goodreads.png','http://goodreads.com/%userid%','Enter your GoodReads username','GoodReads'),
array(30,'qype.png','http://www.qype.com/people/%userid%','Enter your Qype username.','Qype'),
array(31,'orkut.png','http://www.orkut.com/Main#Profile.aspx?uid=%userid%','Enter your Orkut numeric uid.','Orkut'),
array(32,'google.png','http://www.google.com/profiles/%userid%','Enter your Google username (without @google.com).','Google Profile'),
array(33,'googlereader.png','http://www.google.com/reader/shared/%userid%','Enter your Google Reader shared number.','Google Reader'),
array(34,'ebay.png','http://myworld.ebay.com/%userid%','Enter your Ebay username.','Ebay')
);

//comparison based on socialnetwok name
function compare_social_sidebar($s1, $s2) {
   return strcmp($s1[KEY_DISPLAY_NAME], $s2[KEY_DISPLAY_NAME]);
}

//Sorts socialnetworks in alphabetical order
uasort($definitions, "compare_social_sidebar");


function social_links_sidebar_wrapper(){

   // This only works if the widget api is installed
   if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
      return; // ...and if not, exit gracefully from the script.

   //WPD_print('Filename: '.__FILE__);
   //WPD_print('DB version: '.get_option( "SOCIAL_LINKS_DB_VERSION" ));
   // Displays the icons in the sidebar
   function widget_social_links_sidebar($args) {
      global $definitions;
      extract($args);

      $options = get_option('widget_social_links_sidebar');
      $title = empty($options['title']) ? 'Social Links Sidebar' : $options['title'];
      $width =  empty($options['width']) ? 20 : $options['width'];

      echo $before_widget;
      echo $before_title . $title . $after_title ;

      echo '<!-- Social Links Sidebar Version: '. SOCIAL_LINKS_VERSION .' -->';
      echo "<div id='socialLinksSidebarContainer' style='width:$width"."px;'>";
      echo generateSocialLinksSidebarInnerHTML();
      echo '</div>';
      echo $after_widget;

   }

   //Config Panel
   function widget_social_links_sidebar_control() {
      global $definitions;
      $options = get_option('widget_social_links_sidebar');

      if ( $_POST['social-links-submit'] ) {
         // Clean up control form submission options
         $newoptions['title'] = strip_tags(stripslashes($_POST['social-links-title']));
         $newoptions['width'] = strip_tags(stripslashes($_POST['social-links-width']));


         if ( $options != $newoptions ) {
            $options = $newoptions;
            update_option('widget_social_links_sidebar', $options);
         }
      }

      $title = empty($options['title']) ? 'Social Links Sidebar' : $options['title'];
      $width = empty($options['width']) ? 100 : $options['width'];

      ?>

      <table>
         <tr><td>
            <label for="social-links-title">Widget title: <input type="text" id="social-links-title" name="social-links-title" value="<?php echo $title; ?>" /></label>
         </td></tr>
         <tr><td>
            <label for="social-links-width">Width: <input type="text" id="social-links-width" name="social-links-width" size="8" value="<?php echo $width; ?>" /> pixels</label>
         </td></tr>
      </table>
      <input type="hidden" name="social-links-submit" id="social-links-submit" value="1" />

      <?php
   }//End of widget_social_links_sidebar_control


   function wp_ajax_social_links_sidebar_add_network(){
      // read submitted information
      global $definitions;
   
      $siteID = $_POST['siteID'];
      $data = $_POST['value'];
      $messageId = $_POST['responseDiv'];
   
      $result = insertNetworkNow($siteID,$data);
      if($result == 1)
         $result = 'Link added.';
      else
         $result = 'There was a problem adding the link. Refresh the page and try again.';
   
      $innerHTML = generateSocialLinksSidebarPreviewInnerHTML('');
      die('
         $("message").innerHTML = "'.$result.'";
         $("message").className="updated fade";
         $("message").style.visibility = "visible";
         document.getElementById("displayDiv").innerHTML = "'.$innerHTML.'";
         createSortables();
      ');
   
      //Add this line to the above javascript to show the complete table.
      //document.getElementById("editDiv").innerHTML = "' . generateSocialLinksEditInnerHTML(). '";
   }

//TODO: Implement the add ajax process to send data and let the javascript add child elements
//This is to avoid using innerHTML replacement and will then allow for more advanced client side effects
/*	function wp_ajax_social_links_add_network_send_data(){
   // read submitted information
   global $definitions;

   $selectedIndex = $_POST['networkIndex'];
   $data = $_POST['value'];

   $result = insertNetworkNow($selectedIndex,$data);
   $data = generateSocialLinksData();
   //$result = 'fake insert';
   die("$result
      $('message').innerHTML = 'Database result is $result.';
   $('message').class='updated fade';
   $('message').style.visibility = 'visible';
   updateSocialLinks($data);
   ");

}
*/
   function wp_ajax_social_links_sidebar_delete_network(){
      global $wpdb;
      global $definitions;
   
      $linkId = $_POST['linkId'];
      //WPD_print('deleting linkID='. $linkId);
      $table_name = $wpdb->prefix . "social_links_sidebar";
      $sql = 'delete from ' .  $table_name . ' where id='.$linkId;
      $result = $wpdb->query($wpdb->prepare($sql));
   
      if($result == 1)
         $result = 'Removed link.';
      else
         $result = 'There was a problem deleting the link. Refresh the page and try again.'.$sql;
      //WPD_print($result);
      die('
         $("message").innerHTML = "'.$result.'";
         $("message").className="updated fade";
         $("message").style.visibility = "visible";
      ');
   }



   function insertNetworkNow($id,$value){
   
   
      //WPD_print('Inserting new network');
      global $wpdb;
   
      //WPD_print('networkID='.$id.' data='.$value);
      $table_name = $wpdb->prefix . "social_links_sidebar";
      $sql = 'Insert into ' .  $table_name . ' (network_id,user_info,sort_order) VALUES ("'.$id.'","'.$value.'",1000)';
      $result = $wpdb->query($wpdb->prepare($sql));
      //WPD_print($sql);
      return $result;
   }
   
   function getSocialLinksSidebar(){
      global $wpdb;
      $table_name = $wpdb->prefix . "social_links_sidebar";
      $sql = 'Select * from ' .  $table_name . ' order by sort_order';
      $results = $wpdb->get_results($sql,ARRAY_N);
      ////WPD_print("Select networks results: ".$results);
      return $results;
   
   }
   
   function generateSocialLinksSidebarInnerHTML(){
      global $definitions;
      global $plugindir;
   
      $rows = getSocialLinksSidebar();
      if(count($rows)==0)
         return;
      ////WPD_print("Found".count($rows)." networks.");
   
      foreach ($rows as $row) {
         //WPD_print("SiteID: " . $row[1]);
         $linkInfoArray = $definitions[$row[1]];
         //WPD_print('network info '. $linkInfoArray);
         $url = str_replace("%userid%",$row[2],$linkInfoArray[KEY_URL_TEMPLATE]);
         $innerHTML = $innerHTML . "<a id='link_$row[0]' href='$url' rel='me'><img src='$plugindir/images/".$linkInfoArray[KEY_IMAGE]."' alt='".$linkInfoArray[KEY_DISPLAY_NAME]."' title='".$linkInfoArray[KEY_DISPLAY_NAME]."'/>".$linkInfoArray[KEY_DISPLAY_NAME]."</a>";
         if($row != $rows[count($rows)-1]){
            $innerHTML = $innerHTML."\n";
         }
      }
   
      return $innerHTML;
   }
   
   function generateSocialLinksSidebarPreviewInnerHTML($delimiter){
      global $definitions;
      global $plugindir;
   
      $rows = getSocialLinksSidebar();
      if(count($rows)==0)
         return;
      //WPD_print("Found ".count($rows)." networks.");
   
      foreach ($rows as $row) {
         //WPD_print("SiteID: " . $row[2]);
         //var_dump($row);
         $linkInfoArray = $definitions[$row[1]];
         //var_dump($linkInfoArray);
         //WPD_print('network info '. $linkInfoArray);
         $url = str_replace("%userid%",$row[2],$linkInfoArray[KEY_URL_TEMPLATE]);
         $innerHTML = $innerHTML . "<span id='link_$row[0]' title='$url'><img style='margin:2px' src='$plugindir/images/".$linkInfoArray[KEY_IMAGE]."' alt='".$linkInfoArray[KEY_DISPLAY_NAME]."' title='".$linkInfoArray[KEY_DISPLAY_NAME]."'/></span>";
         if($row != $rows[count($rows)-1]){
            $innerHTML = $innerHTML.$delimiter;
         }
      }
   
      return $innerHTML;
   }

/*
function generateSocialLinksData(){
   global $definitions;

   $rows = getSocialLinksSidebar();
   if(count($rows)==0)
      return;
   ////WPD_print("Found".count($rows)." networks.");
   $data = '';
   foreach ($rows as $row) {
      $linkInfoArray = $definitions[$row[2]];
      $data += "link_$row[0],$linkInfoArray[0],$linkInfoArray[1],$linkInfoArray[4]\n";
   }
   return $data;

}
*/

   function social_links_sidebar_admin_menu(){

      add_management_page('Social Links Sidebar Settings', 'Social Links Sidebar', 8,__FILE__,'widget_social_links_sidebar_settings');
   
      global $plugindir;
      wp_enqueue_script('about-me', $plugindir . '/javascript.js',array('sack'));
      wp_enqueue_script('scriptaculous');
   }
   
   function addHeaderCodeNow(){
      //WPD_print("header code");
      global $plugindir;
      echo '<link type="text/css" rel="stylesheet" href="' . $plugindir . '/stylesheet.css" />' . "\n";
   
   }



////WPD_print("Registering plugin");

   add_action('wp_head','addHeaderCodeNow');



   //Add action to load sub menu
   add_action('admin_menu', 'social_links_sidebar_admin_menu');




   //Add ajax callback action called from client side javascript
   add_action('wp_ajax_social_links_sidebar_add_network', 'wp_ajax_social_links_sidebar_add_network' );
   add_action('wp_ajax_social_links_sidebar_delete_network', 'wp_ajax_social_links_sidebar_delete_network' );
   
   register_sidebar_widget('Social Links Sidebar', 'widget_social_links_sidebar');
   register_widget_control('Social Links Sidebar', 'widget_social_links_sidebar_control');

}//End of SocialLinks class


add_action('plugins_loaded','social_links_sidebar_wrapper');

//todo handle auto db table update
function social_links_sidebar_install(){
   //require_once('datastore.php');
   //sl_install();

   global $wpdb;

   //WPD_print("Installing Social Links Plugin");
   //echo '<div>Activation social links</div>';

   $table_name = $wpdb->prefix . "social_links_sidebar";

   // $installed_ver = get_option( "SOCIAL_LINKS_DB_VERSION" );

   if($wpdb->get_var("show tables like '$table_name'") != $table_name ) {

      $sql = "CREATE TABLE " . $table_name . " (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         network_id int not null,
         user_info VARCHAR(55) NOT NULL,
         sort_order int not null DEFAULT 0,
         UNIQUE KEY id (id)
         );";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);


      add_option("SOCIAL_LINKS_DB_VERSION", SOCIAL_LINKS_DB_VERSION);
   }
}


//Administration page
$message;
$messageClass;
function widget_social_links_sidebar_settings(){

   if (isset($_POST['saveorder']))
   {
      saveSortOrderNow();
   }


   global $definitions;
   global $message;
   global $messageClass;
   global $plugindir;

   $visibility = 'hidden';
   if(!empty($messageClass))
      $visibility = 'visible';

   ?>


   <div class="wrap">
      <h2>Social Links Sidebar</h2>


      <div id="message" class="<?php echo $messageClass;  ?>" style="background-color: rgb(255, 251, 204);margin-top:10px;display:block;visibility:<?php echo $visibility;  ?>;width:300px"><?php echo $message;  ?></div>
      <div style="position:relative;float:left;margin-right:20px;">
         <h3>Add New Social Link</h3>
         <select id='networkDropdown' onchange='selectionChanged()'>
            <option>Select network...</option>

            <?php

         foreach ($definitions as $key => $linkInfoArray){
            echo "<option value='$linkInfoArray[0]' instruction='$linkInfoArray[3]'>$linkInfoArray[4]</option>";
         }
         ?>
      </select>

      <label id='instruction'></label>
      <br/>
      <input type="text" id="addSettingInput" style="width:400px;" onkeydown="if(event.keyCode == 13){social_links_ajax_addNetwork(document.getElementById('networkDropdown').selectedIndex,document.getElementById('addSettingInput'),document.getElementById('responseDiv'));}">
      <input type="button" id="addButton" value="Add" disabled=true
      onclick="social_links_ajax_addNetwork(document.getElementById('networkDropdown').selectedIndex,document.getElementById('addSettingInput'),document.getElementById('responseDiv'));" />
      <br/>
   </div> 
   
   <form method="post" onSubmit="social_links_sidebar_ajax_saveOrder()" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

      <div style="float:left;">
         <h3>Preview</h3>
         <div  id="displayDiv" style="width:100px;cursor:move;" class="drop_target">
            <?php echo generateSocialLinksSidebarPreviewInnerHTML("\n");  ?>
         </div>

         <input type="submit" id="saveOrderButton" name="saveorder" value="Save Order" style="margin-top:20px"/>
         <input type="hidden" name="sortOrderData" id="sortOrderData"/>
         <input type="hidden" name="callBackUrl" id="callBackUrl" value="<?php echo $plugindir ?>"/>
      </div>
      <div id="trash" style="float:left;position:relative;top:45px;left:25px;width:50px;height:50px;" class="drop_target">
         <img src="<?php echo $plugindir ?>/images/trash.jpg"/>
      </div>
   </form>
   <div style="clear: both;"> </div>
   <div>
      <p>
         To add a new link select the network from the drop down, fill in the appropriate information and press enter.<br/>
         To change the order they appear, rearrange the icons in the preview and click 'Save Order'. <br/>
         To delete a link, simply drag it to the trash can.
      </p>
   </div>
</div>	
<script language="JavaScript">
createSortables();
</script>
<?php


}//End of widget_social_links_sidebar_settings

function saveSortOrderNow(){
   global $wpdb;
   global $message;
   global $messageClass;
   //WPD_print("Action: " . $action);
   //WPD_print("Sort Data: " . $sortDataOrder);
   $sortDataOrder = !empty($_POST['sortOrderData']) ? $_POST['sortOrderData'] : '';
   if(!empty($sortDataOrder))
   {
      //WPD_print("Saving order");
      parse_str($sortDataOrder,$newSortorderArray);
      if(count($newSortorderArray) != 0){
         //WPD_print("List size: " . count($newSortorderArray));
         $table_name = $wpdb->prefix . "social_links_sidebar";
         foreach($newSortorderArray["displayDiv"] as $order => $id){
            //WPD_print('Order: '.$order.' Value: '.$id);

            $sql = 'Update ' .  $table_name . ' Set sort_order='.$order.' where id='.$id;
            $result = $wpdb->query($wpdb->prepare($sql));
            //WPD_print('Result for '.$sql.' is '.$result);

         }
         $message = 'Saved links\' order.';
      }
      else{
         $message = 'No items to save.';
      }
      $messageClass = 'updated fade';
   }
}


?>
