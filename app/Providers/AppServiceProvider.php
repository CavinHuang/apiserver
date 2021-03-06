<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
      Schema::defaultStringLength(191);
  
      //sql调试
      $sql_debug = config('database.sql_debug');
      if ($sql_debug) {
        DB::listen(function ($sql) {
          foreach ($sql->bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
              $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else {
              if (is_string($binding)) {
                $sql->bindings[$i] = "'$binding'";
              }
            }
          }
          $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
          $query = vsprintf($query, $sql->bindings);
          print_r($query);
          echo '<br />';
        });
      }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
