<?php

class Gwilym_Request_Test extends Gwilym_Request
{
	public function routers ()
	{
		return $this->_routers;
	}
}

class TestController_Transfer_From extends Gwilym_Controller
{
	public function action ()
	{
		Tests_Gwilym_Request::$testData = 2;
		$this->request()->transfer('TestController_Transfer_To');
	}
}

class TestController_Transfer_FromUri extends Gwilym_Controller
{
	public function action ()
	{
		Tests_Gwilym_Request::$testData = 2;
		$this->request()->transfer('to');
	}
}

class TestController_Transfer_To extends Gwilym_Controller
{
	public function action ()
	{
		Tests_Gwilym_Request::$testData = 3;
		$this->view(new Gwilym_View_None);
	}
}

class TestController_Transfer_Loop extends Gwilym_Controller
{
	public function action ()
	{
		Tests_Gwilym_Request::$testData++;
		$this->request()->transfer('TestController_Transfer_Loop');
	}
}

class Tests_Gwilym_Request extends UnitTestCase
{
	public static $testData;

	public function setUp ()
	{
		self::$testData = null;
	}

	public function testRequestStartsWithNoRouters ()
	{
		$request = new Gwilym_Request_Test;
		$routers = $request->routers();
		$this->assertTrue(empty($routers));
	}

	public function testAddRouterAsInstance ()
	{
		$request = new Gwilym_Request_Test;
		$router = new Gwilym_Router_Fixed;
		$request->addRouter($router);
		$routers = $request->routers();
		$this->assertIdentical($router, $routers[0]);
	}

	public function testAddRouterAsString ()
	{
		$request = new Gwilym_Request_Test('/');
		$request->addRouter('Gwilym_Router_Fixed');
		$this->assertFalse($request->handle());
		$routers = $request->routers();
		$this->assertIsA($routers[0], 'Gwilym_Router_Fixed');
	}

	public function testTransferToController ()
	{
		$router = new Gwilym_Router_Fixed;
		$router->addFixedRoute('', 'TestController_Transfer_From');

		$request = new Gwilym_Request_Test('');
		$request->addRouter($router);

		self::$testData = 1;
		$request->handle();
		$this->assertEqual(3, self::$testData);
	}

	public function testTransferToUri ()
	{
		$router = new Gwilym_Router_Fixed;
		$router->addFixedRoute('from', 'TestController_Transfer_FromUri');
		$router->addFixedRoute('to', 'TestController_Transfer_To');

		$request = new Gwilym_Request_Test('from');
		$request->addRouter($router);

		self::$testData = 1;
		$request->handle();
		$this->assertEqual(3, self::$testData);
	}

	public function testTransferLimit ()
	{
		$router = new Gwilym_Router_Fixed;
		$router->addFixedRoute('', 'TestController_Transfer_Loop');

		$request = new Gwilym_Request_Test('');
		$request->addRouter($router);

		self::$testData = 0;
		try
		{
			$request->handle();
			$this->fail('expected Gwilym_Request_Exception_TooManyTransfers');
		}
		catch (Gwilym_Request_Exception_TooManyTransfers $exception) { }

		$this->assertEqual(Gwilym_Request::MAX_NESTING_DEPTH, self::$testData);
	}

	public function testRouteToUri ()
	{
		$request = new Gwilym_Request_Test('/');
		$request->addRouter('Gwilym_Router_Fixed');
		$request->addRouter('Gwilym_Router_Standard_Reverse');
		$route = $request->route('Controller_Admin_Product');
		$this->assertEqual('/admin/product', $request->routeToUri($route));
	}

	public function testRouteToUriWithNoSolution ()
	{
		$request = new Gwilym_Request_Test('/');
		$request->addRouter('Gwilym_Router_Fixed');
		$route = $request->route('Controller_Admin_Product');
		$this->assertFalse($request->routeToUri($route));
	}

	public function testRequestGuessesUriByDefault ()
	{
		$request = new Gwilym_Request_Test();
		$parser = $request->uriParser();
		$this->assertIsA($parser, 'Gwilym_UriParser_Guess');
	}
}
