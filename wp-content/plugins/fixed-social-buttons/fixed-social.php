<?php
/*
	Plugin Name: Fixed Social buttons
	Plugin URI: http://emyl.fr/ongame/poker/plugin-social-buttons/
	Version: 2.4.5.3
	Author: loane
	Author URI: http://www.webonews.fr
	Description: Let your visitors make your site popular by sharing your pages thanks to colorfull and attractiv social buttons. 
	You can show it on top or bottom with 3 styles. Check screenshots of social buttons set on top and screenshot of admin panel.
	*/

	/*
	Copyright 2009  Loan  (http://www.webonews.fr)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  

	/**
	 * Called upon activation of the plugin. Sets some options.
	 */
	function initfixedsocial(){
		add_option('stylenu', '1');  	
		add_option('cible', '1');  	
		add_option('follow', '2');	
		add_option('verti', '2');			
		add_option('show', 'top');
		add_option('showtweet', 'yes'); 		
		add_option('showtechno', 'yes'); 		
		add_option('showface', 'yes'); 		
		add_option('showspace', 'yes'); 		
		add_option('showdigg', 'yes'); 		
		add_option('showdeli', 'yes'); 		
		add_option('showbook', 'yes'); 		
		add_option('showfeed', 'yes');		
		add_option('showedin', 'yes');	
		add_option('showebo', 'yes');
		add_option('showreddit', 'yes');		
		add_option('showflickr', 'yes');	
		add_option('showstumb', 'yes');
		add_option('shownews', 'yes');		
		add_option('showyou', 'yes');		
		add_option('feedurl', '');
		add_option('flickrprof', '');
		add_option('twitprof', '');		
		add_option('faceprof', '');			
		add_option('spaceprof', '');			
		}

	/**
	 * Called upon deactivation of the plugin. Cleans our mess.
	 */
	function destroyfixedsocial(){
		delete_option('stylenu');
		delete_option('cible');
		delete_option('follow');
		delete_option('verti');
		delete_option('show');
		delete_option('showtweet');
		delete_option('showtechno');
		delete_option('showface');
		delete_option('showdigg');
		delete_option('showdeli');
		delete_option('showbook');
		delete_option('showfeed');
		delete_option('feedurl');		
		delete_option('showedin');	
		delete_option('showebo');		
		delete_option('showreddit');	
		delete_option('showflickr');
		delete_option('showstumb');
		delete_option('shownews');		
		delete_option('flickrprof');		
		delete_option('twitprof');		
		delete_option('faceprof');		
		delete_option('spaceprof');	
		delete_option('showyou');		
	}

	/**
	 * For the header.
	 */
	function headerfixedsocial(){
	// use stylever.css for vertical axe, 
	if (get_option('verti')=='2') { $distance="right"; $fichier="hor".get_option('show');} else { $distance=get_option('show'); $fichier="ver"; };		
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('wpurl').'/wp-content/plugins/fixed-social-buttons/style'.$fichier.'.css" />';
        }

    function fixed_icons() {

    echo '<div class="fixed-icons">';
	if (get_option('verti')=='2') { $distance="right"; $fichier="hor".get_option('show');} else { $distance=get_option('show'); $fichier="ver"; };

	
	$pos = 0; $xpos = 0; $ypos = 0;
	$premier = 1;
	switch (get_option('stylenu')) {
	case '1':
		$long = 35; $taille = 32;
		break;
	case '2':
		$long = 27; $taille = 24;
		break;
	case '3':
		$long = 40; $taille = 39;
		break;
	}

		if (get_option('feedurl') == ' ' or '' ) {$flux = 'http://www.webonews.fr'; $titreflux = 'Annuaire Web';} 
                                            else {$flux=get_option('feedurl'); $titreflux = 'Subscribe';};		
		if (get_option('cible') == '1') {$wcible = 'target="_blank"';} else {$wcible='';};
		if (get_option('follow') == '1') {$follow = ' rel="nofollow" ';} else {$follow=' ';};
        if (is_single()||is_page()) { $titre = get_the_title(); $lien = get_permalink(); }
			else { $titre = get_bloginfo('name'); $lien = get_bloginfo('wpurl'); } ; 
			
		if (get_option('showfeed')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo ' 
			<a href="'.$flux.'" '.$wcible.$follow.' title="'.$titreflux.'" class="buttonfixed feed'.get_option('stylenu').'" style="'.$distance.':'.$pos.'px;">
				Rss Feed</a> ';
											}
		if (get_option('showtweet')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			if (get_option('twitprof')=='' or get_option('twitprof')==' ') {
				echo '
				<a href="http://twitter.com/home?status='.$titre.'+-+'.$lien.'+" '.$wcible.$follow.' class="buttonfixed twit'.get_option('stylenu').'" title="Tweet this" style="'.$distance.':'.$pos.'px;">
				Tweeter button</a>';	}
			else {
				echo '
				<a href="http://twitter.com/'.get_option('twitprof').'" '.$wcible.$follow.' class="buttonfixed twit'.get_option('stylenu').'" title="Follow me" style="'.$distance.':'.$pos.'px;">
					Tweeter button</a>';	}				
											}
			if (get_option('showface')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			if (get_option('faceprof')=='' or get_option('faceprof')==' ') {
			echo '
			<a href="http://www.facebook.com/share.php?u='.$lien.'&t='.$titre.'" '.$wcible.$follow.' class="buttonfixed face'.get_option('stylenu').'" title="Share it on Facebook" style="'.$distance.':'.$pos.'px;">
				Facebook button</a>';
				}
						else {
			echo '
			<a href="http://www.facebook.com/'.get_option('faceprof').'" '.$wcible.$follow.' class="buttonfixed face'.get_option('stylenu').'" title="Facebook profile" style="'.$distance.':'.$pos.'px;">
				Facebook button</a>';
				}
											}
		if (get_option('showtechno')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://technorati.com/faves?add='.$lien.'" '.$wcible.$follow.' class="buttonfixed techno'.get_option('stylenu').'" title="Share it On Technorati" style="'.$distance.':'.$pos.'px;">
				Technorati button</a>';
											}
		if (get_option('showdigg')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://reddit.com/submit?url='.$lien.'&title='.$titre.'" '.$wcible.$follow.' title="Reddit" class="buttonfixed digg'.get_option('stylenu').'" style="'.$distance.':'.$pos.'px;">
				Reddit button</a>';
											}
		if (get_option('showspace')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;		
			if (get_option('spaceprof')=='' or get_option('spaceprof')==' ') {
			echo '
			<a href="http://www.myspace.com/Modules/PostTo/Pages/?u='.$lien.'&t='.$titre.'" '.$wcible.$follow.' class="buttonfixed space'.get_option('stylenu').'" title="Share it on Myspace" style="'.$distance.':'.$pos.'px;">
				Myspace button</a>';
				}
			else {
			echo '
			<a href="http://www.myspace.com/'.get_option('spaceprof').'" '.$wcible.$follow.' class="buttonfixed space'.get_option('stylenu').'" title="Myspace profile" style="'.$distance.':'.$pos.'px;">
				Myspace button</a>';
											}
				}
		if (get_option('showedin')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://www.linkedin.com/shareArticle?mini=true&url='.$lien.'&title='.$titre.'" '.$wcible.$follow.' class="buttonfixed edin'.get_option('stylenu').'" title="Share it on Linkedin" style="'.$distance.':'.$pos.'px;">
				Linkedin button</a>';
											}
		if (get_option('showebo')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://www.webonews.fr/index.php?do=basic&action=meta_send&url='.$lien.'" '.$wcible.$follow.' class="buttonfixed webo'.get_option('stylenu').'" title="Submit it on Webonews" style="'.$distance.':'.$pos.'px;">
				Webonews button</a>';
											}
		if (get_option('showdeli')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://del.icio.us/post?url='.$lien.'&title='.$titre.'" '.$wcible.$follow.' class="buttonfixed deli'.get_option('stylenu').'" title="Share it on Delicious" style="'.$distance.':'.$pos.'px;">
				Delicious button</a>';
											}
		if (get_option('showreddit')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://digg.com/submit?phase=2&url='.$lien.'&title='.$titre.'" '.$wcible.$follow.' class="buttonfixed reddit'.get_option('stylenu').'" title="Digg" style="'.$distance.':'.$pos.'px;">
				Digg button</a>';
											}	
		if (get_option('showflickr')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://www.flickr.com/photos/'.get_option('flickrprof').'" '.$wcible.$follow.' class="buttonfixed flickr'.get_option('stylenu').'" title="Flickr" style="'.$distance.':'.$pos.'px;">
				Flickr button</a>';
											}	
		if (get_option('showstumb')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://www.stumbleupon.com/submit?url='.$lien.'&title='.$titre.'" '.$wcible.$follow.' class="buttonfixed stumb'.get_option('stylenu').'" title="Stumbleupon" style="'.$distance.':'.$pos.'px;">
				Stumbleupon button</a>';
											}
		if (get_option('shownews')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://www.newsvine.com/_tools/seed&save?u='.$lien.'&h='.$titre.'" '.$wcible.$follow.' class="buttonfixed news'.get_option('stylenu').'" title="Newsvine" style="'.$distance.':'.$pos.'px;">
				Newsvine button</a>';
											}	
		if (get_option('showyou')=='yes') {
			if ($premier==1) {$pos = $pos+5;} else {$pos = $pos+$long;} ; $premier=0;
			echo '
			<a href="http://www.youtube.com/user/'.get_option('youprof').'" '.$wcible.$follow.' class="buttonfixed you'.get_option('stylenu').'" title="Youtube" style="'.$distance.':'.$pos.'px;">
				Youtube button</a>';
											}												
                echo '</div>';       										
	}
	
	/**
	 * Outputs the HTML form for the admin area. Also updates the options.
	 */
	function adminFormfixedsocial(){
		if($_POST['action'] == 'save'){
			$ok = false;
			
			if($_POST['stylenu']){
				update_option('stylenu', $_POST['stylenu']);
				$ok = true;
			}
			if($_POST['feedurl']){
				update_option('feedurl', $_POST['feedurl']);
				$ok = true;
			}
			if($_POST['twitprof']){
				update_option('twitprof', $_POST['twitprof']);
				$ok = true;
			}
			if($_POST['faceprof']){
				update_option('faceprof', $_POST['faceprof']);
				$ok = true;
			}			
			if($_POST['spaceprof']){
				update_option('spaceprof', $_POST['spaceprof']);
				$ok = true;
			}			
			if($_POST['flickrprof']){
				update_option('flickrprof', $_POST['flickrprof']);
				$ok = true;
			}	
			if($_POST['youprof']){
				update_option('youprof', $_POST['youprof']);
				$ok = true;
			}				
			if($_POST['show']){
				update_option('show', $_POST['show']);
				$ok = true;
			}	
			if($_POST['cible']){
				update_option('cible', $_POST['cible']);  
				$ok = true;
			}
			if($_POST['verti']){
				update_option('verti', $_POST['verti']);  
				$ok = true;
			}			
			if($_POST['follow']){
				update_option('follow', $_POST['follow']);  
				$ok = true;
			}			
			if($_POST['showfeed'] == 'yes'){
				update_option('showfeed', 'yes');
			}
			else{
				update_option('showfeed', 'no');
			}
			if($_POST['showtweet'] == 'yes'){
				update_option('showtweet', 'yes');
			}
			else{
				update_option('showtweet', 'no');
			}
			if($_POST['showtechno'] == 'yes'){
				update_option('showtechno', 'yes');
			}
			else{
				update_option('showtechno', 'no');
			}
			if($_POST['showface'] == 'yes'){
				update_option('showface', 'yes');
			}
			else{
				update_option('showface', 'no');
			}
			if($_POST['showdigg'] == 'yes'){
				update_option('showdigg', 'yes');
			}
			else{
				update_option('showdigg', 'no');
			}
			if($_POST['showspace'] == 'yes'){
				update_option('showspace', 'yes');
			}
			else{
				update_option('showspace', 'no');
			}			
			if($_POST['showdeli'] == 'yes'){
				update_option('showdeli', 'yes');
			}
			else{
				update_option('showdeli', 'no');
			}	
			if($_POST['showedin'] == 'yes'){
				update_option('showedin', 'yes');
			}
			else{
				update_option('showedin', 'no');
			}			
			if($_POST['showbook'] == 'yes'){
				update_option('showbook', 'yes');
			}
			else{
				update_option('showbook', 'no');
			}	
			if($_POST['showebo'] == 'yes'){
				update_option('showebo', 'yes');
			}
			else{
				update_option('showebo', 'no');
			}	
			if($_POST['showreddit'] == 'yes'){
				update_option('showreddit', 'yes');
			}
			else{
				update_option('showreddit', 'no');
			}
			if($_POST['showflickr'] == 'yes'){
				update_option('showflickr', 'yes');
			}
			else{
				update_option('showflickr', 'no');
			}	
			if($_POST['showstumb'] == 'yes'){
				update_option('showstumb', 'yes');
			}
			else{
				update_option('showstumb', 'no');
			}	
			if($_POST['shownews'] == 'yes'){
				update_option('shownews', 'yes');
			}
			else{
				update_option('shownews', 'no');
			}	
			if($_POST['showyou'] == 'yes'){
				update_option('showyou', 'yes');
			}
			else{
				update_option('showyou', 'no');
			}			
			
			if($ok){
				?>
				<div id="message" class="updated fade">
					<p>Changes have been saved </p>
				</div>
				<?php
			}
			else{
				?>
				<div id="message" class="error fade">
					<p>An error has occurred</p>
				</div>
				<?php
			}
		}
		
		// get the options values
		$stylenu = get_option('stylenu'); 
		$cible = get_option('cible');
		$verti = get_option('verti');		
		$feedurl = get_option('feedurl');
		$twitprof = get_option('twitprof');	
		$flickrprof = get_option('flickrprof');	
		$show = get_option('show');
		$follow = get_option('follow');
		$showtweet = get_option('showtweet');
		$showfeed = get_option('showfeed');
		$showface = get_option('showface');
		$showtechno = get_option('showtechno');
		$showdigg = get_option('showdigg');
		$showspace = get_option('showspace');
		$showdeli = get_option('showdeli');		
		$showedin = get_option('showedin');			
		$showbook = get_option('showbook');	
		$showebo = get_option('showebo');		
		$showreddit = get_option('showreddit');		
		$showflickr = get_option('showflickr');	
		$showstumb = get_option('showstumb');	
		$shownews = get_option('shownews');			
		$showyou = get_option('showyou');			
		$faceprof = get_option('faceprof');		
		$spaceprof = get_option('spaceprof');	
		$youprof = get_option('youprof');		
	
		?>
		<div class="wrap">
		<img src="/wp-content/plugins/fixed-social-buttons/img/fixedsocial.gif">
<div id="dashboard-widgets-wrap">

<div id='dashboard-widgets' class='metabox-holder'>
	<div class='postbox-container' style='width:1000;'>
<div id='normal-sortables' class='meta-box-sortables'>
<div id="dashboard_right_now" class="postbox " >
<h3 class='hndle'><span>Fixed Social Buttons Settings</span></h3>

			<form method="post">
				<table class="form-table">
						<tr valign="top">
						<th scope="row">
							<label><b>Choose the button to show</b></label>
						</th>
						<td>
							<table><tr style="text-align: center";><td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/feed1.png"><br/>
							<input <?php echo (($showfeed == "yes")? 'checked="checked"' : ""); ?> name="showfeed" id="showfeed" type="checkbox" value="yes"/>
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/twit1.png">	<br/>						
							<input <?php echo (($showtweet == "yes")? 'checked="checked"' : ""); ?> name="showtweet" id="showtweet" type="checkbox" value="yes" />
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/face1.png"><br/>
							<input <?php echo (($showface == "yes")? 'checked="checked"' : ""); ?> name="showface" id="showface" type="checkbox" value="yes" />
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/techno1.png"><br/>
							<input <?php echo (($showtechno == "yes")? 'checked="checked"' : ""); ?> name="showtechno" id="showtechno" type="checkbox" value="yes" />
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/digg1.png"><br/>
							<input <?php echo (($showdigg == "yes")? 'checked="checked"' : ""); ?> name="showdigg" id="showdigg" type="checkbox" value="yes" />
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/space1.png"><br/>
							<input <?php echo (($showspace == "yes")? 'checked="checked"' : ""); ?> name="showspace" id="showspace" type="checkbox" value="yes" />
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/edin1.png"><br/>
							<input <?php echo (($showedin == "yes")? 'checked="checked"' : ""); ?> name="showedin" id="showedin" type="checkbox" value="yes" />
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/webo1.png"><br/>
							<input <?php echo (($showebo == "yes")? 'checked="checked"' : ""); ?> name="showebo" id="showebo" type="checkbox" value="yes" />
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/deli1.png"><br/>
							<input <?php echo (($showdeli == "yes")? 'checked="checked"' : ""); ?> name="showdeli" id="showdeli" type="checkbox" value="yes" />
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/reddit1.png"><br/>
							<input <?php echo (($showreddit == "yes")? 'checked="checked"' : ""); ?> name="showreddit" id="showreddit" type="checkbox" value="yes" />
							</td>
							<td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/flickr1.png"><br/>
							<input <?php echo (($showflickr == "yes")? 'checked="checked"' : ""); ?> name="showflickr" id="showflickr" type="checkbox" value="yes" />
							</td>	
							<td>							
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/stumb1.png"><br/>
							<input <?php echo (($showstumb == "yes")? 'checked="checked"' : ""); ?> name="showstumb" id="showstumb" type="checkbox" value="yes" />
							</td>	
							<td>							
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/news1.png"><br/>
							<input <?php echo (($shownews == "yes")? 'checked="checked"' : ""); ?> name="shownews" id="shownews" type="checkbox" value="yes" />
							</td>	
							<td>							
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/youtube1.png"><br/>
							<input <?php echo (($showyou == "yes")? 'checked="checked"' : ""); ?> name="showyou" id="showyou" type="checkbox" value="yes" />
							</td>							
							</tr></table>
					</tr>
				<tr valign="top">
						<th scope="row"  width="300">
							<label for="stylenu"><b>Buttons style to use?</b></label>
						</th>
						<td width="700">
						    <label>
							<table>
							<tr><td>
							<input <?php echo (($stylenu == "3")? 'checked="checked"' : ""); ?> name="stylenu" id="stylenu" type="radio" value="3" />
							</td><td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/feed3.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/twit3.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/face3.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/techno3.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/digg3.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/space3.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/deli3.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/edin3.png">	
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/webo3.png">		
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/reddit3.png">	
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/flickr3.png">	
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/stumb3.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/news3.png">	
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/youtube3.png">							
							</td></tr>							
							<tr><td>
							<input <?php echo (($stylenu == "1")? 'checked="checked"' : ""); ?> name="stylenu" id="stylenu" type="radio" value="1" />
							</td><td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/feed1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/twit1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/face1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/techno1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/digg1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/space1.png"
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/deli1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/edin1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/webo1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/reddit1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/flickr1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/stumb1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/news1.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/youtube1.png">							
							</td></tr>
						    <tr><td>
						    <input <?php echo (($stylenu == "2")? 'checked="checked"' : ""); ?> name="stylenu" id="stylenu" type="radio" value="2" />
							</td><td>
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/feed2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/twit2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/face2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/techno2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/digg2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/space2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/deli2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/edin2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/webo2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/reddit2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/flickr2.png">	
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/stumb2.png">
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/news2.png">		
							<img src="<?php echo get_bloginfo('wpurl') ;?>/wp-content/plugins/fixed-social-buttons/img/youtube2.png">							
							</td></tr>							
							</table>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="show"><b>Show on top or bottom?</b></label>
						</th>
						<td>
							<table>
							<tr><td width="90">						
						    <label>
							<input <?php echo (($show == "top")? 'checked="checked"' : ""); ?> name="show" id="show" type="radio" value="top" />
							top
							</label>
							</td><td width="90">							
						    <label>
							<input <?php echo (($show == "bottom")? 'checked="checked"' : ""); ?> name="show" id="show" type="radio" value="bottom" />
							bottom
							</label>
							</td></tr></table>	
						</td>

					</tr>
					<tr valign="top" width="300">
						<th scope="row">
							<label for="verti"><b>Show buttons in a horizontal or vertical axe?</b></label>
						</th>
						<td>
							<table>
							<tr><td width="90">						
						    <label>
							<input <?php echo (($verti == "2")? 'checked="checked"' : ""); ?> name="verti" id="verti" type="radio" value="2" />
							horizontal
							</label>
							</td><td width="90">							
						    <label>
							<input <?php echo (($verti == "1")? 'checked="checked"' : ""); ?> name="verti" id="verti" type="radio" value="1" />
							vertical
							</label>
							</td></tr></table>	
						</td>
					</tr>						
					<tr valign="top">
						<th scope="row">
							<label for="cible"><b>On click open in a new window:</b></label><br/>
							(attribute target="_blank")
						</th>
						<td>
							<table>
							<tr><td width="90">						
						    <label>
							<input <?php echo (($cible == "1")? 'checked="checked"' : ""); ?> name="cible" id="cible" type="radio" value="1" />
							yes
							</label>
							</td><td width="90">							
						    <label>
							<input <?php echo (($cible == "2")? 'checked="checked"' : ""); ?> name="cible" id="cible" type="radio" value="2" />
							no
							</label>
							</td></tr></table>	
						</td>
					</tr>						
					<tr valign="top">
						<th scope="row">
							<label for="follow"><b>Say to bots not to follow links  :</b></label><br/>
							(attribute rel="nofollow")
						</th>
						<td>
							<table>
							<tr><td width="90">
						    <label>
							<input <?php echo (($follow == "1")? 'checked="checked"' : ""); ?> name="follow" id="follow" type="radio" value="1" />
							yes
							</label>
							</td><td width="90">							
						    <label>
							<input <?php echo (($follow == "2")? 'checked="checked"' : ""); ?> name="follow" id="follow" type="radio" value="2" />
							no
							</label>
							</td></tr></table>								
						</td>
					</tr>	
					<tr valign="top">
						<th scope="row">
							<label for="feedurl"><b>Your feed url :</b></label>
						</th>
						<td>
							<input name="feedurl" type="text" id="feedurl" value="<?php echo $feedurl ;?>" class="regular-text code" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="flickrprof"><b>Your Flickr profile :</b></label><br/>
							If you show the flickr button, you have to fill your profile
						</th>
						<td>
							<input name="flickrprof" type="text" id="flickrprof" value="<?php echo $flickrprof ;?>" class="regular-text code" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="youprof"><b>Your Youtube profile :</b></label><br/>
							If you show the Youtube button, you have to fill your profile
						</th>
						<td>
							<input name="youprof" type="text" id="youprof" value="<?php echo $youprof ;?>" class="regular-text code" />
						</td>
					</tr>					
					<tr valign="top">
						<th scope="row">
							<b><label for="twitprof">Your Twitter profile (*):</label></b><br/><br/>
						</th>
						<td>
							<input name="twitprof" type="text" id="twitprof" value="<?php echo $twitprof ;?>" class="regular-text code" />
						</td>
					</tr>	
					<tr valign="top">
						<th scope="row">
							<b><label for="faceprof">Your Facebook profile (*):</label></b><br/><br/>
						</th>
						<td>
							<input name="faceprof" type="text" id="faceprof" value="<?php echo $faceprof ;?>" class="regular-text code" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<b><label for="spaceprof">Your Myspace profile (*):</label></b><br/><br/>
						</th>
						<td>
							<input name="spaceprof" type="text" id="spaceprof" value="<?php echo $spaceprof ;?>" class="regular-text code" />
						</td>
					</tr>	
			
				</table>
				<br/><br/>
				<p class="submit">
					<input type="hidden" name="action" value="save" />
					<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
				</p>
			</form>
			
</div>
</div>	</div><div class='postbox-container' style='display:none;width:49%;'>
<div id='column3-sortables' class='meta-box-sortables'>
</div>	</div><div class='postbox-container' style='display:none;width:49%;'>
<div id='column4-sortables' class='meta-box-sortables'>
</div></div></div>

<div class="clear"></div>
</div><!-- dashboard-widgets-wrap -->			
		    <br/><br/>
			<b>(*)</b> : If you want your visitors follow you on Twitter, Facebook or Myspace, fill your profiles. 
							To let them sharing your blog pages, leave it blank.<br/><br/>
			<b>You can ask support on <a href="http://emyl.fr/ongame/poker/plugin-social-buttons/" target="_blank">Plugin Page</a>. If you appreciate this plugin please <a href="http://wordpress.org/extend/plugins/fixed-social-buttons/" target="_blank">rate it</a>.<br/></b>
			<br/>
			<b>My other wordpress plugin : <a href="http://emyl.fr/ongame/poker/plugin-favicons/" target="_blank">Favicons</a><br/></b>

				<ul><li>Choose easily a favicon to make your site eye-catching and easily recognizable by visitors that have several windows opened on their navigator.</li></ul>
			</div>
		<?php 
	}
	
	/**
	 * Adds the sub menu in the admin panel
	 */
	function adminfixedsocial(){
		add_options_page('Fixed Social Buttons Administration', 'Fixed social Buttons', 'manage_options', __FILE__, 'adminFormfixedsocial');
	}

	// upon activation of the plugin, calls the init  function
	register_activation_hook(__FILE__, 'initfixedsocial');
	// upon deactivation of the plugin, calls the destroy  function
	register_deactivation_hook(__FILE__, 'destroyfixedsocial');
	
	// what to add in the header, calls the header  function
	add_action('wp_head', headerfixedsocial, 1);
    add_action('wp_footer', fixed_icons, 1);	
	// ads the submenu in the admin menu
	add_action('admin_menu', 'adminfixedsocial');
?>