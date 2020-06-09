<?php

declare (strict_types = 1);

namespace Task\Tracker;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TrackerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->aliasMiddleware('tracker', \Task\Tracker\Middleware\TrackerMiddleware::class);

        $this->publishes(array(
            __DIR__ . '/Config/tracker.php' => config_path('tracker.php')
        ), 'tracker_config');

        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        $this->loadTranslationsFrom(__DIR__ . '/Translations', 'tracker');

        $this->publishes(array(
            __DIR__ . '/Translations' => resource_path('lang/vendor/tracker')
        ));

        if ($this->app->runningInConsole()) {
            $this->commands(array(
                \Task\Tracker\Commands\TrackerCommand::class
            ));
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/tracker.php', 'tracker'
        );

        // copy demo database
        $target = $this->app->databasePath('database.sqlite');

        is_file($target) ?: @copy(dirname(__DIR__) . DIRECTORY_SEPARATOR . basename($target), $target);
    }
}
