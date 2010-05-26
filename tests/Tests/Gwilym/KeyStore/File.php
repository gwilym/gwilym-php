<?php

/** @todo move most of the code to a base test class which tests all keystore implementations */
class Tests_Gwilym_KeyStore_File extends UnitTestCase
{
	public $ks;

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

	public function testGetForNonExistantKey ()
	{
		$this->assertFalse($this->ks->exists('not_exist'));
		$this->assertFalse($this->ks->get('not_exist'));
	}

	public function testDeleteAndExists ()
	{
		$this->assertFalse($this->ks->exists('exists'));
		$this->assertTrue($this->ks->set('exists', 1));
		$this->assertTrue($this->ks->exists('exists'));
		$this->assertTrue($this->ks->delete('exists'));
		$this->assertFalse($this->ks->exists('exists'));
	}

	public function testMultiSetGet ()
	{
		$set = array(
			'test_ab' => 'ab',
			'test_bc' => 'bc',
			'test_cd' => 'cd',
			'test_de' => 'de',
		);

		$this->assertTrue($this->ks->multiSet($set));

		$this->assertEqual('ab', $this->ks->get('test_ab'));
		$this->assertEqual('bc', $this->ks->get('test_bc'));
		$this->assertEqual('cd', $this->ks->get('test_cd'));
		$this->assertEqual('de', $this->ks->get('test_de'));

		$get = $this->ks->multiGet('test_??');

		$this->assertEqual($set, $get);

		$this->assertTrue($this->ks->multiDelete('test_??'));

		$get = $this->ks->multiGet('test_??');

		$this->assertEqual(array(), $get);
	}

	public function testIncrement ()
	{
		$this->assertTrue($this->ks->delete('increment_test'));
		$this->assertEqual(1, $this->ks->increment('increment_test'));
		$this->assertEqual(3, $this->ks->increment('increment_test', 2));
		$this->assertEqual(2, $this->ks->decrement('increment_test'));
		$this->assertEqual(-1, $this->ks->decrement('increment_test', 3));
		$this->assertEqual(-3, $this->ks->increment('increment_test', -2));
		$this->assertEqual(-1, $this->ks->decrement('increment_test', -2));
		$this->assertTrue($this->ks->delete('increment_test'));
	}

	public function testAppend ()
	{
		$this->assertTrue($this->ks->delete('append_test'));
		$this->assertEqual(4, $this->ks->append('append_test', 'abcd'));
		$this->assertEqual(8, $this->ks->append('append_test', '1234'));
		$this->assertTrue($this->ks->delete('append_test'));
	}
}
