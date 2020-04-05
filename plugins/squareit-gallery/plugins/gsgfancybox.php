<?php
# get correct id for plugin
$gsgthisfile=basename(__FILE__, ".php");

# register plugin
gsg_register_plugin(
	$gsgthisfile,
	'GSG Fancy Box Display',
	'0.5',
	'Square IT Solutions',
	'http://www.squareitsol.com/',
	'Description: Enables the use of Fancy Box to display images as lightbox gallery',
	'plugins', //page type
	'squareit_fancybox'
);

add_action('gsg-head','sqr_gsg_fancybox_addHeaderScripts',array());
//add_action('gsg-settings-render', 'sqr_gsg_fancybox_rendersettings', array());
//add_action('gsg-settings-process', 'sqr_gsg_fancybox_processsettings', array());

function squareit_fancybox(){}

function sqr_gsg_fancybox_addHeaderScripts()
{
	global $SITEURL;
	
	$selector = 'gsgimage';
	if (isset($_SESSION['SQR_GSG']['gallery_img_class']))
	{
		$selector = $_SESSION['SQR_GSG']['gallery_img_class'];
	}
	?>
	
	<script type="text/javascript" src="<?php echo $SITEURL.GSG_RELFOLDERPATH?>plugins/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
	<script type="text/javascript" src="<?php echo $SITEURL.GSG_RELFOLDERPATH?>plugins/fancybox/jquery.easing-1.3.pack.js"></script>
	<script type="text/javascript" src="<?php echo $SITEURL.GSG_RELFOLDERPATH?>plugins/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
	
	<link rel="stylesheet" href="<?php echo $SITEURL.GSG_RELFOLDERPATH?>plugins/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
	
	<script>
	$jSqr(document).ready(function() { 
		$jSqr("a.<?php echo $selector;?>").fancybox();
	});
	</script>
	<?php 
}

//TODO Add configurable options
function sqr_gsg_fancybox_rendersettings(){}
function sqr_gsg_fancybox_processsettings(){}