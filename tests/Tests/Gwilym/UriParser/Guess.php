<?php

class Tests_Gwilym_UriParser_Guess extends UnitTestCase
{
	public function testRequestUri ()
	{
		$parser = new Gwilym_UriParser_Guess();
		$this->assertEqual($_SERVER['REQUEST_URI'], $parser->requestUri());
	}

	public function testRequestBaseDir ()
	{
		$parser = new Gwilym_UriParser_Guess();
		$this->assertEqual(GWILYM_BASE_DIR, $parser->requestBaseDir());
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

		$parser = new Gwilym_UriParser_Guess();

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

		foreach ($data as $test)
		{
			$parser->requestUri($test['inputRequest']);
			$parser->requestBaseDir($test['inputBase']);
			$this->assertEqual($test['parseBase'], $parser->base());
			$this->assertEqual($test['parseDocroot'], $parser->docroot());
			$this->assertEqual($test['parseUri'], $parser->uri());
		}
	}
}
