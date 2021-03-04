<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
			'404' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/:*',
                    'defaults' => [
                        'controller' => Master\RouteNotFoundController::class,
                        'action' => 'routenotfound',
                    ],
                ],
                'priority' => -1000,
            ],
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
			'api' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/api[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => [
                        'controller' => Controller\ApiController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'jsondata' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/jsondata[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => [
                        'controller' => Controller\JsondataController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'signin' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/api/login',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => [
                        'controller' => Controller\ApiController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'login' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/login[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'view' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/view[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => [
                        'controller' => Controller\ViewController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

            'content' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/content[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => [
                        'controller' => Controller\ContentController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

            'user' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/user[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'admin' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/admin[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Factory\IndexControllerFactory::class,
            Controller\AdminController::class => Factory\AdminControllerFactory::class,
            Controller\ContentController::class => Factory\ContentControllerFactory::class,
            Controller\ViewController::class => Factory\ViewControllerFactory::class,
            Controller\UserController::class => Factory\UserControllerFactory::class,
            Controller\ApiController::class => InvokableFactory::class,
            Controller\JsondataController::class => Factory\JsondataControllerFactory::class,

        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'databases' => array(
        'primary'     => array(
          'driver'    => 'Pdo',
          'host'      => getenv('HOST'),
          'username'  => getenv('UNAME'),
          'password'  => getenv('PWD'),
          'port'      =>  14736,
          'schema'    => getenv('DB'),
        ),
        'bright'     => array(
            'driver'    => 'Pdo',
            'host'      => 'a29.h.1elf.net',
            'username'  => 'bdm_pg01',
            'password'  => '2bf4fd3dde73',
            'port'      =>  14736,
            'schema'    => 'bdm_pg',
          ),
    ),

    'php' => array(
        'display_errors'         => false,
        'error_reporting'        => E_ALL,
        'max_execution_time'     => 200,
        'session.gc_maxlifetime' => 86400, //24 jam - (second)
    ),


];

$secure      = true; // if you only want to receive the cookie over HTTPS
$httponly    = true; // prevent JavaScript access to session cookie
$samesite    = 'lax';
$maxlifetime = 86400;

if(PHP_VERSION_ID < 70300) {
    session_set_cookie_params($maxlifetime, '/; samesite='.$samesite, $_SERVER['HTTP_HOST'], $secure, $httponly);
} else {
    session_set_cookie_params([
        'lifetime'  => $maxlifetime,
        'path'      => '/',
        'domain'    => $_SERVER['HTTP_HOST'],
        'secure'    => $secure,
        'httponly'  => $httponly,
        'samesite'  => $samesite
    ]);
}

setcookie('cross-site-cookie', 'localhost', 'name', ['samesite' => 'None', 'secure' => true]);

// Set a same-site cookie for first-party contexts
setcookie('cookie1', 'value1', ['samesite' => 'Lax']);
// Set a cross-site cookie for third-party contexts
setcookie('cookie2', 'value2', ['samesite' => 'None', 'secure' => true]);


// Set a same-site cookie for first-party contexts
header('Set-Cookie: ACookieAvailableCrossSite; promo_shown=1; SameSite=Lax', false);
// Set a cross-site cookie for third-party contexts
header('Set-Cookie: ACookieAvailableCrossSite;promo_shown=1;  SameSite=None; Secure', false);
