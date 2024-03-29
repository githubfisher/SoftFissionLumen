<?php

require_once __DIR__ . '/../vendor/autoload.php';

$envPrefix  = '.env.';
$defaultEnv = $envPrefix . 'local';
if (isset($_SERVER['HTTP_HOST'])) {
    //根据域名配置切换不同环境
    $suffix   = ! empty($_SERVER['HTTP_HOST']) ? substr($_SERVER['HTTP_HOST'], 0, 2) : '';
    switch ($suffix) {
        case 'so':
            $environment = $envPrefix . 'produce';

            break;
        case 'de':
            $environment = $envPrefix . 'dev';

            break;
        case 'te':
            $environment = $envPrefix . 'test';

            break;
        default:
            $environment = $defaultEnv;

            break;
    }
} else {
    //指定执行命令环境
    $environment = $defaultEnv;
    if ( ! empty($argv)) {
        foreach ($argv as $argvStr) {
            if (strstr($argvStr, '--env=') !== false) {
                $environment = str_replace('--env=', $envPrefix, $argvStr);
            }
        }
    }
}

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__),
    $environment
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
 */

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();
$app->withEloquent();

$app->configure('api');
$app->configure('auth');
$app->configure('allow_origins');
$app->configure('swagger-lume');

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
 */

$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, App\Exceptions\Handler::class);
$app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
 */

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

 $app->routeMiddleware([
     'auth'    => App\Http\Middleware\Auth\Authenticate::class,
     'cors'    => App\Http\Middleware\CorsMiddleware::class,
     'admin'   => \App\Http\Middleware\Auth\AdminAuthGuard::class,
     'ops'     => \App\Http\Middleware\Auth\OpsAuthGuard::class,
     'refresh' => \App\Http\Middleware\Auth\RefreshToken::class,
 ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
 */

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Dingo\Api\Provider\LumenServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
 */

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
    require __DIR__ . '/../routes/api.php';
});

return $app;
