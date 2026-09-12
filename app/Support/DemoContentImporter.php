<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class DemoContentImporter
{
    /**
     * Import a demo zip produced by DemoContentExporter.
     */
    public function importFromZip(string $zipPath): void
    {
        if (! File::exists($zipPath)) {
            throw new \InvalidArgumentException("Zip not found: {$zipPath}");
        }

        $extractTo = storage_path('app/demo-sync/import-'.uniqid());
        File::ensureDirectoryExists($extractTo);

        try {
            $zip = new ZipArchive;

            if ($zip->open($zipPath) !== true) {
                throw new \RuntimeException("Cannot open zip: {$zipPath}");
            }

            $zip->extractTo($extractTo);
            $zip->close();

            $dataFile = $extractTo.'/data.json';

            if (! File::exists($dataFile)) {
                throw new \RuntimeException('data.json missing from demo zip');
            }

            /** @var array{tables?: array<string, list<array<string, mixed>>>} $payload */
            $payload = json_decode(File::get($dataFile), true, 512, JSON_THROW_ON_ERROR);

            DB::transaction(function () use ($payload): void {
                Schema::disableForeignKeyConstraints();

                try {
                    foreach (array_reverse(DemoContentExporter::TABLES) as $table) {
                        if (Schema::hasTable($table)) {
                            DB::table($table)->delete();
                        }
                    }

                    foreach (DemoContentExporter::TABLES as $table) {
                        $rows = $payload['tables'][$table] ?? [];

                        if ($rows === [] || ! Schema::hasTable($table)) {
                            continue;
                        }

                        foreach (array_chunk($rows, 100) as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }
                } finally {
                    Schema::enableForeignKeyConstraints();
                }
            });

            $mediaSource = $extractTo.'/public';

            if (File::isDirectory($mediaSource)) {
                $target = storage_path('app/public');
                File::ensureDirectoryExists($target);
                File::copyDirectory($mediaSource, $target);
            }
        } finally {
            File::deleteDirectory($extractTo);
        }
    }
}
