<?php
declare(strict_types=1);

namespace Besanek\LaravelAliasStorage;

use Besanek\LaravelAliasStorage\Exceptions\InvalidConfigurationException;
use Closure;
use DomainException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider;

class AliasStorageProvider extends ServiceProvider
{
    /** @var array<int,string> */
    private array $targetStack = [];

    public function boot(): void
    {
        $filesystemManager = $this->getFilesystemManager();

        $filesystemManager->extend('alias', Closure::fromCallable([$this, 'extend']));
    }

    /**
     * @param Application $app
     * @param array $config
     * @return Filesystem
     */
    public function extend(Application $app, array $config): Filesystem
    {
        $filesystemManager = $this->getFilesystemManager();

        $target = $this->getTargetFromConfig($config);

        $this->targetStack[] = $target;

        /** @var FilesystemFactory $factory */
        $factory = $this->app->make(FilesystemFactory::class);

        $disk = $factory->make($filesystemManager, $target, $config['options'] ?? []);

        array_pop($this->targetStack);

        return $disk;
    }

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

    /**
     * @param array<string,mixed> $config
     * @return string
     */
    private function getTargetFromConfig(array $config)
    {
        if (!isset($config['target'])) {
            throw new InvalidConfigurationException(
                'Missing target in configuration'
            );
        }

        $target = $config['target'];
        if (in_array($target, $this->targetStack, true)) {
            throw new InvalidConfigurationException(
                'Found cyclic disk aliasing: ' . implode(' > ', $this->targetStack)
            );
        }
        return $target;
    }
}
