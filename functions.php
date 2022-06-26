<?php

function getTitle($url) {
	$data = file_get_contents($url);
	$title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;
	return $title;
}

function getReadmeFile($url){
	$readmeurl = $url."/readme.html";
	$content = file_get_contents($readmeurl);
	if(strpos($content, "Welcome. WordPress is a very special project to me.") === false){
		return false;
	}
	return true;
}
function getWordspressVersion($url){
	$feedurl = $url."/feed";
	$content = file_get_contents($feedurl);
	$version_ = preg_match('#<generator>https://wordpress.org/\?v=(.*?)</generator>#ims', $content, $matches) ? $matches[1] : null;
	return $version_;
}
function getWordspressVersionTwo($url){
	$wplinksopmlphp = $url . "/wp-links-opml.php";
	$content = file_get_contents($wplinksopmlphp, false);
	$version_ = preg_match('#generator="WordPress/(.*?)"#ims', $content, $matches) ? $matches[1] : null;
	return $version_;
}
function getlicensefile($url){
	$llurl = $url."/license.txt";
	$licence = file_get_contents($llurl);
	if(strpos($licence, "WordPress - Web publishing software") === false){
		return false;
	}
	return true;
}
?>