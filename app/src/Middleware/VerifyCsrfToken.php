<?php

namespace App\Middleware;

use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Session;
use BharatPHP\Session\TokenMismatchException;

class VerifyCsrfToken {

    public function execute(Request $request, Response $response) {
        if ($this->isReading($request) || $this->tokensMatch($request)) {
//            return $this->addCookieToResponse($request, $response);
            $this->addCookieToResponse($request, $response);
        }

//        throw new TokenMismatchException;
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch(Request $request) {
        $token = $request->input(Session::csrf_token) ?: $request->getHeader('X-CSRF-TOKEN');

//        if (!$token && $header = $request->getHeader('X-XSRF-TOKEN')) {
//            $token = $this->encrypter->decrypt($header);
//        }

        return $token === Session::token();
    }

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    protected function addCookieToResponse(Request $request, Response $response) {
        \BharatPHP\Cookie::put('XSRF-TOKEN', Session::token(), time() + 60 * 120, '/');
//        $response->headers->setCookie(
//                new Cookie('XSRF-TOKEN', $request->session()->token(), time() + 60 * 120, '/', null, false, false)
//        );

        return $response;
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isReading(Request $request) {
        return in_array($request->getMethodUpperCase(), ['HEAD', 'GET', 'OPTIONS']);
    }

}
