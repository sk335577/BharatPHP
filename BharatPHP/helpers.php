<?php

use BharatPHP\Config;
use BharatPHP\Application;
use BharatPHP\CrypterV2;
use BharatPHP\Translator;
use BharatPHP\Session;
use BharatPHP\HTML;
use BharatPHP\URL;
use BharatPHP\Hash\BcryptHasher;

function setConfig($path, $value)
{
    Config::setConfig($path, $value);
}

function createUrlSlug($text, string $divider = '-')
{
    //  $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $urlString);
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
    //  return $slug;
}


function limitTextByWords($text, $limit, $end_txt = '...')
{
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos   = array_keys($words);
        $text  = substr($text, 0, $pos[$limit]) . $end_txt;
    }
    return $text;
}

function printFullWebRequestURL()
{
    echo request()->getFullBrowserURL();
}

function printAppTitleTag($title, $add_app_name = true, $app_seperator = " - ")
{
    $title = trim($title);
    if ($add_app_name) {
        if (!empty($title)) {

            $title .= " $app_seperator " . config('app_title');
        } else {
            $title = "" . config('app_title');
        }
    }
    echo "<title>$title</title>";
}

function config($path = '', $default = '')
{
    return Config::get($path, $default);
}
function path($path = '', $default = '')
{
    if ($path == 'storage') {
        return BharatPHP_STORAGE_PATH;
    }
}

function envConfig($config_name = '', $default = '')
{
    return Config::envConfig($config_name, $default);
}

function request()
{
    return app()->request();
}

function csrfToken()
{
    return Session::token();
}

function appUrl()
{
    return app()->request()->appUrl();
}

function printAppUrl()
{
    echo app()->request()->appUrl();
}

function getTemplatePart($part, $viewtype = 'frontend')
{
    return app()->view()->getTemplatePart($part);
}

function injectTemplate($position, $template, $viewtype = 'frontend')
{
    return app()->view()->injectTemplate($position, $template, $viewtype);
}

function printInjectedTemplates($position,)
{
    return app()->view()->printInjectedTemplates($position);
}

function view($view, $params = [], $layout = 'layouts/default', $viewtype = 'frontend')
{
    return app()->view()->renderView($view, $params, $layout, $viewtype);
}

function viewWithoutLayout($view, $params = [], $viewtype = 'frontend')
{
    return app()->view()->renderViewOnly($view, $params, $viewtype);
}

function app(): Application
{
    return Application::app();
}

function routeNameToURL($route_name, $route_params = [])
{
    return \BharatPHP\Routes::routeNameToURL($route_name, $route_params);
}

function json($data, $http_code = 200)
{

    app()->response()->setHeader('Content-Type', 'application/json');
    app()->response()->setCode($http_code);

    app()->response()->setBody(json_encode($data));
    return app()->response();
}

function response($view, $http_code = 200)
{

    app()->response()->setCode($http_code);
    app()->response()->setBody($view);

    return app()->response();
}

function getCookie($name)
{
    return app()->request()->getCookie($name);
}

function printTemplatePart($part, $params = [], $viewtype = 'frontend')
{
    echo app()->view()->getTemplatePart($part, $params);
}

//function printTemplatePart($part, $viewtype = 'frontend') {
//    echo app()->view()->getTemplatePart($part);
//}

/**
 * Convert HTML characters to entities.
 *
 * The encoding specified in the application configuration file will be used.
 *
 * @param  string  $value
 * @return string
 */
function e($value)
{
    return HTML::entities($value);
}

function t($string)
{
    return Translator::t($string);
}

/**
 * Dump the given value and kill the script.
 *
 * @param  mixed  $value
 * @return void
 */
function vd($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die;
}

function pd($value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
    die;
}

function pr($value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
}

