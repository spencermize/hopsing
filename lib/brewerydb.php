<?php 
require_once('cache.php');

function brewerydb_lookup($endpoint){
	$BREWERYDB_API_KEY = 'fb8aea017e780a3738078a0308ddc2f9';
	$BREWERYDB_BASEURL = 'http://api.brewerydb.com/v2/';
	$BREWERYDB_FORMAT = 'json';
	$CACHE_FOLDER = "caches/";

	// Make sure db endpoints have been init
	if(R::count('endpoint')===0){
		init_brewerydb_endpoints();
	}
	$endpoint = R::find('endpoint','url = ?',array($endpoint));
	$endpoint = array_shift($endpoint);
	if(!$endpoint){
		return "No route found!";
	}
	$lookup = $endpoint->url;
	$cache = $endpoint->cachename;
	$args = array(
		"key" => $BREWERYDB_API_KEY,
		"format" => $BREWERYDB_FORMAT
	);

	$query = http_build_query($args);
	$url = $BREWERYDB_BASEURL . $lookup . "?" . $query;

	if(!file_exists($CACHE_FOLDER)){
		mkdir($CACHE_FOLDER);
	}
	return json_decode(get_content($CACHE_FOLDER . $cache,$url));
}
function init_brewerydb_endpoints(){
	$urls = array('styles');
	foreach($urls as $url){
		$bean = R::dispense('endpoint'); 
		$bean->url = $url;
		$bean->cachename = $url . '.json';
		$endpoints[] = 	$bean;
	}
	R::storeAll($endpoints);
}
?>