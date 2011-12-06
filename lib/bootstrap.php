<?php

// this file should always be at the root of the Gwilym library of files

if (!defined('GWILYM_PUBLIC_DIR')) {
    throw new Exception('GWILYM_PUBLIC_DIR must be defined before bootstrap.php is included.');
}

// timestamp from when the framework started up
define('GWILYM_START_TIME', microtime(true));

// where to find all of the core Gwilym_* framework classes
define('GWILYM_LIB_DIR', dirname(__FILE__));

// parent folder of GWILYM_LIB_DIR - mainly used for autoloading and calculating other folders if not already defined
define('GWILYM_LIB_PARENT_DIR', dirname(GWILYM_LIB_DIR));

// where to find all of the core app-specific classes
if (!defined('GWILYM_APP_DIR')) {
    // by default this is a sibling of the lib dir
    define('GWILYM_APP_DIR', GWILYM_LIB_PARENT_DIR . '/app');
}

// where to find local overrides or extensions for either the framework or app
if (!defined('GWILYM_LOCAL_DIR')) {
    // by default this is a sibling of the lib dir
    define('GWILYM_LOCAL_DIR', GWILYM_LIB_PARENT_DIR . '/local');
}

// where to find / store all writable files; volatile or not
if (!defined('GWILYM_VAR_DIR')) {
    // by default this is a sibling of the lib dir
    define('GWILYM_VAR_DIR', GWILYM_LIB_PARENT_DIR . '/var');
}

// where to find the volatile cache files
if (!defined('GWILYM_CACHE_DIR')) {
    // by default this is a child of the var dir
    define('GWILYM_CACHE_DIR', GWILYM_VAR_DIR . '/cache');
}

require_once(GWILYM_LIB_DIR . '/Gwilym/Autoloader.php');
Gwilym_Autoloader::init();
Gwilym_Autoloader::addPath(GWILYM_LOCAL_DIR);
Gwilym_Autoloader::addPath(GWILYM_APP_DIR);
Gwilym_Autoloader::addPath(GWILYM_LIB_DIR);
