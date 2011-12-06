<?php

class Tests_Gwilym_UriParser_Guess extends UnitTestCase
{
	public function testRequestUri ()
	{
		$parser = new Gwilym_UriParser_Guess();
		$this->assertEqual($_SERVER['REQUEST_URI'], $parser->getRequestUri());
	}

	public function testRequestBaseDir ()
	{
		$parser = new Gwilym_UriParser_Guess();
		$this->assertEqual(GWILYM_PUBLIC_DIR, $parser->getRequestBaseDir());
	}

	public function testParseOnNotWindows ()
	{
		if (Gwilym_PHP::isWindows())
		{
			$this->skip();
		}

		// not written yet
		$this->skip();
	}

	public function testParseOnWindows ()
	{
		if (!Gwilym_PHP::isWindows())
		{
			$this->skip();
			return;
		}

		$data = array();
		
		$data[] = array(
			'inputRequest' => '/gwilym-php/alpha/beta',
			'inputBase' => 'C:/httpd/www/gwilym-php',
			'parseBase' => '/gwilym-php',
			'parseDocroot' => 'C:\httpd\www',
			'parseUri' => '/alpha/beta',
		);

		$data[] = array(
			'inputRequest' => '/gwilym-php/alpha/beta',
			'inputBase' => 'C:\httpd\www\gwilym-php',
			'parseBase' => '/gwilym-php',
			'parseDocroot' => 'C:\httpd\www',
			'parseUri' => '/alpha/beta',
		);

		$data[] = array(
			'inputRequest' => '/gwilym-php',
			'inputBase' => 'C:\httpd\www\gwilym-php',
			'parseBase' => '/gwilym-php',
			'parseDocroot' => 'C:\httpd\www',
			'parseUri' => '/',
		);

		$data[] = array(
			'inputRequest' => '/',
			'inputBase' => 'C:\httpd\www\gwilym-php',
			'parseBase' => '',
			'parseDocroot' => 'C:\httpd\www\gwilym-php',
			'parseUri' => '/',
		);

		foreach ($data as $test) {
			$parser = new Gwilym_UriParser_Guess;
			$parser->setRequestUri($test['inputRequest']);
			$parser->setRequestBaseDir($test['inputBase']);
			$this->assertEqual($test['parseBase'], $parser->getBase());
			$this->assertEqual($test['parseDocroot'], $parser->getDocRoot());
			$this->assertEqual($test['parseUri'], $parser->getUri());
		}
	}
}
