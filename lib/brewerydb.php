<?php 
require_once('cache.php');

function brewerydb_lookup($query,$args = array()){
	$BREWERYDB_API_KEY = 'fb8aea017e780a3738078a0308ddc2f9';
	$BREWERYDB_BASEURL = 'http://api.brewerydb.com/v2/';
	$BREWERYDB_FORMAT = 'json';
	$CACHE_FOLDER = "caches/";

	// Make sure there's a cache folder
	if(!file_exists($CACHE_FOLDER)){
		mkdir($CACHE_FOLDER);
	}
	// Make sure db endpoints have been init
	if(R::count('endpoint')===0){
		init_brewerydb_endpoints();
	}
	// Find the endpoint object
	$endpoint = R::find('endpoint','url = ?',array($query));
	$endpoint = array_shift($endpoint);
	if(!$endpoint){
		$endpoint = add_brewerydb_endpoint($query . "___" . $query = http_build_query($args));
	}
	$lookup = str_replace("___","?",$endpoint->url);
	$cache = $endpoint->cachename;
	$args = array_merge(array(
		"key" => $BREWERYDB_API_KEY,
		"format" => $BREWERYDB_FORMAT
	),$args);

	$query = http_build_query($args);
	$del = strpos($lookup,"?")>-1 ? "&" : "?";
	$url = $BREWERYDB_BASEURL . $lookup . $del . $query;

	return json_decode(get_content($CACHE_FOLDER . $cache,$url));
}
function init_brewerydb_endpoints(){
	$urls = array('styles');
	foreach($urls as $url){
		$endpoints[] = add_brewerydb_endpoint($url);
	}
	R::storeAll($endpoints);
}
function add_brewerydb_endpoint($url,$save = false){
	$bean = R::dispense('endpoint'); 
	$bean->url = $url;
	$bean->cachename = $url . '.json';	
	R::store($bean);
	return $bean;
}
?>