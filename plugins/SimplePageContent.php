<?php
/*
Plugin Name: Simple Page Content
Description: Get a HTML of page without the whole html and template, but just only the page content
Version: 1.2
Author: Sanjeya Cooray
Author URI: http://www.misadev.com/sanjeya/
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 
	'Simple Page Content', 	
	'1.2', 		
	'Sanjeya Cooray',
	'http://www.misadev.com/sanjeya/', 
	'Get a HTML of page without the whole html and template, but just only the page content',
	'theme',
	'hello_world_show'  
);

# functions
function get_page_by_id($id){
	$file = "data/pages/". $id .".xml";
	if ( file_exists($file) ){
		$data_index = getXML($file);		
		$page=stripslashes( html_entity_decode($data_index->content, ENT_QUOTES, 'UTF-8') );
	}else{
		$page="";
	}
	
	return $page;
}

?>