/**
 * Get an item from an array using "dot" notation.
 *
 * <code>
 * 		// Get the $array['user']['name'] value from the array
 * 		$name = array_get($array, 'user.name');
 *
 * 		// Return a default from if the specified item doesn't exist
 * 		$name = array_get($array, 'user.name', 'Taylor');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function array_get($array, $key, $default = null)
{
    if (is_null($key))
        return $array;

    // To retrieve the array item using dot syntax, we'll iterate through
    // each segment in the key and look for that value. If it exists, we
    // will return it, otherwise we will set the depth of the array and
    // look for the next segment.
    foreach (explode('.', $key) as $segment) {
        if (!is_array($array) or !array_key_exists($segment, $array)) {
            return value($default);
        }

        $array = $array[$segment];
    }

    return $array;
}

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * <code>
 * 		// Set the $array['user']['name'] value on the array
 * 		array_set($array, 'user.name', 'Taylor');
 *
 * 		// Set the $array['user']['name']['first'] value on the array
 * 		array_set($array, 'user.name.first', 'Michael');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return void
 */
function array_set(&$array, $key, $value)
{
    if (is_null($key))
        return $array = $value;

    $keys = explode('.', $key);

    // This loop allows us to dig down into the array to a dynamic depth by
    // setting the array value for each level that we dig into. Once there
    // is one key left, we can fall out of the loop and set the value as
    // we should be at the proper depth.
    while (count($keys) > 1) {
        $key = array_shift($keys);

        // If the key doesn't exist at this depth, we will just create an
        // empty array to hold the next value, allowing us to create the
        // arrays to hold the final value.
        if (!isset($array[$key]) or !is_array($array[$key])) {
            $array[$key] = array();
        }

        $array = &$array[$key];
    }

    $array[array_shift($keys)] = $value;
}

/**
 * Remove an array item from a given array using "dot" notation.
 *
 * <code>
 * 		// Remove the $array['user']['name'] item from the array
 * 		array_forget($array, 'user.name');
 *
 * 		// Remove the $array['user']['name']['first'] item from the array
 * 		array_forget($array, 'user.name.first');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @return void
 */
function array_forget(&$array, $key)
{
    $keys = explode('.', $key);

    // This loop functions very similarly to the loop in the "set" method.
    // We will iterate over the keys, setting the array value to the new
    // depth at each iteration. Once there is only one key left, we will
    // be at the proper depth in the array.
    while (count($keys) > 1) {
        $key = array_shift($keys);

        // Since this method is supposed to remove a value from the array,
        // if a value higher up in the chain doesn't exist, there is no
        // need to keep digging into the array, since it is impossible
        // for the final value to even exist.
        if (!isset($array[$key]) or !is_array($array[$key])) {
            return;
        }

        $array = &$array[$key];
    }

    unset($array[array_shift($keys)]);
}

/**
 * Return the first element in an array which passes a given truth test.
 *
 * <code>
 * 		// Return the first array element that equals "Taylor"
 * 		$value = array_first($array, function($k, $v) {return $v == 'Taylor';});
 *
 * 		// Return a default value if no matching element is found
 * 		$value = array_first($array, function($k, $v) {return $v == 'Taylor'}, 'Default');
 * </code>
 *
 * @param  array    $array
 * @param  Closure  $callback
 * @param  mixed    $default
 * @return mixed
 */
function array_first($array, $callback, $default = null)
{
    foreach ($array as $key => $value) {
        if (call_user_func($callback, $key, $value))
            return $value;
    }

    return value($default);
}

/**
 * Recursively remove slashes from array keys and values.
 *
 * @param  array  $array
 * @return array
 */
function array_strip_slashes($array)
{
    $result = array();

    foreach ($array as $key => $value) {
        $key = stripslashes($key);

        // If the value is an array, we will just recurse back into the
        // function to keep stripping the slashes out of the array,
        // otherwise we will set the stripped value.
        if (is_array($value)) {
            $result[$key] = array_strip_slashes($value);
        } else {
            $result[$key] = stripslashes($value);
        }
    }

    return $result;
}

/**
 * Divide an array into two arrays. One with keys and the other with values.
 *
 * @param  array  $array
 * @return array
 */
function array_divide($array)
{
    return array(array_keys($array), array_values($array));
}

/**
 * Pluck an array of values from an array.
 *
 * @param  array   $array
 * @param  string  $key
 * @return array
 */
