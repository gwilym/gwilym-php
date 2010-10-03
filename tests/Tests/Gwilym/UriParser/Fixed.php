<?php

class Tests_Gwilym_UriParser_Fixed extends UnitTestCase
{
	public function testConstructor ()
	{
		$parser = new Gwilym_UriParser_Fixed('Base', 'Uri', 'Docroot');
		$this->assertEqual('Base', $parser->getBase());
		$this->assertEqual('Uri', $parser->getUri());
		$this->assertEqual('Docroot', $parser->getDocRoot());
	}
}
