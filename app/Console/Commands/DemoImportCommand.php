<?php

namespace App\Console\Commands;

use App\Support\DemoContentImporter;
use Illuminate\Console\Command;

class DemoImportCommand extends Command
{
    protected $signature = 'demo:import {zip : Absolute path to the demo content zip}';

    protected $description = 'Import demo content zip (tables + public media) into the current database';

    public function handle(DemoContentImporter $importer): int
    {
        $zip = $this->argument('zip');

        $this->info("Importing demo content from {$zip}…");
        $importer->importFromZip($zip);
        $this->info('Demo content imported.');

        return self::SUCCESS;
    }
}
