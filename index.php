<?php

// include the Gwilym framework and tell it where the public request origin is
define('GWILYM_PUBLIC_DIR', dirname(__FILE__));
require_once(dirname(__FILE__) . '/lib/bootstrap.php');

// create a new request handler
$request = new Gwilym_Request;

/*
// set up fixed routes directly from / to Index -- quickest way of loading a home page directly from known urls
$fixed = new Gwilym_Router_Fixed;
$fixed->addFixedRoute('', 'Controller_Index');
$fixed->addFixedRoute('/', 'Controller_Index');
$fixed->addFixedRoute('/index.php', 'Controller_Index');
$request->addRouter($fixed);

// set up fallback for dynamic routes using lazy router load
$request->addRouter('Gwilym_Router_Default');
*/

$router = new Gwilym_Router_Restful;
$request->addRouter($router);

// begin handling the request
$request->handle();
