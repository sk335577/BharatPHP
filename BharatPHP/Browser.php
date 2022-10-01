<?php

namespace BharatPHP;

class Browser {

    /**
     * User IP address
     * @var string
     */
    protected $ip = null;

    /**
     * User Subnet
     * @var string
     */
    protected $subnet = null;

    /**
     * User agent property
     * @var string
     */
    protected $ua = null;

    /**
     * Platform
     * @var string
     */
    protected $platform = null;

    /**
     * Operating system
     * @var string
     */
    protected $os = null;

    /**
     * Browser name
     * @var string
     */
    protected $name = null;

    /**
     * Browser version
     * @var string
     */
    protected $version = null;

    /**
     * Mozilla flag
     * @var boolean
     */
    protected $mozilla = false;

    /**
     * Chrome flag
     * @var boolean
     */
    protected $chrome = false;

    /**
     * WebKit flag
     * @var boolean
     */
    protected $webkit = false;

    /**
     * MSIE flag
     * @var boolean
     */
    protected $msie = false;

    /**
     * Opera flag
     * @var boolean
     */
    protected $opera = false;

    /**
     * Constructor
     *
     * Instantiate the browser object
     *
     * @return Browser
     */
    public function __construct() {
        // Set the user agent and object properties.
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->ip = $_SERVER['REMOTE_ADDR'];
            $this->subnet = substr($_SERVER['REMOTE_ADDR'], 0, strrpos($_SERVER['REMOTE_ADDR'], '.'));
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->ua = $_SERVER['HTTP_USER_AGENT'];
            $this->detect();
        }
    }

    /**
     * Get IP
     *
     * @return string
     */
    public function getIp() {
        return $this->ip;
    }

    public function getUserIPAddress() {
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

    /**
     * Get subnet
     *
     * @return string
     */
    public function getSubnet() {
        return $this->subnet;
    }

    /**
     * Get user-agent
     *
     * @return string
     */
    public function getUa() {
        return $this->ua;
    }

    /**
     * Get platform
     *
     * @return string
     */
    public function getPlatform() {
        return $this->platform;
    }

    /**
     * Get OS
     *
     * @return string
     */
    public function getOs() {
        return $this->os;
    }

    /**
     * Get browser name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Get Mozilla flag
     *
     * @return boolean
     */
    public function isMozilla() {
        return $this->mozilla;
    }

    /**
     * Get Chrome flag
     *
     * @return boolean
     */
    public function isChrome() {
        return $this->chrome;
    }

    /**
     * Get WebKit flag
     *
     * @return boolean
     */
    public function isWebkit() {
        return $this->webkit;
    }

    /**
     * Get MSIE flag
     *
     * @return boolean
     */
    public function isMsie() {
        return $this->msie;
    }

    /**
     * Get Opera flag
     *
     * @return boolean
     */
    public function isOpera() {
        return $this->opera;
    }

    /**
     * Detect properties.
     *
     * @return void
     */
    protected function detect() {
        // Determine system platform and OS version.
        if (stripos($this->ua, 'Windows') !== false) {
            $this->platform = 'Windows';
            $this->os = (stripos($this->ua, 'Windows NT') !== false) ? substr($this->ua, stripos($this->ua, 'Windows NT'), 14) : 'Windows';
        } else if (stripos($this->ua, 'Macintosh') !== false) {
            $this->platform = 'Macintosh';
            if (stripos($this->ua, 'Intel') !== false) {
                $this->os = substr($this->ua, stripos($this->ua, 'Intel'));
                $this->os = substr($this->os, 0, stripos($this->os, ';'));
            } else if (stripos($this->ua, 'PPC') !== false) {
                $this->os = substr($this->ua, stripos($this->ua, 'PPC'));
                $this->os = substr($this->os, 0, stripos($this->os, ';'));
            } else {
                $this->os = 'Macintosh';
            }
        } else if (stripos($this->ua, 'Linux') !== false) {
            $this->platform = 'Linux';
            if (stripos($this->ua, 'Linux') !== false) {
                $this->os = substr($this->ua, stripos($this->ua, 'Linux '));
                $this->os = substr($this->os, 0, stripos($this->os, ';'));
            } else {
                $this->os = 'Linux';
            }
        } else if (stripos($this->ua, 'SunOS') !== false) {
            $this->platform = 'SunOS';
            if (stripos($this->ua, 'SunOS') !== false) {
                $this->os = substr($this->ua, stripos($this->ua, 'SunOS '));
                $this->os = substr($this->os, 0, stripos($this->os, ';'));
            } else {
                $this->os = 'SunOS';
            }
        } else if (stripos($this->ua, 'OpenBSD') !== false) {
            $this->platform = 'OpenBSD';
            if (stripos($this->ua, 'OpenBSD') !== false) {
                $this->os = substr($this->ua, stripos($this->ua, 'OpenBSD '));
                $this->os = substr($this->os, 0, stripos($this->os, ';'));
            } else {
                $this->os = 'OpenBSD';
            }
        } else if (stripos($this->ua, 'NetBSD') !== false) {
            $this->platform = 'NetBSD';
            if (stripos($this->ua, 'NetBSD') !== false) {
                $this->os = substr($this->ua, stripos($this->ua, 'NetBSD '));
                $this->os = substr($this->os, 0, stripos($this->os, ';'));
            } else {
                $this->os = 'NetBSD';
            }
        } else if (stripos($this->ua, 'FreeBSD') !== false) {
            $this->platform = 'FreeBSD';
            if (stripos($this->ua, 'FreeBSD') !== false) {
                $this->os = substr($this->ua, stripos($this->ua, 'FreeBSD '));
                $this->os = substr($this->os, 0, stripos($this->os, ';'));
            } else {
                $this->os = 'FreeBSD';
            }
        }

        // Determine browser and browser version.
        if (stripos($this->ua, 'Camino') !== false) {
            $this->name = 'Camino';
            $this->webkit = true;
            $this->version = substr($this->ua, (stripos($this->ua, 'Camino/') + 7));
        } else if (stripos($this->ua, 'Chrome') !== false) {
            $this->name = 'Chrome';
            $this->chrome = true;
            $this->webkit = true;
            $this->version = substr($this->ua, (stripos($this->ua, 'Chrome/') + 7));
            $this->version = substr($this->version, 0, (stripos($this->version, ' ')));
        } else if (stripos($this->ua, 'Firefox') !== false) {
            $this->name = 'Firefox';
            $this->mozilla = true;
            $this->version = substr($this->ua, (stripos($this->ua, 'Firefox/') + 8));
        } else if (stripos($this->ua, 'MSIE') !== false) {
            $this->name = 'MSIE';
            $this->msie = true;
            $this->version = substr($this->ua, (stripos($this->ua, 'MSIE ') + 5));
            $this->version = substr($this->version, 0, stripos($this->version, ';'));
        } else if (stripos($this->ua, 'Trident') !== false) {
            $this->name = 'MSIE';
            $this->msie = true;
            $this->version = substr($this->ua, (stripos($this->ua, 'rv:') + 3));
            $this->version = substr($this->version, 0, stripos($this->version, ')'));
        } else if (stripos($this->ua, 'Konqueror') !== false) {
            $this->name = 'Konqueror';
            $this->webkit = true;
            $this->version = substr($this->ua, (stripos($this->ua, 'Konqueror/') + 10));
            $this->version = substr($this->version, 0, stripos($this->version, ';'));
        } else if (stripos($this->ua, 'Navigator') !== false) {
            $this->name = 'Navigator';
            $this->mozilla = true;
            $this->version = substr($this->ua, (stripos($this->ua, 'Navigator/') + 10));
        } else if (stripos($this->ua, 'Opera') !== false) {
            $this->name = 'Opera';
            $this->opera = true;
            $this->version = substr($this->ua, (stripos($this->ua, 'Opera/') + 6));
            $this->version = substr($this->version, 0, stripos($this->version, ' '));
        } else if (stripos($this->ua, 'Safari') !== false) {
            $this->name = 'Safari';
            $this->webkit = true;
            $this->version = substr($this->ua, (stripos($this->ua, 'Version/') + 8));
            $this->version = substr($this->version, 0, stripos($this->version, ' '));
        }
    }

}
