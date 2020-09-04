<?php

namespace Dorcas\ModulesFinanceTax;

use Illuminate\Support\ServiceProvider;

class ModulesFinanceTaxServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'modules-finance-tax');
        $this->publishes([
            __DIR__.'/config/modules-finance-tax.php' => config_path('modules-finance-tax.php'),
        ], 'dorcas-modules');
        // adds models to directory path
        $this->publishes([
            __DIR__.'/Models/TaxAuthorities.php' => app_path('Models/TaxAuthorities.php')
        ], 'dorcas-modules');

        // you can add any number of migrations here
        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateTaxAuthoritiesTable')) {
                $this->publishes([
                    __DIR__ .'/database/migrations/create_tax_authorities_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tax_authorities_table.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // add menu config
        $this->mergeConfigFrom(
            __DIR__.'/config/navigation-menu.php', 'navigation-menu.modules-finance.sub-menu'
        );
    }
}
