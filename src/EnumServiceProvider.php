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
                __DIR__ . '/../stubs/enum.stub' => base_path('stubs/vendor/enum-maker/enum.stub'),
                __DIR__ . '/../stubs/enum-filament.stub' => base_path('stubs/vendor/enum-maker/enum-filament.stub'),
            ], 'enum-maker-stubs');
        }
    }
}
