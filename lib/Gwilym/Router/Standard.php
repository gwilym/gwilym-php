<?php

abstract class Gwilym_Router_Standard extends Gwilym_Router
{
	public static function walkController (&$item)
	{
		$item = rawurlencode(strtolower($item));
	}

	public static function walkUri (&$item)
	{
		$item = ucfirst($item);
	}

	public function getUriForRoute (Gwilym_Route $route)
	{
		$uri = explode('_', $route->controller());
		array_shift($uri);

		if (end($uri) == $this->defaultController())
		{
			array_pop($uri);
		}

		array_walk($uri, array('Gwilym_Router_Standard', 'walkController'));

		$uri = implode('/', $uri);

		foreach ($route->args() as $key => $value)
		{
			if (is_int($key))
			{
				$uri .= '/' . rawurlencode($value);
			}
			else
			{
				$uri .= '/' . rawurlencode($key) . '=' . rawurlencode($value);
			}
		}

		return Gwilym_String::unltrim($uri, '/');
	}
}
