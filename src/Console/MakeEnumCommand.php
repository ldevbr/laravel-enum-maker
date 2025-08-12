<?php

namespace Ldevbr\EnumMaker\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeEnumCommand extends GeneratorCommand
{
    protected $signature = 'make:enum
        {name : The name of the enum}
        {--backed=string : Backed type: string or int}
        {--cases= : Comma separated case names}
        {--filament : Implements Filament HasLabel & HasColor}
        {--in= : Sub-namespace under Enums (e.g. Sale or Sales/Orders)}
        {--force : Create the class even if the enum already exists}';

    protected $description = 'Create a new PHP enum class';
    protected $type = 'Enum';

    protected function getStub()
    {
        $published = base_path('stubs/vendor/enum-maker/' . ($this->option('filament') ? 'enum-filament.stub' : 'enum.stub'));
        if (file_exists($published)) {
            return $published;
        }

        return __DIR__ . '/../../stubs/' . ($this->option('filament') ? 'enum-filament.stub' : 'enum.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $in = trim((string) $this->option('in'));
        if ($in !== '') {
            $in = collect(explode('/', str_replace('\\', '/', $in)))
                ->map(fn($p) => Str::studly($p))
                ->implode('\\');

            return $rootNamespace . '\\Enums\\' . $in;
        }

        return $rootNamespace . '\\Enums';
    }

    protected function buildClass($name)
    {
        $stub   = parent::buildClass($name);
        $backed = $this->option('backed') ?: 'string';
        $cases  = $this->option('cases') ?: '';

        $caseLines  = '';
        $labelMatch = '';
        $colorMatch = '';

        if ($cases !== '') {
            $items = collect(explode(',', $cases))
                ->map(fn($c) => trim($c))
                ->filter()
                ->values();

            if ($backed === 'int') {
                $caseLines = $items->map(
                    fn($c, $i) =>
                    '    case ' . Str::upper(Str::snake($c)) . ' = ' . $i . ';'
                )->implode(PHP_EOL);
            } else {
                $caseLines = $items->map(
                    fn($c) =>
                    "    case " . Str::upper(Str::snake($c)) . " = '" . Str::slug($c, '_') . "';"
                )->implode(PHP_EOL);
            }

            if ($this->option('filament')) {
                $labelMatch = $items->map(
                    fn($c) =>
                    "            self::" . Str::upper(Str::snake($c)) . " => '" . Str::headline($c) . "',"
                )->implode(PHP_EOL);

                $colors = ['gray', 'info', 'success', 'warning', 'danger', 'primary', 'secondary'];
                $colorMatch = $items->map(
                    fn($c, $i) =>
                    "            self::" . Str::upper(Str::snake($c)) . " => '" . $colors[$i % count($colors)] . "',"
                )->implode(PHP_EOL);
            }
        }

        return str_replace(
            ['{{ backedType }}', '{{ cases }}', '{{ labelMatch }}', '{{ colorMatch }}'],
            [$backed, $caseLines, $labelMatch, $colorMatch],
            $stub
        );
    }
}
