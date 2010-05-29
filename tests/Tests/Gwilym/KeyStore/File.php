<?php

class Tests_Gwilym_KeyStore_File extends Tests_Gwilym_KeyStore_Base
{
	public function setUp ()
	{
		$this->ks = new Gwilym_KeyStore_File();
	}

	public function testDirectoryCreateErrorException ()
	{
		$ks = new Gwilym_KeyStore_File("\0");
		$this->expectException('Gwilym_KeyStore_File_Exception_DirectoryCreateError');
		$ks->keyToFilename('x');
	}

	public function testFileNotWritableException ()
	{
		$key = md5(uniqid('', true));
		$file = $this->ks->keyToFilename($key);
		touch($file);
		chmod($file, 775);

		$fail = true;
		try
		{
			$this->ks->set($key, '1');
		}
		catch (Gwilym_KeyStore_File_Exception_FileNotWritable $exception)
		{
			$fail = false;
		}

		chmod($file, 666);
		unlink($file);

		if ($fail)
		{
			$this->fail('Gwilym_KeyStore_File_Exception_FileNotWritable not thrown');
		}
	}

	public function testPatternToRegularExpresion ()
	{
		$data = array(
			'*' => '#^.*$#',
			'?' => '#^.{1}$#',
			'[ab]' => '#^[ab]$#'
		);

		foreach ($data as $pattern => $expected)
		{
			$actual = $this->ks->patternToRegularExpresion($pattern);
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
				$actual = $this->ks->testFilenameAgainstPattern($filename, $pattern);
				$this->assertEqual($expected, $actual);
			}
		}
	}

	public function testKeyToFilenameInvalidKeyName ()
	{
		$this->expectException('Gwilym_KeyStore_File_Exception_InvalidKeyName');
		$this->ks->set('/', '');
	}
}
