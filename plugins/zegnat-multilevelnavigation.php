<?php
$thisfile = basename(__FILE__, ".php");
register_plugin(
	$thisfile,
	'Zegnåt’s multi-level navigation.',
	'3.1',
	'Martijn',
	'http://zegnat.net/',
	'A multi-level menu output for your theme, standard compliant and with customised sorting (coming soon).',
	'theme',
	'menu_master_settings'
);

require_once 'zegnat-files/find-url.php'; // Backwards compatibility
require_once 'zegnat-files/zegnat-menu-data.php';

function menu_master($a=null) {
	// Get the settings.
	global $menu_master_settings;
	$settings = getXML(GSPLUGINPATH.'/zegnat-files/menu-settings.xml');
	$menu_master_settings['sorting'] = explode(', ',(string)@$settings->sorting);
	$menu_master_settings['priority'] = ((string)@$settings->priority=='after'?true:false);
	// The script
	if (!isset($a) || empty($a)) { $a = return_page_slug(); }
	$data = zegnat_menu_data(true, $menu_master_settings['sorting']);
	$menu = $data->xpath('//*[parent=""]');
	if (!function_exists('menu_sorting')) {
		function menu_sorting($a, $b) {
			global $menu_master_settings;
			$sorting = $menu_master_settings['sorting'];
			foreach ($sorting as $item) {
				$c=strcmp($a->{$item}, $b->{$item});
				if ($c!=0) {
					if ($item=='menuOrder'&&$menu_master_settings['priority']==true) {
						if ((string)$b->menuOrder=='0') return -1;
						if ((string)$a->menuOrder=='0') return +1;
					}
					return $c;
				}
			}
			return 0;
		}
	}
	usort($menu, "menu_sorting");
	if (count($menu) > 0) {
	    echo '<ul class="menu">';
	    foreach ($menu as $link) {
	        if ("$link->slug" == "") $link->slug = "index";
	        $isActiveParent = count($data->xpath('//item[slug="'.$a.'"][parent="'.$link->slug.'"]'))>0;
	        echo '<li'.("$link->slug"==$a?' class="active"':($isActiveParent?' class="parent"':'')).'><a href="'.$link->url.'">'.$link->menu.'</a>';
	        $menu = $data->xpath('//*[parent="'.$link->slug.'"]');
	        usort($menu, "menu_sorting");
	        if (count($menu) > 0) {
	            echo '<ul class="submenu">';
	            foreach ($menu as $link) {
	                echo '<li'.("$link->slug"==$a?' class="active"':'').'><a href="'.$link->url.'">'.$link->menu.'</a></li>';
	            }
	            echo '</ul>';
	        }
	        echo '</li>';
	    }
	    echo '</ul>';
	}
}

// Here follows the whole admin section thing. A better way of coding these pages might be a good idea.

if (basename($_SERVER['PHP_SELF'],".php")==='load' && @$_GET['id']===$thisfile) { add_action('header','menu_master_head',''); }
add_action('theme-sidebar','createSideMenu',array($thisfile, 'Change Menu Settings')); 

function menu_master_head() { ?>
	<style type="text/css">
	ol li {
		margin-bottom: 1em;
	}
	#load .delete a {
		display: inline;
		padding: 4px 8px;
		margin-left: 5px;
	}
	var {
		color: #CF3805;
		font-family: mono;
		font-size: 120%;
	}
	</style>
	<script type="text/javascript">
		$(function() {
			$('span.delete a').live('click', function() {
				$(this).parent().parent().remove()
				return false;
			});
			$('#addSorting').bind('click', function() {
				$('#sorting li:first').clone().children().children().removeAttr('selected').parent().parent().append('<span class="delete"><a href="#">Remove</a></span>').appendTo('#sorting');
				return false;
			});
		});
	</script>
<?php }
function menu_master_settings() {
	$settings = getXML(GSPLUGINPATH.'/zegnat-files/menu-settings.xml');
	$selected = explode(', ',(string)@$settings->sorting);
	$priority = (string)@$settings->priority;
	$elements = array();
	$data = getXML(GSDATAPAGESPATH.'index.xml');
	foreach($data->children() as $child) { $elements[] = $child->getName(); }
	sort($elements);
	if (@count($_POST)>0) {
		if (isset($_POST['sort'])) $selected = $_POST['sort'];
		if (isset($_POST['priority'])) $priority = $_POST['priority'];
		$xml = @new SimpleXMLElement('<menu></menu>');
		$xml->addChild('sorting', implode($selected,', '));
		$xml->addChild('priority', $priority);
		$xml->asXML(GSPLUGINPATH.'/zegnat-files/menu-settings.xml');
	}
?>
<form method="post" action="<?php echo $_SERVER ['REQUEST_URI']; ?>">
<h3>Sorting</h3>
<ol id="sorting">
<?php
	foreach ($selected as $n => $element) {
		echo '<li><select class="text" name="sort[]">';
		foreach ($elements as $option) {
			echo "<option".($option==$element||($option=='url'&&$element=='slug')?' selected="selected"':'').($option=='url'?' value="slug"':'').">$option</option>";
		}
		echo '</select>'.($n!=0?'<span class="delete"><a href="#">Remove</a></span>':'').'</li>';
	}
?>
</ol>
<p><button id="addSorting">Add</button> one more step or <input type="submit" class="submit" value="save it"> as it is now.</p>
</form>
</div>
<div class="main">
<form method="post" action="<?php echo $_SERVER ['REQUEST_URI']; ?>">
<h3>Priorities</h3>
<p>Put menu items without any priority, those with their priority set to <var>-</var>, <select class="text" style="width:6em;" name="priority"><option<?php echo ($priority=='before'?' selected="selected"':''); ?>>before</option><option<?php echo ($priority=='after'?' selected="selected"':''); ?>>after</option></select> those with a priority.</p>
<p><input type="submit" class="submit" value="Update priority order"></p>
</form>
<?php } ?>