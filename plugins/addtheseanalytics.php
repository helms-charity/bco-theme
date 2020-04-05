<?php
/*
Plugin Name: add this + google analytics
Description: AddThis code is added to the bottom of all posts/pages that link up to your Google Analytics account.
Version: 1.0
Author: Charity Musielak
Author URI: http://venturewisedesign.com/
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# registration
register_plugin(
	$thisfile,
	'add these analytics',
	'1.0',
	'Charity Musielak',
	'http://venturewise.co',
	'AddThis code is added to the bottom of all posts/pages that link up to your Google Analytics account',
	'',
	''
);

# hooks
add_action('theme-header', 'gssocial_css');
add_action('content-bottom', 'analyticscode');

# functions
function gssocial_css() {
	echo '
		<style type="text/css">
			div#socialize {margin:20px 0;}
			div#socialize a {margin:0 8px 0 0 0;}
			div#socialize a img {border:none;}
		</style>
	';
}

function analyticscode() {
	echo '
	<div id="socialize">';
	
	
	
	include('plugins/analyticsjs/analyticsjs.php');
	echo '
	</div>';
}

?>