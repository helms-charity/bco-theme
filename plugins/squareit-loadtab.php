<?php
/*
 Name: Admin Tab Loader
 Description: Allows plugins to create admin tabs and use full content canvas and side bar.
 Version: 1.0.0
 Author: Julian Castaneda (Square IT Solutions)
 Author URI: http://www.squareitsol.com/
 */

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

add_action('header','squareit_tabLoadStyle',array());

# register plugin
register_plugin(
	$thisfile,
	'Admin Tab Loader',
	'1.0',
	'Square IT Solutions',
	'http://www.squareitsol.com/',
	'Description: Allows plugins to create admin tabs and use full content canvas and side bar.',
	'plugins', //page type
	'squareit_setup_loadtab'
);

//install loadtab.php into /admin
$file = 'loadtab.php';
//check if random.php exists in admin section
if(!file_exists(GSADMINPATH.$file)) {
	if (copy(GSPLUGINPATH.'squareit-loadtab/'.$file, GSADMINPATH.$file)) { }
}

function squareit_setup_loadtab(){}

/**
 * Creates a new style for selected tab item
 */
function squareit_tabLoadStyle()
{
	?>
	<style>
		.tabSelected {
			background: #F9F9F9 !important;
			color: #182227 !important;;
		}
	</style>
	<?php
}

/**
 * @function sqr_createSideMenu
 * @param $plugin  	- Plugin Name
 * @param $item  	- Plugin Sub Item
 * @param $txt     	- text to display on link
 *
 */
function sqr_createSideMenu($plugin, $item ,$txt){
	$class="";
	if (@$_GET['item'] == @$item) {
		$class='class="current"';
	}

	echo '<li><a href="loadtab.php?id='.$plugin.'&item='.$item.'" '.$class.' >'.$txt.'</a></li>';
}



/**
 * @function sqr_createNavTab
 * @param $plugin  	- Plugin Name
 * @param $txt 		- text to display on link
 */
function sqr_createNavTab($plugin,$txt) {
	$class = '';
	if (@$_GET['id'] == @$plugin) {
		$class='class="tabSelected"';
	}
	echo '<li><a href="loadtab.php?id='.$plugin.'&item='.$plugin.'" '.$class.' >';
	echo $txt;
	echo "</a></li>";
}
