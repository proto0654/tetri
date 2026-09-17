<?php

namespace App\Console\Commands;

use App\Support\DemoContentImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class DemoPullCommand extends Command
{
    protected $signature = 'demo:pull
        {--host= : SSH host (default DEPLOY_HOST env)}
        {--user= : SSH user (default DEPLOY_USER env)}
        {--path= : Remote app path relative to home (default DEPLOY_PATH env)}
        {--key= : SSH private key path (default DEPLOY_SSH_KEY env)}
        {--php= : Remote PHP binary (default DEPLOY_PHP env)}
        {--import : Import the downloaded zip into the local database}
        {--dry-run : Only print the remote export/scp plan}';

    protected $description = 'Export content on a remote host and download the zip over SSH';

    public function handle(DemoContentImporter $importer): int
    {
        $this->loadDeployEnvFile();

        $host = $this->option('host') ?: env('DEPLOY_HOST');
        $user = $this->option('user') ?: env('DEPLOY_USER', '<DEPLOY_USER>');
        $path = $this->option('path') ?: env('DEPLOY_PATH', 'www/tetri-cafe.ru/');
        $key = $this->option('key') ?: env('DEPLOY_SSH_KEY', $this->defaultSshKeyPath());
        $php = $this->option('php') ?: env('DEPLOY_PHP', '/opt/php/8.4/bin/php');

        if (! str_ends_with($path, '/')) {
            $path .= '/';
        }

        $zipPath = storage_path('app/demo-sync/demo-content.zip');
        File::ensureDirectoryExists(dirname($zipPath));

        if (! filled($host)) {
            $this->error('DEPLOY_HOST / --host is required.');

            return self::FAILURE;
        }

        if (! File::exists($key)) {
            $this->error("SSH key not found: {$key}");

            return self::FAILURE;
        }

        $remoteZipScp = $path.'storage/app/demo-sync/demo-content.zip';
        $remoteZipExport = 'storage/app/demo-sync/demo-content.zip';
        $sshBase = ['ssh', '-i', $key, '-o', 'StrictHostKeyChecking=accept-new', '-o', 'BatchMode=yes', "{$user}@{$host}"];

        if ($this->option('dry-run')) {
            $this->info("Would export on {$user}@{$host}:{$path}");
            $this->info("Would download {$remoteZipScp} → {$zipPath}");

            return self::SUCCESS;
        }

        $this->info('Exporting on remote…');
        $export = Process::timeout(300)->run([
            ...$sshBase,
            "cd {$path} && mkdir -p storage/app/demo-sync && {$php} artisan demo:export --path=".escapeshellarg($remoteZipExport),
        ]);

        if ($export->failed()) {
            $this->error($export->errorOutput() ?: $export->output());

            return self::FAILURE;
        }

        if (filled($export->output())) {
            $this->info($export->output());
        }

        $this->info('Downloading zip…');
        $scp = Process::timeout(600)->run([
            'scp', '-i', $key, '-o', 'StrictHostKeyChecking=accept-new', '-o', 'BatchMode=yes',
            "{$user}@{$host}:{$remoteZipScp}",
            $zipPath,
        ]);

        if ($scp->failed()) {
            $this->error($scp->errorOutput() ?: $scp->output());

            return self::FAILURE;
        }

        $this->info('Zip ready: '.$zipPath);

        if ($this->option('import')) {
            $this->info('Importing into local database…');
            $importer->importFromZip($zipPath);
            $this->info('Local import complete.');
        }

        return self::SUCCESS;
    }

    protected function defaultSshKeyPath(): string
    {
        $home = getenv('HOME') ?: getenv('USERPROFILE') ?: '';

        return $home !== '' ? $home.DIRECTORY_SEPARATOR.'.ssh'.DIRECTORY_SEPARATOR.'deploy_key' : 'deploy_key';
    }

    protected function loadDeployEnvFile(): void
    {
        $path = base_path('.env.deploy');

        if (! is_file($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\"'");

            if ($name !== '' && env($name) === null) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
