<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tighten\Ziggy\Ziggy;

class GenerateZiggyRoutes extends Command
{
    protected $signature = 'ziggy:generate {path=./resources/js/ziggy.js}';
    protected $description = 'Generate Ziggy routes file';

    public function handle()
    {
        $path = $this->argument('path');

        $ziggy = new Ziggy('web');

        file_put_contents(
            base_path($path),
            'export default ' . json_encode($ziggy->toArray(), JSON_PRETTY_PRINT) . ';'
        );

        $this->info('Ziggy routes generated successfully!');
    }
}
