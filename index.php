<?php

require_once('bootstrap.php');

$request = new Gwilym_Request;
$request->addRouter(new Gwilym_Router_Standard_Reverse);
$request->handle();
