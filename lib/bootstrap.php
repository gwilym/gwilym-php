<?php

// this file should always be at the root of your site(s) structure(s)
// its file system location will be used to determine if it is running at the doc-root level, or in a sub-directory, no matter what the URI is

define('GWILYM_START_TIME', microtime(true));

define('GWILYM_BASE_DIR', dirname(__FILE__));

define('GWILYM_LIB_DIR', GWILYM_BASE_DIR . '/lib');
define('GWILYM_LOCAL_DIR', GWILYM_BASE_DIR . '/local');

define('GWILYM_APP_DIR', GWILYM_BASE_DIR . '/app');
define('GWILYM_VAR_DIR', GWILYM_APP_DIR . '/var');
define('GWILYM_CACHE_DIR', GWILYM_VAR_DIR . '/cache');

require_once(GWILYM_LIB_DIR . '/Gwilym/Autoloader.php');
Gwilym_Autoloader::init();
Gwilym_Autoloader::addPath(GWILYM_LOCAL_DIR);
Gwilym_Autoloader::addPath(GWILYM_APP_DIR);
Gwilym_Autoloader::addPath(GWILYM_LIB_DIR);
