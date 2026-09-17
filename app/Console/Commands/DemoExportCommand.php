<?php

namespace App\Console\Commands;

use App\Support\DemoContentExporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DemoExportCommand extends Command
{
    protected $signature = 'demo:export
        {--path= : Zip output path (default storage/app/demo-sync/demo-content.zip)}';

    protected $description = 'Export content tables and public media into a demo zip';

    public function handle(DemoContentExporter $exporter): int
    {
        $zipPath = $this->option('path') ?: storage_path('app/demo-sync/demo-content.zip');
        File::ensureDirectoryExists(dirname($zipPath));

        $this->info('Exporting content…');
        $exporter->exportToZip($zipPath);
        $this->info('Zip ready: '.$zipPath);

        return self::SUCCESS;
    }
}
