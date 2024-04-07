<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

use App\Models\Users;
use App\Helpers\Utilities;
use BharatPHP\Auth;
use BharatPHP\Str;
use BharatPHP\Config;
use PragmaRX\Google2FA\Google2FA;
use BharatPHP\Response;
use BharatPHP\Session;
use BharatPHP\Cookie;
use BharatPHP\Crypter;
use BharatPHP\CrypterV2;

class DashboardController extends Controller
{

    public function dashboard()
    {
        
        return response(view('dashboard/dashboard', [], $layout = 'layouts/default', $viewtype = 'backend'));
    }
}
