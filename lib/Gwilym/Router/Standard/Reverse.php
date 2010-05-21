<?php

/**
* This class defines a basic uri segment -> controller route with positional and named arguments.
*
* This is a fallback router that will always resolve a controller. To find more specific routes first, add another more specific Router class to the RequestHandler.
*
* Controller detection starts before the first segment containing a character which is invalid as a class name.
*
* A numeric segment will be treated as an argument and so Controller_Product_1 will never be checked, only Controller_Product
*
* A forward slash will always separate controllers and arguments and must be escaped to be used as a character in an argument.
*
* Equal signs can be used to name arguments otherwise unnamed arguments will be added numerically. It is up to your controller code to decide how to handle this.
*
* www.example.com/foo/bar/alpha/beta/gamma
* -> Controller_Foo_Bar_Alpha_Beta_Gamma()->action() or if not exist
* -> Controller_Foo_Bar_Alpha_Beta(array($gamma))->action() or if not exist
* -> Controller_Foo_Bar_Alpha(array($beta, $gamma))->action() or if not exist
* -> etc.
* -> Controller_Index(array($foo, $bar, ...))=>action()
*
* www.example.com/foo/bar/alpha,beta,gamma
* -> Controller_Foo_Bar(array($alpha, $beta, $gamma))->action() or if not exist
* -> Controller_Foo(array($bar, $alpha, $beta, $gamma))->action()
* -> etc.
*
* www.example.com/foo/bar/alpha=beta/gamma/delta
* -> Controller_Foo_Bar(array(0=>$gamma, 1=>delta, $alpha=>$beta))->action() or if not exist
* -> Controller_Foo(array(0=>$bar, 1=>$gamma, 2=>delta, $alpha=>$beta))->action() or if not exist
* -> etc.
*/
class Gwilym_Router_Standard_Reverse extends Gwilym_Router_Standard
{
	public function requestToRoute (Gwilym_Request $request)
	{
		$uri = $request->uri();

		// anything after ? is unwanted and will be in $_GET anyway
		list($uri) = explode('?', $uri, 2);
		$uri = trim($uri, " \t\n\r\0\x0B/");
		$args = array();

		if (!$uri || $uri == 'index.php')
		{
			// straight to default controller
			$controller = 'Controller_' . $this->defaultController();
		}
		else
		{
			$uri = explode('/', $uri);

			// find starting point: controllers can only be valid class names so invalid characters denote end of controller path
			foreach ($uri as $index => $segment)
			{
				if (!is_numeric($segment) && preg_match('#^[a-zA-Z0-9_\x7f-\xff]*$#', $segment))
				{
					// valid
					continue;
				}

				// invalid: crop the uri and pre-fill args
				while (count($uri) > $index)
				{
					array_unshift($args, array_pop($uri));
				}
				break;
			}

			// need to maintain second copy of uri with capitalisation we require while keeping $uri untouched for use as args
			$controllers = $uri;
			array_walk($controllers, array('Gwilym_Router_Standard', 'walkUri'));

			$found = false;
			while (!empty($uri))
			{
				$controller = 'Controller_' . implode('_', $controllers) . '_Index';
				if (class_exists($controller) && Gwilym_Reflection::isClassInstanciable($controller))
				{
					$found = true;
					break;
				}

				$controller = 'Controller_' . implode('_', $controllers);
				if (class_exists($controller) && Gwilym_Reflection::isClassInstanciable($controller))
				{
					$found = true;
					break;
				}

				array_pop($controllers);
				array_unshift($args, array_pop($uri));
			}

			if (!$found)
			{
				$controller = 'Controller_' . $this->defaultController();
			}
		}

		// run through args and split any that are in the format of foo=bar
		// todo: make this more strict, or consider removing it and requiring these types of args be in the GET params instead
		foreach ($args as $index => $value) {
			if (strpos($value, '=') !== false) {
				list($key, $value) = explode('=', $value, 2);
				$args[$key] = $value;
				array_splice($args, $index, 1);
			}
		}

		return new Gwilym_Route($request, $controller, $args);
	}
}
