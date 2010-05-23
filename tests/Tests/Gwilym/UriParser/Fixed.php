<?php

class Tests_Gwilym_UriParser_Fixed extends UnitTestCase
{
	public function testConstructor ()
	{
		$parser = new Gwilym_UriParser_Fixed('Base', 'Uri', 'Docroot');
		$this->assertEqual('Base', $parser->base());
		$this->assertEqual('Uri', $parser->uri());
		$this->assertEqual('Docroot', $parser->docroot());
	}
}
