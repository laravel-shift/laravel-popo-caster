<?php

namespace Morrislaptop\Caster;

use Illuminate\Support\ServiceProvider;
use Morrislaptop\Caster\Commands\CasterCommand;

class CasterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-castable-object.php' => config_path('laravel-castable-object.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/laravel-castable-object'),
            ], 'views');

            $migrationFileName = 'create_laravel_castable_object_table.php';
            if (! $this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
                ], 'migrations');
            }

            $this->commands([
                CasterCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-castable-object');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-castable-object.php', 'laravel-castable-object');
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}