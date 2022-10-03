<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ServiceResponseProvider extends ServiceProvider
{
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('api', function ($content = null, $status = 200, $warnings = null, $errors = null) use ($factory) {

            empty($content) === false ? $customFormat["content"] = $content : null;
            empty($status) === false ? $customFormat["status"] = $status : null;
            empty($errors) === false ? $customFormat["errors"] = $errors : null;
            empty($warnings) === false ? $customFormat["warnings"] = $warnings : null;

            return $factory->make($customFormat, $status);
        });
    }

    public function register()
    {
        //
    }
}
