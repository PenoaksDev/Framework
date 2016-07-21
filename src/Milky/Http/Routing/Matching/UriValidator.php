<?php namespace Milky\Http\Routing\Matching;

use Milky\Http\Request;
use Milky\Http\Routing\Route;

class UriValidator implements ValidatorInterface
{
	/**
	 * Validate a given rule against a route and request.
	 *
	 * @param  Route $route
	 * @param  Request $request
	 * @return bool
	 */
	public function matches( Route $route, Request $request )
	{
		$path = $request->path() == '/' ? '/' : '/' . $request->path();

		return preg_match( $route->getCompiled()->getRegex(), rawurldecode( $path ) );
	}
}