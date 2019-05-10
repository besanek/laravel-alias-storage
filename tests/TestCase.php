<?php
declare(strict_types=1);

namespace Besanek\LaravelAliasStorage\Tests;

use Besanek\LaravelAliasStorage\AliasStorageProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($application): array
    {
        return [
            AliasStorageProvider::class,
        ];
    }
}
