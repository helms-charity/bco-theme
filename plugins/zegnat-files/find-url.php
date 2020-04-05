<?php
# Version 1
# Taken from GetSimple version 2.01
if (!function_exists('find_url')) {
	function find_url($slug, $parent) {
		global $PRETTYURLS;
		global $SITEURL;
		if ($PRETTYURLS == '1') {
			if ($slug != 'index'){
				if ($parent != '') {$parent = tsl($parent); }
				$url = $SITEURL . @$parent . $slug;
			} else {
				$url = $SITEURL;
			}
		} else {
			if ($slug != 'index'){
				$url = $SITEURL .'index.php?id='.$slug;
			} else {
				$url = $SITEURL;
			}
		}
		return $url;
	}
}
?>