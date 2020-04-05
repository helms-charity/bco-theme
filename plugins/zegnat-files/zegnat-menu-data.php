<?php
# Version 1
# http://get-simple.info/forum/viewtopic.php?id=544
if (!function_exists('zegnat_menu_data')) {
	function zegnat_menu_data($xml=true, $elements=array(), $overwrite=false) {
		global $PRETTYURLS;
		global $SITEURL;
		$path = GSDATAPAGESPATH;
		$defaultElements = array('menuOrder','menu','parent','title','slug','pubDate','url');
		if ($overwrite==true) { $elements = (array)$elements; }
		else { $elements = array_merge($defaultElements, array_diff((array)$elements, $defaultElements)); }

		$dir_handle = @opendir($path) or die("Unable to open $path");
		$filenames = array();
		while ($filename = readdir($dir_handle)) {
			$filenames[] = $filename;
		}
		closedir($dir_handle);

		$count = 0;
		foreach ($filenames as $file) {
			if ($file[0]=='.' || is_dir($path.$file)) continue;
			$data = getXML($path.$file);
			if ($data->private=='Y'||$data->menuStatus!='Y') continue;
			foreach ($elements as $item) {
				if ($item=='slug') { $page[$count]['slug'] = @(string)$data->url; }
				elseif ($item=='url') { $page[$count]['url'] = @(string)find_url(@(string)$data->url,@(string)$data->parent); }
				elseif ($item=='title' && (string)$data->title=='') { $page[$count]['title'] = @(string)$data->menu; }
				elseif ($item=='menu' && (string)$data->menu=='') { $page[$count]['menu'] = @(string)$data->title; }
				else { $page[$count][$item] = @(string)$data->{$item}; }
			}
			$count++;
		}
		if (!$xml) return @$page;
		$xmlOutput = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
		foreach ($page as $item) {
			$el = $xmlOutput->addChild('item');
			foreach ($item as $element => $value) {
				$l = $el->addChild($element);
				$l->addCData($value);
			}
		}
		return $xmlOutput;
	}
}
?>