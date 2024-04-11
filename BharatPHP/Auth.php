<?php

namespace BharatPHP;

use App\Models\Users;
use BharatPHP\Session;
use BharatPHP\Hash;

class Auth
{

	/**
	 * The current user of the application.
	 *
	 * @var array
	 */
	protected static $user;

	/**
	 * The key used when storing the user ID in the session.
	 *
	 * @var string
	 */
	const user_key = 'bp57us24hv_';

	/**
	 * The key used when setting the "remember me" cookie.
	 *
	 * @var string
	 */
	const remember_key = 'bp78567rem96_';

	/**
	 * Determine if the user of the application is not logged in.
	 *
	 * This method is the inverse of the "check" method.
	 *
	 * @return bool
	 */
	public static function guest()
	{
		return !static::check();
	}

	/**
	 * Determine if the user of the application is logged in.
	 *
	 * @return bool
	 */
	public static function check()
	{
		return !is_null(static::user());
	}

	/**
	 * Get the current user of the application.
	 *
	 * This method will call the "user" closure in the auth configuration file.
	 * If the user is not authenticated, null will be returned by the methd.
	 *
	 * If no user exists in the session, the method will check for a "remember me"
	 * cookie and attempt to login the user based on the value of that cookie.
	 *
	 * <code>
	 *		// Get the current user of the application
	 *		$user = Auth::user();
	 *
	 *		// Access a property on the current user of the application
	 *		$email = Auth::user()->email;
	 * </code>
	 *
	 * @return array
	 */
	public static function user()
	{
		if (!is_null(static::$user)) return static::$user;

		$id = Session::get(Auth::user_key);


		// static::$user = call_user_func(Config::get('auth.user'), $id);
		if (!is_null($id) and filter_var($id, FILTER_VALIDATE_INT) !== false) {
			return Users::getUserByUserID($id);
		}

		// If the user was not found in the database, but a "remember me" cookie
		// exists, we will attempt to recall the user based on the cookie value.
		// Since all cookies contain a fingerprint hash verifying that the have
		// not been modified on the client, we should be able to trust it.
		$recaller = Cookie::get(Auth::remember_key);

		if (is_null(static::$user) and !is_null($recaller)) {
			static::$user = static::recall($recaller);
		}

		return static::$user;
	}

