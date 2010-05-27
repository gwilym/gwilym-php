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
