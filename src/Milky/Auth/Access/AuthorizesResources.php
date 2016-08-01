<?php namespace Milky\Auth\Access;

/**
 * The MIT License (MIT)
 * Copyright 2016 Penoaks Publishing Co. <development@penoaks.org>
 *
 * This Source Code is subject to the terms of the MIT License.
 * If a copy of the license was not distributed with this file,
 * You can obtain one at https://opensource.org/licenses/MIT.
 */
trait AuthorizesResources
{
	/**
	 * Authorize a resource action based on the incoming request.
	 *
	 * @param  string $model
	 * @param  string|null $parameter
	 * @param  array $options
	 * @return void
	 */
	public function authorizeResource( $model, $parameter = null, array $options = [] )
	{
		$parameter = $parameter ?: strtolower( class_basename( $model ) );
		foreach ( $this->resourceAbilityMap() as $method => $ability )
		{
			$modelName = in_array( $method, ['index', 'create', 'store'] ) ? $model : $parameter;
			$this->middleware( "can:{$ability},{$modelName}", $options )->only( $method );
		}
	}

	/**
	 * Get the map of resource methods to ability names.
	 *
	 * @return array
	 */
	protected function resourceAbilityMap()
	{
		return [
			'index' => 'view',
			'create' => 'create',
			'store' => 'create',
			'show' => 'view',
			'edit' => 'update',
			'update' => 'update',
			'destroy' => 'delete',
		];
	}
}
