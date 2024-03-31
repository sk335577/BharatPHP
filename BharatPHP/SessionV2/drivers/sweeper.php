<?php namespace BharatPHP\Session\Drivers;

interface Sweeper {

	/**
	 * Delete all expired sessions from persistent storage.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration);

}