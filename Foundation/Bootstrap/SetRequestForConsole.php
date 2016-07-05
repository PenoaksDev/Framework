<?php
namespace Foundation\Bootstrap;

use Foundation\Framework;
use Foundation\Http\Request;

class SetRequestForConsole implements Bootstrap
{
	/**
	 * Bootstrap the given application.
	 *
	 * @param  \Foundation\Framework  $fw
	 * @return void
	 */
	public function bootstrap(Framework $fw)
	{
		$url = $fw->make('config')->get('app.url', 'http://localhost');

		$fw->bindings->instance('request', Request::create($url, 'GET', [], [], [], $_SERVER));
	}
}
