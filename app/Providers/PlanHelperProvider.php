<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
class PlanHelperProvider extends ServiceProvider
{
  public static function titles(){
    return Array("Mr"=>"Mr","Mrs"=>"Mrs","Ms"=>"Ms","Dr"=>"Dr","Prof"=>"Prof");
  }
}