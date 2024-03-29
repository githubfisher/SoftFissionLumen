<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\Validaters\MobileValidater;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (in_array(app()->environment(), ['local', 'dev'])) {
            // 开发所用扩展包在这里注册
            app()->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            app()->register(\Mnabialek\LaravelSqlLogger\Providers\ServiceProvider::class);
            app()->register(\SwaggerLume\ServiceProvider::class);
        }

        app()->register(MobileValidater::class);
    }
}
