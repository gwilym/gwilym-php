<?php

require_once(dirname(__FILE__) . '/bootstrap.php');

$request = new Gwilym_Request;

// fixed routes directly from / to Index
$fixed = new Gwilym_Router_Fixed;
$fixed->addFixedRoute('', 'Controller_Index');
$fixed->addFixedRoute('/', 'Controller_Index');
$fixed->addFixedRoute('/index.php', 'Controller_Index');
$request->addRouter($fixed);

// fallback for dynamic routes using lazy router load
$request->addRouter('Gwilym_Router_Standard_Reverse');

// go
$request->handle();

echo '<pre>';
var_dump(1000 * (microtime(true) - GWILYM_START_TIME));
var_dump(memory_get_usage());
var_dump(memory_get_peak_usage());
print_r(get_included_files());
