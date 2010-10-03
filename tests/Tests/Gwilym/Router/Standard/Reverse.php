<?php

class Tests_Gwilym_Router_Standard_Reverse extends UnitTestCase
{
	public $router;

	protected $_routes;

	public function __construct ()
	{
		$this->router = new Gwilym_Router_Standard_Reverse;

		$default = $this->router->defaultController();

		// input uris to test and expected controller/arg outcomes
		$this->_uris = array(
			'' => array('Controller_' . $default),
			'/' => array('Controller_' . $default),
			'/abstract' => array('Controller_' . $default, array('abstract')),
			'/index.php' => array('Controller_' . $default),
			'/product' => array('Controller_Product_' . $default),
			'/product/' => array('Controller_Product_' . $default),
			'/product/admin/' => array('Controller_Product_Admin_' . $default),
			'/product/admin/edit' => array('Controller_Product_Admin_' . $default, array('edit')),
			'/product/admin/edit/' => array('Controller_Product_Admin_' . $default, array('edit')),
			'/product/admin/delete' => array('Controller_Product_Admin_Delete'),
			'/product/1/' => array('Controller_Product_' . $default, array(1)),
			'/product/1/edit/' => array('Controller_Product_' . $default, array(1, 'edit')),
			'/product/!foo/bar/' => array('Controller_Product_' . $default, array('!foo', 'bar')),
			'/product/foo=bar/' => array('Controller_Product_' . $default, array('foo' => 'bar')),
			'/product/alpha/beta=gamma/delta/' => array('Controller_Product_' . $default, array('alpha', 'beta' => 'gamma', 'delta')),
		);

		// input controller/arg params and expected path outputs
		$this->_routes = array(
			array('/', 'Controller_' . $default),
			array('/abstract', 'Controller_' . $default, array('abstract')),
			array('/product', 'Controller_Product_' . $default),
			array('/product/admin', 'Controller_Product_Admin_' . $default),
			array('/product/admin/edit', 'Controller_Product_Admin_' . $default, array('edit')),
			array('/product/admin/delete', 'Controller_Product_Admin_Delete'),
			array('/product/1', 'Controller_Product_' . $default, array(1)),
			array('/product/1/edit', 'Controller_Product_' . $default, array(1, 'edit')),
			array('/product/%21foo/bar', 'Controller_Product_' . $default, array('!foo', 'bar')),
			array('/product/foo=bar', 'Controller_Product_' . $default, array('foo' => 'bar')),
			array('/product/alpha/beta=gamma/delta', 'Controller_Product_' . $default, array('alpha', 'beta' => 'gamma', 'delta')),
			array('/product/alpha/delta/beta=gamma', 'Controller_Product_' . $default, array('alpha', 'delta', 'beta' => 'gamma')),
		);
	}

	public function testRouteToUri ()
	{
		foreach ($this->_routes as $route)
		{
			$uri = $route[0];
			$controller = $route[1];

			$request = new Gwilym_Request($uri);
			$request->addRouter($this->router);

			if (isset($route[2]))
			{
				$route = new Gwilym_Route($request, $controller, $route[2]);
			}
			else
			{
				$route = new Gwilym_Route($request, $controller);
			}

			$this->assertEqual($this->router->getUriForRoute($route), $uri);

		}
	}

	public function testGetRouteForRequest ()
	{
		foreach ($this->_uris as $uri => $expected)
		{
			$request = new Gwilym_Request($uri);
			$request->addRouter($this->router);
			$request->setUriParser(new Gwilym_UriParser_Fixed('', $uri));
			$route = $this->router->getRouteForRequest($request);
			$controller = $route->controller();
			$this->assertEqual($controller, $expected[0], "uri '$uri' routed to $controller, should be {$expected[0]}");

			if (isset($expected[1])) {
				$args = $expected[1];
			} else {
				$args = array();
			}

			$this->assertEqual($args, $route->args(), "URI '$uri' generated unexpected arguments. Expected " . var_export($args, true) . " found " . var_export($route->args(), true));
		}
	}
}
