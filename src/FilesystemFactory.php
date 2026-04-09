<?php
declare(strict_types=1);

namespace Besanek\LaravelAliasStorage;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;

class FilesystemFactory extends FilesystemManager
{
    public function __construct()
    {
        parent::__construct(app());
    }

    public function make(FilesystemManager $manager, string $target, array $config): Filesystem
    {
        /** @var Repository $repository */
        $repository = $this->app['config'];
        $key = "filesystems.disks.{$target}";

        $backupConfig = $repository->get($key, []);

        $repository->set($key, array_merge($backupConfig, $config));

        try {
            return $manager->resolve($target);
        } finally {
            $repository->set($key, $backupConfig);
        }
    }
}
