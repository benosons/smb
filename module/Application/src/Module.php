<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap($e)
    {
        
        
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
            // header('Set-Cookie: cross-site-cookie=name; SameSite=None; Secure');
            // header('Set-Cookie: cross-site-cookie=name; SameSite=Lax;');

        

        }

        

            // Set a same-site cookie for first-party contexts
            setcookie('cookie1', 'value1', ['samesite' => 'Lax']);
            // Set a cross-site cookie for third-party contexts
            setcookie('cookie2', 'value2', ['samesite' => 'None', 'secure' => true]);


            // Set a same-site cookie for first-party contexts
            header('Set-Cookie: ACookieAvailableCrossSite; promo_shown=1; SameSite=Lax', false);
            // Set a cross-site cookie for third-party contexts
            header('Set-Cookie: ACookieAvailableCrossSite;promo_shown=1;  SameSite=None; Secure', false);

            


        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            
            exit(0);
        }
    }
}
