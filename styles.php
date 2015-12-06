<?php
require "vendor/leafo/scssphp/scss.inc.php";

use Leafo\ScssPhp\Server;
use Leafo\ScssPhp\Compiler;

$scss = new Compiler();
//$scss->setFormatter("scss_formatter_compressed");

$server = new Server("styles", null, $scss);
$server->serve();

?>