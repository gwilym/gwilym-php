<?php

class Tests_Gwilym_KeyStore_File extends UnitTestCase
{
	public function testPatternToRegularExpresion ()
	{
		$data = array(
			'*' => '#^.*$#',
			'?' => '#^.{1}$#',
			'[ab]' => '#^[ab]$#'
		);

		foreach ($data as $pattern => $expected)
		{
			$actual = Gwilym_KeyStore_File::patternToRegularExpresion($pattern);
			$this->assertEqual($expected, $actual);
		}
	}

	public function testTestFilenameAgainstPattern ()
	{
		$fh = fopen(dirname(__FILE__) . '/testFilenameAgainstPattern.csv', 'r');

		$header = fgetcsv($fh);
		array_shift($header);
		$patterns = $header;

		while ($row = fgetcsv($fh))
		{
			$filename = array_shift($row);
			$data[$filename] = $row;
		}
		fclose($fh);

		foreach ($data as $filename => $matches)
		{
			foreach ($patterns as $index => $pattern)
			{
				$expected = (bool)$matches[$index];
				$actual = Gwilym_KeyStore_File::testFilenameAgainstPattern($filename, $pattern);
				$this->assertEqual($expected, $actual);
			}
		}
	}
}
