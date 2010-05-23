<?php

class Gwilym_Request_Test extends Gwilym_Request
{
	public function routers ()
	{
		return $this->_routers;
	}
}

class Tests_Gwilym_Request extends UnitTestCase
{
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
}
