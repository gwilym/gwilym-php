<?php

class Gwilym_Autoloader_Test extends Gwilym_Autoloader
{
	public static function classNameToPathTest ($className)
	{
		return self::classNameToPath($className);
	}
	
	public static function getPaths ()
	{
		return self::$_paths;
	}
}

class Tests_Gwilym_Autoloader extends UnitTestCase
{
	public function testClassNameToPath ()
	{
		$this->assertEqual('/Foo.php', Gwilym_Autoloader_Test::classNameToPathTest('Foo'));
		$this->assertEqual('/Foo/Bar.php', Gwilym_Autoloader_Test::classNameToPathTest('Foo_Bar'));
		$this->assertEqual('/Foo/Bar/Baz.php', Gwilym_Autoloader_Test::classNameToPathTest('Foo_Bar_Baz'));
	}
	
	public function testFindClasses ()
	{
		$expected = array(
			'Tests_Gwilym_Autoloader_Bar_FindClasses',
			'Tests_Gwilym_Autoloader_Foo_FindClasses',
		);
		
		$actual = array();
		$result = Gwilym_Autoloader_Test::findClasses('Tests_Gwilym_Autoloader_*_FindClasses');
		foreach ($result as $class) {
			$actual[] = $class;
		}
		
		$this->assertEqual($expected, $actual);
	}
	
	public function testAddPathLowPriority ()
	{
		// Gwilym_Autoloader is static so it needs to be relative to getPaths
		
		$expected = Gwilym_Autoloader_Test::getPaths();
		$expected[] = 'low';
		
		Gwilym_Autoloader::addPath('low', Gwilym_Autoloader::PRIORITY_LOW);
		$actual = Gwilym_Autoloader_Test::getPaths();

		$this->assertEqual($expected, $actual);
	}
	
	public function testAddPathHighPriority ()
	{
		// Gwilym_Autoloader is static so it needs to be relative to getPaths

		$expected = Gwilym_Autoloader_Test::getPaths();
		array_unshift($expected, 'high');

		Gwilym_Autoloader::addPath('high', Gwilym_Autoloader::PRIORITY_HIGH);
		$actual = Gwilym_Autoloader_Test::getPaths();
		
		$this->assertEqual($expected, $actual);
	}
}
