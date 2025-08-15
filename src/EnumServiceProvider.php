<?php

namespace Ldevbr\LaravelEnumMaker;

use Illuminate\Support\ServiceProvider;
use Ldevbr\LaravelEnumMaker\Console\MakeEnumCommand;

class EnumServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([MakeEnumCommand::class]);

            $this->publishes([
                __DIR__ . '/../stubs/lm-enum.stub' => base_path('stubs/vendor/enum-maker/lm-enum.stub'),
                __DIR__ . '/../stubs/lm-enum-filament.stub' => base_path('stubs/vendor/enum-maker/lm-enum-filament.stub'),
            ], 'enum-maker-stubs');
        }
    }
}
