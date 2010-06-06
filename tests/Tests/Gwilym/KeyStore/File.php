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

	public function testKeyToFilenameInvalidKeyName ()
	{
		$this->expectException('Gwilym_KeyStore_File_Exception_InvalidKeyName');
		$this->ks->set('/', '');
	}
}
