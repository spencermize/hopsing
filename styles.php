<?php
require "vendor/leafo/scssphp/scss.inc.php";

$scss = new scssc();
$scss->setFormatter("scss_formatter_compressed");

$server = new scss_server("styles", null, $scss);
$server->serve();

?>