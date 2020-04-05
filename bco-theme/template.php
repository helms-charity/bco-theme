<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/****************************************************

 ______________________________________________________________
|            |                                                 |
| \  \ /  /  |    DESIGN + VentureWise {http://VentureWise.co} |
|  \  \  /   |      DATE + 2/2011                              |
|   \/ \/    | COPYRIGHT + venturewise.co                      |
|______________________________________________________________|

*****************************************************/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<title><?php get_page_clean_title(); ?> | <?php get_site_name(); ?></title>

	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

	<meta name="robots" content="index, follow" />



			<?php get_header(); ?>
            <link href='http://fonts.googleapis.com/css?family=PT+Sans:regular,bold' rel='stylesheet' type='text/css'>
            <link href='http://fonts.googleapis.com/css?family=Dancing+Script' rel='stylesheet' type='text/css'>
            <link  href="http://fonts.googleapis.com/css?family=Corben:bold" rel="stylesheet" type="text/css" >

	<link rel="stylesheet" type="text/css" href="<?php get_theme_url(); ?>/bco_style.css" />

		<link rel="stylesheet" type="text/css" href="<?php get_theme_url(); ?>/css/superfish.css" media="screen">



		<script type="text/javascript" src="<?php get_theme_url(); ?>/js/hoverIntent.js"></script>



		<script type="text/javascript" src="<?php get_theme_url(); ?>/js/superfish.js"></script>

		<script type="text/javascript">



		// initialise plugins

		jQuery(function(){

			jQuery('ul.menu').superfish();

		});



		</script>





	<script type="text/javascript"><!--

		try {

			document.execCommand("BackgroundImageCache", false, true);

		} catch(err) {}

		/* IE6 flicker hack from http://dean.edwards.name/my/flicker.html */

	--></script>



</head>



<body id="<?php get_page_slug(); ?>" >

<div class="wrapper"><div id="contentwrapper"><div id="bodycontent">

	<div id="header"><div id="logo+tag">

		<a class="logo" href="<?php get_site_url(); ?>"><img src="<?php get_theme_url(); ?>/images/bco-logo.jpg" border="0" height="130" /></a>

	<div class="tagline"><?php get_component('tagline'); ?></div>	</div>

		<?php menu_master (); ?>

	</div><!-- end div#header -->








		<div class="post">

			<h1><?php get_page_title(); ?></h1>



			<div class="postcontent">

				<?php get_page_content(); ?>

			</div>


		</div>

	</div><!-- end div#bodycontent -->



	<div id="sidebar">

		<div class="featured">

<?php echo get_page_by_id('_right_sidebar'); ?>
			<div class="clear"></div>

		</div>

	</div><!-- end div#sidebar -->



	<div class="clear"></div>	</div></div><!-- end div.wrapper -->


<div id="footerwrapper">
	<div id="footer">



		<div class="clear"></div><div id="whitefootertext"><div class="mission"><?php echo get_page_by_id('_footer'); ?></div><div class="social">
		 <?php get_component('footercontact'); ?>
</div></div>
<p class="left-footer">&copy;<?php echo date('Y'); ?> <strong><?php get_site_name(); ?></strong></p>

	</div><!-- end div#footer --></div>







</body>

</html>