function array_pluck($array, $key)
{
    return array_map(function ($v) use ($key) {
        return is_object($v) ? $v->$key : $v[$key];
    }, $array);
}

/**
 * Get a subset of the items from the given array.
 *
 * @param  array  $array
 * @param  array  $keys
 * @return array
 */
function array_only($array, $keys)
{
    return array_intersect_key($array, array_flip((array) $keys));
}

/**
 * Get all of the given array except for a specified array of items.
 *
 * @param  array  $array
 * @param  array  $keys
 * @return array
 */
function array_except($array, $keys)
{
    return array_diff_key($array, array_flip((array) $keys));
}

/**
 * Determine if "Magic Quotes" are enabled on the server.
 *
 * @return bool
 */
function magic_quotes()
{
    return function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc();
}

/**
 * Return the first element of an array.
 *
 * This is simply a convenient wrapper around the "reset" method.
 *
 * @param  array  $array
 * @return mixed
 */
function head($array)
{
    return reset($array);
}

/**
 * Generate an application URL.
 *
 * <code>
 * 		// Create a URL to a location within the application
 * 		$url = url('user/profile');
 *
 * 		// Create a HTTPS URL to a location within the application
 * 		$url = url('user/profile', true);
 * </code>
 *
 * @param  string  $url
 * @param  bool    $https
 * @return string
 */
function url($url = '', $https = null)
{
    return URL::to($url, $https);
}

/**
 * Generate an application URL to an asset.
 *
 * @param  string  $url
 * @param  bool    $https
 * @return string
 */
function asset($url, $https = null)
{
    return URL::to_asset($url, $https);
}

/**
 * Generate a URL to a controller action.
 *
 * <code>
 * 		// Generate a URL to the "index" method of the "user" controller
 * 		$url = action('user@index');
 *
 * 		// Generate a URL to http://example.com/user/profile/taylor
 * 		$url = action('user@profile', array('taylor'));
 * </code>
 *
 * @param  string  $action
 * @param  array   $parameters
 * @return string
 */
function action($action, $parameters = array())
{
    return URL::to_action($action, $parameters);
}

/**
 * Generate a URL from a route name.
 *
 * <code>
 * 		// Create a URL to the "profile" named route
 * 		$url = route('profile');
 *
 * 		// Create a URL to the "profile" named route with wildcard parameters
 * 		$url = route('profile', array($username));
 * </code>
 *
 * @param  string  $name
 * @param  array   $parameters
 * @return string
 */
function route($name, $parameters = array())
{
    return URL::to_route($name, $parameters);
}

/**
 * Determine if a given string begins with a given value.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function starts_with($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}

/**
 * Determine if a given string ends with a given value.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function ends_with($haystack, $needle)
{
    return $needle == substr($haystack, strlen($haystack) - strlen($needle));
}

/**
 * Determine if a given string contains a given sub-string.
 *
 * @param  string        $haystack
 * @param  string|array  $needle
 * @return bool
 */
//function str_contains($haystack, $needle) {
//    foreach ((array) $needle as $n) {
//        if (strpos($haystack, $n) !== false)
//            return true;
//    }
//
//    return false;
//}

/**
 * Cap a string with a single instance of the given string.
 *
 * @param  string  $value
 * @param  string  $cap
 * @return string
 */
function str_finish($value, $cap)
{
    return rtrim($value, $cap) . $cap;
}

/**
 * Determine if the given object has a toString method.
 *
 * @param  object  $value
 * @return bool
 */
function str_object($value)
{
    return is_object($value) and method_exists($value, '__toString');
}

/**
 * Get the root namespace of a given class.
 *
 * @param  string  $class
 * @param  string  $separator
 * @return string
 */
function root_namespace($class, $separator = '\\')
{
    if (str_contains($class, $separator)) {
        return head(explode($separator, $class));
    }
}

/**
 * Get the "class basename" of a class or object.
 *
 * The basename is considered to be the name of the class minus all namespaces.
 *
 * @param  object|string  $class
 * @return string
 */
