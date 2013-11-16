<?php
// If you have your 'wp-content' directory in a place other than the default location, please specify your blog directory here. This is not your blog url. It is the address in your server. For example: '/public_html/myblog'
$blogdir = ""; 

if(isset($_POST['refresh'])) {

	if (!$blogdir) {
		$blogdir = preg_replace('|/wp-content.*$|','', __FILE__);
	}
	if($blogdir == __FILE__) {
		$blogdir = preg_replace('|\wp-content.*$|','', __FILE__);
		include_once($blogdir.'\wp-config.php');
		include_once($blogdir.'\wp-includes\wp-db.php');
	}
	else {
		include_once($blogdir.'/wp-config.php');
		include_once($blogdir.'/wp-includes/wp-db.php');
	}
	include_once(str_replace("-ajax", "", __FILE__));

	$page_num = isset($_POST['page_num'])?$_POST['page_num']:1;
	$page1 = isset($_POST['p1'])?$_POST['p1']:1;
	$page2 = isset($_POST['p2'])?$_POST['p2']:1;
	$page3 = isset($_POST['p3'])?$_POST['p3']:1;
	$page4 = isset($_POST['p4'])?$_POST['p4']:1;
	$page5 = isset($_POST['p5'])?$_POST['p5']:1;
	$page6 = isset($_POST['p6'])?$_POST['p6']:1;
	$page7 = isset($_POST['p7'])?$_POST['p7']:1;
	$page8 = isset($_POST['p8'])?$_POST['p8']:1;
	$page9 = isset($_POST['p9'])?$_POST['p9']:1;
	$page10 = isset($_POST['p10'])?$_POST['p10']:1;
	$end = isset($_POST['end'])?$_POST['end']:1;

	if ($end == false) {$page_num++;} //Next Page - Page Number increase!
	for ($i = 1; $i <= 10; $i++) {
		$pnum = 'p'.$i;
		if ($_POST[$pnum] != 'null' && $done == false) {
			if ($i == 10 ) { // if all 10 spots are filled!
				$pnum = 'p'.$i;
				$page = isset($_POST[$pnum])?$_POST[$pnum]:1;
				$end = true;
			}
			$page = isset($_POST[$pnum])?$_POST[$pnum]:1;
			if ($i == $page_num ) {	$end = false; break; }
		}
		else {  // If some of the spots are left blank
			$i--;
			$pnum = 'p'.$i;
			$page = isset($_POST[$pnum])?$_POST[$pnum]:1;
			$end = true;
			break;
		}
	}
//	print "page_num:".$page_num."<br>";
	//Now that we've got page numbers we need to figure a way to pull what the page_id is from the form for page_id1 - page_id6
	
	$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'pageTitle'=>'Change the World', 'page_id'=>$page, 'page_id1'=>$page1, 'page_id2'=>$page2, 'page_id3'=>$page3, 'page_id4'=>$page4, 'page_id5'=>$page5, 'page_id6'=>$page6, 'page_id7'=>$page7, 'page_id8'=>$page8, 'page_id9'=>$page9, 'page_id10'=>$page10) );
	$args = "ajax_refresh=2&page_num={$page_num}&page_end={$end}";

	if($response = subpageslider_page($args, $instance)) {
		@header("Content-type: text/javascript; charset=utf-8");
		//die( $response ); 
	}
	else
		die( $error );
}

?>