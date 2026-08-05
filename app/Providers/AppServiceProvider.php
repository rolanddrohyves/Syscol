<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL; 

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Ajouter la règle de validation current_password
        Validator::extend('current_password', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, auth()->user()->password);
        }, 'Le mot de passe actuel est incorrect.');

        //AJOUTE CES 3 LIGNES POUR FORCER HTTPS EN PRODUCTION
        if (env('APP_ENV') == 'production') {
            URL::forceScheme('https');
        }
    }
}
