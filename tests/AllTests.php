<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL ^ E_DEPRECATED);

require_once(dirname(dirname(__FILE__)) . '/bootstrap.php');

if (defined('E_DEPRECATED')) {
	error_reporting(E_ALL ^ E_DEPRECATED);
} else {
	error_reporting(E_ALL);
}

require_once dirname(__FILE__) . '/simpletest/unit_tester.php';
require_once dirname(__FILE__) . '/simpletest/mock_objects.php';
require_once dirname(__FILE__) . '/simpletest/collector.php';

class AllTests extends TestSuite {
	function AllTests() {
		$this->TestSuite('All tests');
		$this->addTestClass('Tests_Gwilym_ArrayObject_FirstRead');
		$this->addTestClass('Tests_Gwilym_ArrayObject_ReadOnly');
		$this->addTestClass('Tests_Gwilym_Autoloader');
		$this->addTestClass('Tests_Gwilym_Event');
		$this->addTestClass('Tests_Gwilym_KeyStore_File');
		$this->addTestClass('Tests_Gwilym_KeyStore_Mongodb');
		$this->addTestClass('Tests_Gwilym_Request');
		$this->addTestClass('Tests_Gwilym_Router_Standard_Reverse');
		$this->addTestClass('Tests_Gwilym_String');
		$this->addTestClass('Tests_Gwilym_UriParser_Fixed');
		$this->addTestClass('Tests_Gwilym_UriParser_Guess');
	}
}

Gwilym_Autoloader::addPath(dirname(__FILE__));
$suite = new AllTests();

if (@$_GET['coverage']) {
	$filter = PHP_CodeCoverage_Filter::getInstance();
	$filter->addDirectoryToWhitelist(dirname(dirname(__FILE__)) . '/lib/Gwilym');
	$filter->addDirectoryToBlacklist(dirname(__FILE__));

	$coverage = new PHP_CodeCoverage();
	$coverage->start('UnitTests');
}

$suite->run(new DefaultReporter());

if (@$_GET['coverage']) {
	$coverage->stop();

	$report = dirname(__FILE__) . '/coverage/' . date('Y_m_d-H_i_s');
	mkdir($report);

	$writer = new PHP_CodeCoverage_Report_HTML;
	$writer->process($coverage, $report);

	$report = dirname(__FILE__) . '/coverage/current';
	@mkdir($report);
	$writer->process($coverage, $report);
}
