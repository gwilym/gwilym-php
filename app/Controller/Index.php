<?php

class Controller_Index extends Gwilym_Controller
{
	public function action ()
	{
		$file = GWILYM_BASE_DIR . '/admin/scripts/jquery-ui-1.8.2.custom.js';
//		$file = GWILYM_BASE_DIR . '/admin/scripts/jquery.src.js';
//		$file = GWILYM_BASE_DIR . '/admin/scripts/common.js';

//		$parser = new Gwilym_JavaScript_Parser($file);
		echo memory_get_usage() . "\n";
		echo memory_get_peak_usage() . "\n";
		$parser = new Gwilym_JavaScript_Compressor($file, $file . '.compressed.js');
		$parser->start();
		echo memory_get_usage() . "\n";
		echo memory_get_peak_usage() . "\n";
		die();


	}
}
