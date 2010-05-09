<?php

require_once(dirname(dirname(__FILE__)) . '/init.php');

error_reporting(E_ALL ^ E_DEPRECATED);

require_once('simpletest/autorun.php');

Gwilym_Autoloader::addPath(dirname(__FILE__));

class AllTests extends TestSuite {
	function AllTests() {
		$this->TestSuite('All tests');
		$this->addTestClass('Tests_Gwilym_Router_Standard_Reverse');
	}
}
