<?php

namespace BharatPHP\Auth;

use BharatPHP\Config;
use BharatPHP\Session;
use BharatPHP\Events;

class Auth {

    /**
     * Get the current user of the application.
     *
     * If the user is a guest, null should be returned.
     *
     * @param  int  $id
     * @return mixed|null
     */
    public function retrieve($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) !== false) {
            return DB::table(Config::get('auth.table'))->find($id);
        }
    }

    /**
     * Attempt to log a user into the application.
     *
     * @param  array $arguments
     * @return void
     */
    public function attempt($arguments = array()) {
        $user = $this->get_user($arguments);

        // If the credentials match what is in the database we will just
        // log the user into the application and remember them if asked.
        $password = $arguments['password'];

        $password_field = Config::get('auth.password', 'password');

        if (!is_null($user) and Hash::check($password, $user->{$password_field})) {
            return $this->login($user->id, array_get($arguments, 'remember'));
        }

        return false;
    }

    /**
     * Get the user from the database table.
     *
     * @param  array  $arguments
     * @return mixed
     */
    protected function get_user($arguments) {
        $table = Config::get('auth.table');

        return DB::table($table)->where(function ($query) use ($arguments) {
                    $username = Config::get('auth.username');

                    $query->where($username, '=', $arguments['username']);

                    foreach (array_except($arguments, array('username', 'password', 'remember')) as $column => $val) {
                        $query->where($column, '=', $val);
                    }
                })->first();
    }

    /**
     * The user currently being managed by the driver.
     *
     * @var mixed
     */
    public $user;

    /**
     * The current value of the user's token.
     *
     * @var string|null
     */
    public $token;

    /**
     * Create a new login auth driver instance.
     *
     * @return void
     */
    public function __construct() {
        if (Session::started()) {
            $this->token = Session::get($this->token());
        }

        // If a token did not exist in the session for the user, we will attempt
        // to load the value of a "remember me" cookie for the driver, which
        // serves as a long-lived client side authenticator for the user.
        if (is_null($this->token)) {
            $this->token = $this->recall();
        }
    }

    /**
     * Determine if the user of the application is not logged in.
     *
     * This method is the inverse of the "check" method.
     *
     * @return bool
     */
    public function guest() {
        return !$this->check();
    }

    /**
     * Determine if the user is logged in.
     *
     * @return bool
     */
    public function check() {
        return !is_null($this->user());
    }

    /**
     * Get the current user of the application.
     *
     * If the user is a guest, null should be returned.
     *
     * @return mixed|null
     */
    public function user() {
        if (!is_null($this->user))
            return $this->user;

        return $this->user = $this->retrieve($this->token);
    }

    /**
     * Login the user assigned to the given token.
     *
     * The token is typically a numeric ID for the user.
     *
     * @param  string  $token
     * @param  bool    $remember
     * @return bool
     */
    public function login($token, $remember = false) {
        $this->token = $token;

        $this->store($token);

        if ($remember)
            $this->remember($token);

        Event::fire('bharatphp.auth: login');

        return true;
    }

    /**
     * Log the user out of the driver's auth context.
     *
     * @return void
     */
    public function logout() {
        $this->user = null;

        $this->cookie($this->recaller(), null, -2000);

        Session::forget($this->token());

        Event::fire('bharatphp.auth: logout');

        $this->token = null;
    }

    /**
     * Store a user's token in the session.
     *
     * @param  string  $token
     * @return void
     */
    protected function store($token) {
        Session::put($this->token(), $token);
    }

    /**
     * Store a user's token in a long-lived cookie.
     *
     * @param  string  $token
     * @return void
     */
    protected function remember($token) {
        $token = Crypter::encrypt($token . '|' . Str::random(40));

        $this->cookie($this->recaller(), $token, Cookie::forever);
    }

    /**
     * Attempt to find a "remember me" cookie for the user.
     *
     * @return string|null
     */
    protected function recall() {
        $cookie = Cookie::get($this->recaller());

        // By default, "remember me" cookies are encrypted and contain the user
        // token as well as a random string. If it exists, we'll decrypt it
        // and return the first segment, which is the user's ID token.
        if (!is_null($cookie)) {
            return head(explode('|', Crypter::decrypt($cookie)));
        }
    }

    /**
     * Store an authentication cookie.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  int     $minutes
     * @return void
     */
    protected function cookie($name, $value, $minutes) {
        // When setting the default implementation of an authentication
        // cookie we'll use the same settings as the session cookie.
        // This typically makes sense as they both are sensitive.
        $config = Config::get('session');

        extract($config);

        Cookie::put($name, $value, $minutes, $path, $domain, $secure);
    }

    /**
     * Get the session key name used to store the token.
     *
     * @return string
     */
    protected function token() {
        return $this->name() . '_login';
    }

    /**
     * Get the name used for the "remember me" cookie.
     *
     * @return string
     */
    protected function recaller() {
        return Config::get('auth.cookie', $this->name() . '_remember');
    }

    /**
     * Get the name of the driver in a storage friendly format.
     *
     * @return string
     */
    protected function name() {
        return strtolower(str_replace('\\', '_', get_class($this)));
    }

}
