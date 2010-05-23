<?php

class Tests_Gwilym_String extends UnitTestCase
{
	public function testUnltrim ()
	{
		$data = array(
			'/' => array('', '/'),
			'/' => array('/', '/'),
			'/a' => array('a', '/'),
			'/a' => array('/a', '/'),
			'/ a' => array(' a', '/'),
		);

		foreach ($data as $expected => $input)
		{
			$actual = Gwilym_String::unltrim($input[0], $input[1]);
			$this->assertEqual($expected, $actual);
		}
	}
}
