<?php

namespace JSzD\World;

use Illuminate\Support\ServiceProvider;
use JSzD\World\Services\WorldService;

class WorldServiceProvider extends ServiceProvider {
    /**
     * Register the application services.
     */
    public function register() {
        if (!defined('JSZD_LW')) {
            define('JSZD_LW', realpath(__DIR__ . '/..'));
        }

        $this->mergeConfigFrom(JSZD_LW . '/config/laravel-world.php', 'laravel-world');

        $this->app->singleton('laravel-world', WorldService::class);
    }

    /**
     * Bootstrap the application services.
     */
    public function boot() {
        $this->publishes([
            JSZD_LW . '/config/laravel-world.php' => config_path('laravel-world.php'),
        ], 'config');

        $this->publishes([
            JSZD_LW . '/database/seeders/WorldSeeder.php' => database_path('seeders/WorldSeeder.php'),
        ], 'seeder');

        $this->publishes([
            JSZD_LW . '/resources/lang' => lang_path('vendor/laravel-world'),
        ], 'lang');

        $this->loadTranslationsFrom(JSZD_LW . '/resources/lang', 'laravel-world');

        $this->loadMigrationsFrom(JSZD_LW . '/database/migrations');

        $this->loadRoutesFrom(JSZD_LW . '/routes/web.php');
    }
}
