<?php

namespace App\Console\Commands;

use App\Support\DemoContentExporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class DemoPushCommand extends Command
{
    protected $signature = 'demo:push
        {--host= : SSH host (default DEPLOY_HOST env)}
        {--user= : SSH user (default DEPLOY_USER env)}
        {--path= : Remote app path relative to home (default DEPLOY_PATH env)}
        {--key= : SSH private key path (default DEPLOY_SSH_KEY env)}
        {--php= : Remote PHP binary (default DEPLOY_PHP env)}
        {--dry-run : Build zip only, do not upload}';

    protected $description = 'Export local demo content and push it to the demo host over SSH';

    public function handle(DemoContentExporter $exporter): int
    {
        $this->loadDeployEnvFile();

        $host = $this->option('host') ?: env('DEPLOY_HOST');
        $user = $this->option('user') ?: env('DEPLOY_USER', '<DEPLOY_USER>');
        $path = $this->option('path') ?: env('DEPLOY_PATH', 'www/former-staging.example/');
        $key = $this->option('key') ?: env('DEPLOY_SSH_KEY', $this->defaultSshKeyPath());
        $php = $this->option('php') ?: env('DEPLOY_PHP', '/opt/php/8.4/bin/php');

        if (! str_ends_with($path, '/')) {
            $path .= '/';
        }

        $zipPath = storage_path('app/demo-sync/demo-content.zip');
        File::ensureDirectoryExists(dirname($zipPath));

        $this->info('Exporting local content…');
        $exporter->exportToZip($zipPath);
        $this->info('Zip ready: '.$zipPath);

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        if (! filled($host)) {
            $this->error('DEPLOY_HOST / --host is required.');

            return self::FAILURE;
        }

        if (! File::exists($key)) {
            $this->error("SSH key not found: {$key}");

            return self::FAILURE;
        }

        $remoteZip = rtrim($path, '/').'/storage/app/demo-sync/demo-content.zip';
        $sshBase = ['ssh', '-i', $key, '-o', 'StrictHostKeyChecking=accept-new', '-o', 'BatchMode=yes', "{$user}@{$host}"];

        $this->info('Uploading zip…');
        $mkdir = Process::run([...$sshBase, "mkdir -p {$path}storage/app/demo-sync"]);

        if ($mkdir->failed()) {
            $this->error($mkdir->errorOutput() ?: $mkdir->output());

            return self::FAILURE;
        }

        $scp = Process::run([
            'scp', '-i', $key, '-o', 'StrictHostKeyChecking=accept-new', '-o', 'BatchMode=yes',
            $zipPath,
            "{$user}@{$host}:{$remoteZip}",
        ]);

        if ($scp->failed()) {
            $this->error($scp->errorOutput() ?: $scp->output());

            return self::FAILURE;
        }

        $this->info('Importing on remote…');
        $import = Process::run([
            ...$sshBase,
            "cd {$path} && {$php} artisan demo:import ".escapeshellarg($remoteZip)." && {$php} artisan optimize:clear",
        ]);

        if ($import->failed()) {
            $this->error($import->errorOutput() ?: $import->output());

            return self::FAILURE;
        }

        if (filled($import->output())) {
            $this->info($import->output());
        }

        $this->info('Demo content pushed.');

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
