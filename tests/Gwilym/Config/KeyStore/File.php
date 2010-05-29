<?php

class Gwilym_Config_KeyStore_File extends Gwilym_Config
{
	public static $storage;
}

Gwilym_Config_KeyStore_File::$storage = GWILYM_BASE_DIR . '/tests/var/keystore';
