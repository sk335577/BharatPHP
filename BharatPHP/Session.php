<?php

namespace BharatPHP;

class Session {

    private static $sessionId = null;

    public static function init() {
        // Start a session and set the session id.
        if (session_id() == '') {
            session_start();
            self::$sessionId = session_id();
        }
    }

    public static function getId() {
        return self::$sessionId;
    }

    public static function regenerateId() {
        session_regenerate_id();
        self::$sessionId = session_id();
    }

    public static function kill() {
        $_SESSION = null;
        session_unset();
        session_destroy();
        unset(self::$sessionId);
    }

    public static function set($name, $value) {
        $_SESSION[$name] = $value;
    }

    public static function get($name) {
        return (isset($_SESSION[$name])) ? $_SESSION[$name] : null;
    }

    public function has($name) {
        return isset($_SESSION[$name]);
    }

    public function delete($name) {
        $_SESSION[$name] = null;
        unset($_SESSION[$name]);
    }

}
