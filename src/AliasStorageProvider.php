<?php
declare(strict_types=1);

namespace Besanek\LaravelAliasStorage;

use Closure;
use DomainException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider;

class AliasStorageProvider extends ServiceProvider
{
    public function boot(): void
    {
        $filesystemManager = $this->getFilesystemManager();

        $filesystemManager->extend('alias', Closure::fromCallable([$this, 'extend']));
    }

    public function extend(Application $app, array $config): Filesystem
    {
        $filesystemManager = $this->getFilesystemManager();

        /** @var Filesystem $disk */
        $disk = $filesystemManager->disk($config['target']);
        return $disk;
    }

    /**
     * @return FilesystemManager
     */
    private function getFilesystemManager(): FilesystemManager
    {
        $filesystem = null;
        try {
            /** @var FilesystemManager $filesystem */
            $filesystem = $this->app->make('filesystem');
        } catch (BindingResolutionException $e) {
        }

        if (!$filesystem || !$filesystem instanceof FilesystemManager) {
            throw new DomainException(
                'package besanek/laravel-alias-storage works only with '
                . FilesystemManager::class . ' file systems'
            );
        }

        return $filesystem;
    }
}
