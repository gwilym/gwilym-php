<?php

require_once(dirname(__FILE__) . '/bootstrap.php');

$request = new Gwilym_Request;
$request->addRouter(new Gwilym_Router_Standard_Reverse);
$request->handle();
