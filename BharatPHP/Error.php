<?php

namespace BharatPHP;

use BharatPHP\Config;
use BharatPHP\Log;

class Error {

    /**
     * Handle an exception and display the exception report.
     *
     * @param  Exception  $exception
     * @param  bool       $trace
     * @return void
     */
    public static function exception($exception, $trace = true) {
        static::log($exception);

        ob_get_level() and ob_end_clean();

        $message = $exception->getMessage();

        // For BharatPHP view errors we want to show a prettier error:
        $file = $exception->getFile();

        // If detailed errors are enabled, we'll just format the exception into
        // a simple error message and display it on the screen. We don't use a
        // View in case the problem is in the View class.

        if (Config::get('error.detail')) {
            $response_body = "<html><h2>Unhandled Exception</h2>
				<h3>Message:</h3>
				<pre>" . $message . "</pre>
				<h3>Location:</h3>
				<pre>" . $file . " on line " . $exception->getLine() . "</pre>";

            if ($trace) {
                $response_body .= "
				  <h3>Stack Trace:</h3>
				  <pre>" . $exception->getTraceAsString() . "</pre></html>";
            }

            app()->response()->setBody($response_body);
            app()->response()->setCode(500);
        }

        // If we're not using detailed error messages, we'll use the event
        // system to get the response that should be sent to the browser.
        // Using events gives the developer more freedom.
        else {

            if (config('paths.views.500')) {
                $error_view_info = config('paths.views.500');

                app()->response()->setCode(500);
                app()->response()->setBody(view($error_view_info['path'], $error_view_info['params'], $error_view_info['layout']));
//                    return view($error_404_view['path'], $error_404_view['params'], $error_404_view['layout']);
            } else {
                $response = app()->events()->trigger('500', array($exception));
                app()->response()->setBody($response);
            }

            //
//            app()->response()->setCode(500);
//            app()->response()->setBody(view($error_404_view['path'], $error_404_view['params'], $error_404_view['layout']));
//            app()->response()->setCode(500);
//            $response = Event::first('500', array($exception));
//            app()->response()->setBody($response);
//            app()->response()->setCode(500);
//            $response = Response::prepare($response);
        }


        app()->response()->send();

        exit(1);
    }

    /**
     * Handle a native PHP error as an ErrorException.
     *
     * @param  int     $code
     * @param  string  $error
     * @param  string  $file
     * @param  int     $line
     * @return void
     */
    public static function native($code, $error, $file, $line) {

        if (error_reporting() === 0)
            return;

        // For a PHP error, we'll create an ErrorException and then feed that
        // exception to the exception method, which will create a simple view
        // of the exception details for the developer.
        $exception = new \ErrorException($error, $code, 0, $file, $line);

//        if (in_array($code, Config::get('error.ignore'))) {
//            return static::log($exception);
//        }

        static::exception($exception);
    }

    /**
     * Handle the PHP shutdown event.
     *
     * @return void
     */
    public static function shutdown() {
        // If a fatal error occurred that we have not handled yet, we will
        // create an ErrorException and feed it to the exception handler,
        // as it will not yet have been handled.
        $error = error_get_last();

        if (!is_null($error)) {
            extract($error, EXTR_SKIP);

            static::exception(new \ErrorException($message, $type, 0, $file, $line), false);
        }
    }

    /**
     * Log an exception.
     *
     * @param  Exception  $exception
     * @return void
     */
    public static function log($exception) {
        if (Config::get('error.log')) {
//            call_user_func(Config::get('error.logger'), $exception);
            Log::exception($exception);
        }
    }

    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level  Error level
     * @param string $message  Error message
     * @param string $file  Filename the error was raised in
     * @param int $line  Line number in the file
     *
     * @return void
     */
//    public static function errorHandler($level, $message, $file, $line) {
//        if (error_reporting() !== 0) {  // to keep the @ operator working
//            throw new \ErrorException($message, 0, $level, $file, $line);
//        }
//    }

    /**
     * Exception handler.
     *
     * @param Exception $exception  The exception
     *
     * @return void
     */
//    public static function exceptionHandler($exception) {
//        // Code is 404 (not found) or 500 (general error)
//        $code = $exception->getCode();
//        if ($code != 404) {
//            $code = 500;
//        }
//        http_response_code($code);
//        if (Config::SHOW_ERRORS) {
//            echo "<h1>Fatal error</h1>";
//            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
//            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
//            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
//            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
//        } else {
//            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
//            ini_set('error_log', $log);
//            $message = "Uncaught exception: '" . get_class($exception) . "'";
//            $message .= " with message '" . $exception->getMessage() . "'";
//            $message .= "\nStack trace: " . $exception->getTraceAsString();
//            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();
//            error_log($message);
//            View::renderTemplate("$code.html");
//        }
//    }
}