function class_basename($class)
{
    if (is_object($class))
        $class = get_class($class);

    return basename(str_replace('\\', '/', $class));
}

/**
 * Return the value of the given item.
 *
 * If the given item is a Closure the result of the Closure will be returned.
 *
 * @param  mixed  $value
 * @return mixed
 */
function value($value)
{
    return (is_callable($value) and !is_string($value)) ? call_user_func($value) : $value;
}

/**
 * Short-cut for constructor method chaining.
 *
 * @param  mixed  $object
 * @return mixed
 */
function with($object)
{
    return $object;
}

/**
 * Determine if the current version of PHP is at least the supplied version.
 *
 * @param  string  $version
 * @return bool
 */
function has_php($version)
{
    return version_compare(PHP_VERSION, $version) >= 0;
}

/**
 * Calculate the human-readable file size (with proper units).
 *
 * @param  int     $size
 * @return string
 */
function get_file_size($size)
{
    $units = array('Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB');
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $units[$i];
}

function array_filter_by_key_values(array $arr, array $params)
{

    $res = array_filter(
        $arr,
        fn ($row) => !array_diff_assoc($params, $row)
    );

    if (count($res)) {
        $res = array_values($res);
        if (isset($res[0])) {
            return $res[0];
        }
    }

    return [];
}

function getRouteParams()
{
    return app()->request()->getRouteParams();
}

function getRouteParam($param, $default = null)
{
    return app()->request()->getRouteParam($param, $default);
}


function getAndClearFlashMessages($message_type = 'errors')
{
    $errors = Session::get($message_type);
    Session::forget($message_type);
    // Session::save();
    if (empty($errors)) {
        return [];
    }
    return $errors;
}
function setAndSaveFlashMessages($errors, $message_type = 'errors')
{
    Session::put($message_type, $errors);
    // Session::save();
}


function sanitizeStringStripTags($str)
{

    $str = filter_var($str, FILTER_SANITIZE_STRING);

    $str = strip_tags($str);

    $str = htmlspecialchars($str);

    //        $res = str_replace(array('\'', '"',
    //            ',', ';', '<', '>', '*', ':', '@', '$', '(', '`', '~', ')', '[', ']', '.'), ' ', $str);

    return $str;
}

function sanitizeString($str)
{

    $str = filter_var($str, FILTER_SANITIZE_STRING);

    $str = strip_tags($str);

    $str = htmlspecialchars($str);

    $res = str_replace(array(
        '\'', '"',
        ',', ';', '<', '>', '*', ':', '@', '$', '(', '`', '~', ')', '[', ']', '.'
    ), ' ', $str);

    return $str;
}

/*
 * Sanitize the input email
 */

function sanitizeEmail($email)
{
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $email = str_replace(array('\'', '"', ';', '<', '>', '*', '`', '~', '[', ']'), ' ', $email);

    return $email;
}

function createDirectory($dir, $permission = 0755)
{
    return mkdir($dir, $permission, true);
}

function prepareUploadDirectory()
{

    // return mkdir($dir, $permission, true);
}

function encryptArray($array)
{
    $cr = new CrypterV2(config('application_key'));
    return $cr->encrypt($array, true);
    //  $c = $cr->decrypt($c);
}
function decryptArray($encrypted_array)
{
    $cr = new CrypterV2(config('application_key'));
    return  $cr->decrypt($encrypted_array, true);
}

function encryptString($s)
{
    $cr = new CrypterV2(config('application_key'));
    return $cr->encrypt($s, false);
    //  $c = $cr->decrypt($c);
}
function decryptString($s)
{
    $cr = new CrypterV2(config('application_key'));
    return  $cr->decrypt($s, true);
}
function hashBcrypt($s)
{

    return app()->services()->get('hasher_bcrypt')->make($s);
    // $b = new BcryptHasher();
    // return  $b->make($s);
}
function hashBcryptVerify($s, $hash)
{
    // $b = new BcryptHasher();
    return app()->services()->get('hasher_bcrypt')->check($s, $hash);
}
