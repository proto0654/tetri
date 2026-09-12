<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class DemoContentExporter
{
    /**
     * Content tables synced to the demo host (not cache/jobs/sessions).
     *
     * @var list<string>
     */
    public const TABLES = [
        'users',
        'categories',
        'menu_items',
        'stories',
        'settings',
    ];

    /**
     * Build a zip with data.json + storage/app/public files for demo:push.
     */
    public function exportToZip(string $zipPath): string
    {
        File::ensureDirectoryExists(dirname($zipPath));

        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        $staging = storage_path('app/demo-sync/staging-'.uniqid());
        File::ensureDirectoryExists($staging);

        try {
            $payload = [
                'exported_at' => now()->toIso8601String(),
                'tables' => [],
            ];

            foreach (self::TABLES as $table) {
                $payload['tables'][$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
            }

            File::put($staging.'/data.json', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            $publicDisk = storage_path('app/public');
            if (File::isDirectory($publicDisk)) {
                File::copyDirectory($publicDisk, $staging.'/public');
            }

            $zip = new ZipArchive;

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException("Cannot create zip at {$zipPath}");
            }

            $this->addDirectoryToZip($zip, $staging, '');
            $zip->close();

            return $zipPath;
        } finally {
            File::deleteDirectory($staging);
        }
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $directory, string $prefix): void
    {
        foreach (File::files($directory) as $file) {
            $localName = ltrim($prefix.'/'.$file->getFilename(), '/');
            $zip->addFile($file->getPathname(), $localName);
        }

        foreach (File::directories($directory) as $subdir) {
            $name = basename($subdir);
            $this->addDirectoryToZip($zip, $subdir, ltrim($prefix.'/'.$name, '/'));
        }
    }
}
