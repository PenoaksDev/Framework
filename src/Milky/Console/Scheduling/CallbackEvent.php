<?php namespace Milky\Console\Scheduling;

use InvalidArgumentException;
use LogicException;
use Milky\Binding\UniversalBuilder;

class CallbackEvent extends Event
{
	/**
	 * The callback to call.
	 *
	 * @var string
	 */
	protected $callback;

	/**
	 * The parameters to pass to the method.
	 *
	 * @var array
	 */
	protected $parameters;

	/**
	 * Create a new event instance.
	 *
	 * @param  string $callback
	 * @param  array $parameters
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( $callback, array $parameters = [] )
	{
		if ( !is_string( $callback ) && !is_callable( $callback ) )
			throw new InvalidArgumentException( 'Invalid scheduled callback event. Must be string or callable.' );

		$this->callback = $callback;
		$this->parameters = $parameters;
	}

	/**
	 * Run the given event.
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 */
	public function run()
	{
		if ( $this->description )
			touch( $this->mutexPath() );

		try
		{
			UniversalBuilder::call( $this->callback, $this->parameters )
		}
		finally
		{
			$this->removeMutex();
		}

		parent::callAfterCallbacks();

		return $response;
	}

	/**
	 * Remove the mutex file from disk.
	 *
	 * @return void
	 */
	protected function removeMutex()
	{
		if ( $this->description )
			@unlink( $this->mutexPath() );
	}

	/**
	 * Do not allow the event to overlap each other.
	 *
	 * @return $this
	 *
	 * @throws \LogicException
	 */
	public function withoutOverlapping()
	{
		if ( !isset( $this->description ) )
			throw new LogicException( "A scheduled event name is required to prevent overlapping. Use the 'name' method before 'withoutOverlapping'." );

		return $this->skip( function ()
		{
			return file_exists( $this->mutexPath() );
		} );
	}

	/**
	 * Get the mutex path for the scheduled command.
	 *
	 * @return string
	 */
	protected function mutexPath()
	{
		return storage_path( 'framework/schedule-' . sha1( $this->description ) );
	}

	/**
	 * Get the summary of the event for display.
	 *
	 * @return string
	 */
	public function getSummaryForDisplay()
	{
		if ( is_string( $this->description ) )
			return $this->description;

		return is_string( $this->callback ) ? $this->callback : 'Closure';
	}
}
