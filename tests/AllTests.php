<?php

require_once(dirname(dirname(__FILE__)) . '/bootstrap.php');

error_reporting(E_ALL ^ E_DEPRECATED);

require_once dirname(__FILE__) . '/simpletest/unit_tester.php';
require_once dirname(__FILE__) . '/simpletest/mock_objects.php';
require_once dirname(__FILE__) . '/simpletest/collector.php';

class AllTests extends TestSuite {
	function AllTests() {
		$this->TestSuite('All tests');
		$this->addTestClass('Tests_Gwilym_Event');
		$this->addTestClass('Tests_Gwilym_Request');
		$this->addTestClass('Tests_Gwilym_Router_Standard_Reverse');
	}
}

Gwilym_Autoloader::addPath(dirname(__FILE__));
$suite = new AllTests();

if (@$_GET['coverage']) {
	global $PHPCOVERAGE_HOME;
	$PHPCOVERAGE_HOME = dirname(__FILE__) . '/spikephpcoverage';
	require_once($PHPCOVERAGE_HOME . '/phpcoverage.inc.php');
	require_once($PHPCOVERAGE_HOME . '/CoverageRecorder.php');
	require_once($PHPCOVERAGE_HOME . '/reporter/HtmlCoverageReporter.php');
	$spikePhpCoverageHtmlReporter = new HtmlCoverageReporter("Code Coverage Report", "", "report");

	$include = array(
		GWILYM_BASE_DIR,
	);

	$exclude = array(
		GWILYM_APP_DIR . '/var',
		GWILYM_LIB_DIR . '/Twig',
		dirname(__FILE__),
	);

	$spikePhpCoverageRecorder = new CoverageRecorder($include, $exclude, $spikePhpCoverageHtmlReporter);
	$spikePhpCoverageRecorder->startInstrumentation();
}

$suite->run(new DefaultReporter());

if (@$_GET['coverage']) {
	$spikePhpCoverageRecorder->stopInstrumentation();
	set_time_limit(0);
	$spikePhpCoverageRecorder->generateReport();
	echo '<pre>';
	$spikePhpCoverageHtmlReporter->printTextSummary();
	echo '</pre>';
}
