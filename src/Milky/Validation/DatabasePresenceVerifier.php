<?php namespace Milky\Validation;

use Milky\Database\ConnectionResolverInterface;
use Milky\Database\Query\Builder;
use Milky\Helpers\Str;

class DatabasePresenceVerifier implements PresenceVerifierInterface
{
	/**
	 * The database connection instance.
	 *
	 * @var ConnectionResolverInterface
	 */
	protected $db;

	/**
	 * The database connection to use.
	 *
	 * @var string
	 */
	protected $connection = null;

	/**
	 * Create a new database presence verifier.
	 *
	 * @param  ConnectionResolverInterface $db
	 * @return void
	 */
	public function __construct( ConnectionResolverInterface $db )
	{
		$this->db = $db;
	}

	/**
	 * Count the number of objects in a collection having the given value.
	 *
	 * @param  string $collection
	 * @param  string $column
	 * @param  string $value
	 * @param  int $excludeId
	 * @param  string $idColumn
	 * @param  array $extra
	 * @return int
	 */
	public function getCount( $collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [] )
	{
		$query = $this->table( $collection )->where( $column, '=', $value );

		if ( !is_null( $excludeId ) && $excludeId != 'NULL' )
		{
			$query->where( $idColumn ?: 'id', '<>', $excludeId );
		}

		foreach ( $extra as $key => $extraValue )
		{
			$this->addWhere( $query, $key, $extraValue );
		}

		return $query->count();
	}

	/**
	 * Count the number of objects in a collection with the given values.
	 *
	 * @param  string $collection
	 * @param  string $column
	 * @param  array $values
	 * @param  array $extra
	 * @return int
	 */
	public function getMultiCount( $collection, $column, array $values, array $extra = [] )
	{
		$query = $this->table( $collection )->whereIn( $column, $values );

		foreach ( $extra as $key => $extraValue )
		{
			$this->addWhere( $query, $key, $extraValue );
		}

		return $query->count();
	}

	/**
	 * Add a "where" clause to the given query.
	 *
	 * @param  Builder $query
	 * @param  string $key
	 * @param  string $extraValue
	 * @return void
	 */
	protected function addWhere( $query, $key, $extraValue )
	{
		if ( $extraValue === 'NULL' )
		{
			$query->whereNull( $key );
		}
		elseif ( $extraValue === 'NOT_NULL' )
		{
			$query->whereNotNull( $key );
		}
		elseif ( Str::startsWith( $extraValue, '!' ) )
		{
			$query->where( $key, '!=', mb_substr( $extraValue, 1 ) );
		}
		else
		{
			$query->where( $key, $extraValue );
		}
	}

	/**
	 * Get a query builder for the given table.
	 *
	 * @param  string $table
	 * @return Builder
	 */
	protected function table( $table )
	{
		return $this->db->connection( $this->connection )->table( $table )->useWritePdo();
	}

	/**
	 * Set the connection to be used.
	 *
	 * @param  string $connection
	 * @return void
	 */
	public function setConnection( $connection )
	{
		$this->connection = $connection;
	}
}