	/**
	 * Attempt to login a user based on a long-lived "remember me" cookie.
	 *
	 * @param  string  $recaller
	 * @return mixed
	 */
	protected static function recall($recaller)
	{
		// When the "remember me" cookie is stored, it is encrypted and contains the
		// user's ID and a long, random string. The ID and string are separated by
		// a pipe character. Since we exploded the decrypted string, we can just
		// pass the first item in the array to the user Closure.
		// $recaller = explode('|', Crypter::decrypt($recaller));
		$recaller = explode('|', decryptString($recaller));

		if (!is_null($user = call_user_func(Config::get('auth.user'), $recaller[0]))) {
			static::login($user);

			return $user;
		}
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * If the credentials are valid, the user will be logged into the application
	 * and their user ID will be stored in the session via the "login" method.
	 *
	 * The user may also be "remembered", which will keep the user logged into the
	 * application for one year or until they logout. The user is remembered via
	 * an encrypted cookie.
	 *
	 * @param  string  $username
	 * @param  string  $password
	 * @param  bool    $remember
	 * @return bool
	 */
	public static function attempt($username, $password = null, $remember = false)
	{
		$config = Config::get('auth');

		if (config('auth.username') == 'email') {
			// $user = Users::getUserByEmailAndPassword($post_data['email'], $password);
			$user = Users::getUserByEmail($username);
		} else {
			$user = Users::getUserByUsername($username);
		}
		// $user = User::where($config['username'], '=', $username)->first();

		if (isset($user['password'])) {
			$is_password_correct = hashBcryptVerify($password, $user['password']);

			if ($is_password_correct) {
				// if (!is_null($user) and Hash::check($password, $user->password)) {
				// 	return $user;
				// }
				// $user = call_user_func($config['attempt'], $username, $password, $config);

				if (!is_null($user)) {
					static::login($user['id'], $remember);

					return true;
				}
			}
		}




		return false;
	}

	/**
	 * Log a user into the application.
	 *
	 * An object representing the user or an integer user ID may be given to the method.
	 * If an object is given, the object must have an "id" property containing the user
	 * ID as it is stored in the database.
	 *
	 * <code>
	 *		// Login a user by passing a user object
	 *		Auth::login($user);
	 *
	 *		// Login the user with an ID of 15
	 *		Auth::login(15);
	 *
	 *		// Login a user and set a "remember me" cookie
	 *		Auth::login($user, true);
	 * </code>
	 *
	 * @param  object|int  $user
	 * @param  bool        $remember
	 * @return void
	 */
	public static function login($user, $remember = false)
	{
		$id = (is_object($user)) ? $user->id : (int) $user;

		if ($remember) static::remember($id);

		Session::put(Auth::user_key, $id);
	}

	/**
	 * Set a cookie so that users are "remembered" and don't need to login.
	 *
	 * @param  string  $id
	 * @return void
	 */
	protected static function remember($id)
	{
		// $recaller = Crypter::encrypt($id . '|' . Str::random(40));
		$recaller = encryptString($id . '|' . Str::random(40));

		// This method assumes the "remember me" cookie should have the same
		// configuration as the session cookie. Since this cookie, like the
		// session cookie, should be kept very secure, it's probably safe
		// to assume the settings are the same.
		$config = Config::get('session');

		extract($config, EXTR_SKIP);

		Cookie::forever(Auth::remember_key, $recaller, $path, $domain, $secure, $httponly);
	}

	/**
	 * Log the current user out of the application.
	 *
	 * The "logout" closure in the authenciation configuration file will be
	 * called. All authentication cookies will be deleted and the user ID
	 * will be removed from the session.
	 *
	 * @return void
	 */
	public static function logout()
	{
		// call_user_func(Config::get('auth.logout'), static::user());

		static::$user = null;

		$config = Config::get('session');

		extract($config, EXTR_SKIP);

		// When forgetting the cookie, we need to also pass in the path and
		// domain that would have been used when the cookie was originally
		// set by the framework, otherwise it will not be deleted.
		Cookie::forget(Auth::user_key, $path, $domain, $secure);

		Cookie::forget(Auth::remember_key, $path, $domain, $secure);

		Session::forget(Auth::user_key);
	}

	public static function getLoggedInUserName()
	{
		$logged_in_userID = Session::get('loggedInUserID');
		if (is_null($logged_in_userID)) {
			return '';
		}


		$user = Users::getUserByUserID($logged_in_userID);

		if (!isset($user['name'])) {
			return '';
		}

		return $user['name'];
	}

	public static function isUserAdmin()
	{
		$logged_in_userID = Session::get('loggedInUserID');
		if (is_null($logged_in_userID)) {
			return false;
		}


		$user = Users::getUserByUserID($logged_in_userID);

		if (isset($user['user_type'])) {
			if ($user['user_type'] == 'ADMIN') {
				return true;
			}
		}

		//        if (!isset($user['user_type'])) {
		//            return '';
		//        }

		return false;
	}

	public static function isUserJury()
	{
		$logged_in_userID = Session::get('loggedInUserID');
		if (is_null($logged_in_userID)) {
			return false;
		}


		$user = Users::getUserByUserID($logged_in_userID);

		if (isset($user['user_type'])) {
			if ($user['user_type'] == 'JURY') {
				return true;
			}
		}

		//        if (!isset($user['user_type'])) {
		//            return '';
		//        }

		return false;
	}

	public static function getLoggedInUserNamex()
	{

		if (!isset($_COOKIE['ocld']) || empty($_COOKIE['ocld'])) {
			return false;
		}

		$login_cookie_data = json_decode(Utilities::decrypt($_COOKIE['ocld']), 1);

		if (!isset($login_cookie_data['data']['name'])) {
			return '';
		}
		return $login_cookie_data['data']['name'];
	}

	public static function getLoggedInUserID()
	{
		$logged_in_userID = Session::get('loggedInUserID');
		if (is_null($logged_in_userID)) {
			return null;
		}

		return $logged_in_userID;
	}

	public static function getLoggedInUserIDx()
	{

		if (!isset($_COOKIE['ocld']) || empty($_COOKIE['ocld'])) {
			return false;
		}

		$login_cookie_data = json_decode(Utilities::decrypt($_COOKIE['ocld']), 1);

		if (!isset($login_cookie_data['data']['user_id'])) {
			return '';
		}
		return $login_cookie_data['data']['user_id'];
	}

	public static function isLoggedIn()
	{

		if (!isset($_COOKIE['ocld']) || empty($_COOKIE['ocld'])) {
			return false;
		}

		$login_cookie_data = json_decode(Utilities::decrypt($_COOKIE['ocld']), 1);

		if (!isset($login_cookie_data['exp'])) {
			return false;
		}

		if (time() > $login_cookie_data['exp']) {
			return false;
		}


		$login_cookie_data['exp'] = $login_cookie_data['exp'] + 3600;

		$login_cookie_data = Utilities::encrypt(json_encode($login_cookie_data));

		setcookie('ocld', $login_cookie_data, time() + (86400 * 30), "/"); // 86400 = 1 day
		// setcookie('ocld',  $login_cookie_data, time() + (3600), "/"); // 1 hour

		return true;
	}

	public static function getIpAddress()
	{
		$ipAddress = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			// to get shared ISP IP address
			$ipAddress = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// check for IPs passing through proxy servers
			// check if multiple IP addresses are set and take the first one
			$ipAddressList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			foreach ($ipAddressList as $ip) {
				if (!empty($ip)) {
					// if you prefer, you can check for valid IP address here
					$ipAddress = $ip;
					break;
				}
			}
		} else if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
			$ipAddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
			$ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if (!empty($_SERVER['HTTP_FORWARDED'])) {
			$ipAddress = $_SERVER['HTTP_FORWARDED'];
		} else if (!empty($_SERVER['REMOTE_ADDR'])) {
			$ipAddress = $_SERVER['REMOTE_ADDR'];
		}
		return $ipAddress;
	}

	public static function encrypt($string, $key = 5)
	{
		$result = '';
		for ($i = 0, $k = strlen($string); $i < $k; $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return base64_encode($result);
	}

	public static function decrypt($string, $key = 5)
	{
		$result = '';
		$string = base64_decode($string);
		for ($i = 0, $k = strlen($string); $i < $k; $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
		}
		return $result;
	}
}
