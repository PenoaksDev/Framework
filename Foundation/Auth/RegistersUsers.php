<?php

namespace Foundation\Auth;

use Foundation\Http\Request;
use Foundation\Support\Facades\Auth;

trait RegistersUsers
{
	use RedirectsUsers;

	/**
	 * Show the application registration form.
	 *
	 * @return \Foundation\Http\Response
	 */
	public function getRegister()
	{
		return $this->showRegistrationForm();
	}

	/**
	 * Show the application registration form.
	 *
	 * @return \Foundation\Http\Response
	 */
	public function showRegistrationForm()
	{
		if (property_exists($this, 'registerView')) {
			return view($this->registerView);
		}

		return view('auth.register');
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param  \Foundation\Http\Request  $request
	 * @return \Foundation\Http\Response
	 */
	public function postRegister(Request $request)
	{
		return $this->register($request);
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param  \Foundation\Http\Request  $request
	 * @return \Foundation\Http\Response
	 */
	public function register(Request $request)
	{
		$validator = $this->validator($request->all());

		if ($validator->fails()) {
			$this->throwValidationException(
				$request, $validator
			);
		}

		Auth::guard($this->getGuard())->login($this->create($request->all()));

		return redirect($this->redirectPath());
	}

	/**
	 * Get the guard to be used during registration.
	 *
	 * @return string|null
	 */
	protected function getGuard()
	{
		return property_exists($this, 'guard') ? $this->guard : null;
	}
}