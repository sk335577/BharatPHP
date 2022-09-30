<?php

namespace BharatPHP\Exception;

class NotFoundException extends \Exception {

    protected $message = 'You don\'t have permission to access this page';
    protected $code = 404;

}
