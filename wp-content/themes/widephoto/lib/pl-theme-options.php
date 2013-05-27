<?php
/**
 * Define our settings sections
 *
 * array key=$id, array value=$title in: add_settings_section( $id, $title, $callback, $page );
 * @return array
 */
 
function widephoto_options_page_sections() {
	
	$sections = array();
	// $sections[$id] 				= __($title, 'widephoto');
	$sections['txt_section'] 		= __('General Settings', 'widephoto');
	//$sections['txtarea_section'] 	= __('Textarea Form Fields', 'widephoto');
	//$sections['select_section'] 	= __('Select Form Fields', 'widephoto');
	//$sections['checkbox_section'] 	= __('Checkbox Form Fields', 'widephoto');
	
	return $sections;	
} 


/**
 * Define our form fields (settings) 
 *
 * @return array
 */
function widephoto_options_page_fields() {
	// Text Form Fields section
	$options[] = array(
		"section" => "txt_section",
		"id"      => widephoto_SHORTNAME . "_bg_image_1",
		"title"   => __( 'Background Image 1', 'widephoto' ),
		"desc"    => __( 'Background Image 1', 'widephoto' ),
		"type"    => "text",
		"std"     => __('','widephoto')
	);
	
	$options[] = array(
		"section" => "txt_section",
		"id"      => widephoto_SHORTNAME . "_bg_image_2",
		"title"   => __( 'Background Image 2', 'widephoto' ),
		"desc"    => __( 'Background Image 2', 'widephoto' ),
		"type"    => "text",
		"std"     => __('','widephoto')
	);
	
	$options[] = array(
		"section" => "txt_section",
		"id"      => widephoto_SHORTNAME . "_bg_image_3",
		"title"   => __( 'Background Image 3', 'widephoto' ),
		"desc"    => __( 'Background Image 3', 'widephoto' ),
		"type"    => "text",
		"std"     => __('','widephoto')
	);
		
	return $options;	
}

