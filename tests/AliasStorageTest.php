<?php
declare(strict_types=1);

namespace Besanek\LaravelAliasStorage\Tests;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class AliasStorageTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('filesystems.disks.local', [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ]);

        $this->app['config']->set('filesystems.disks.foo', [
            'driver' => 'alias',
            'target' => 'local',
        ]);

        $this->app['config']->set('filesystems.disks.bar', [
            'driver' => 'alias',
            'target' => 'local',
        ]);
    }

    /**
     * @throws FileNotFoundException
     */
    public function testAlias()
    {
        $diskLocal = Storage::disk('local');
        $diskFoo = Storage::disk('foo');
        $diskBar = Storage::disk('bar');

        $random = uniqid('', true);

        $diskLocal->put('something', $random);
        $this->assertSame($random, $diskBar->get('something'));
        $this->assertSame($random, $diskFoo->get('something'));

        $random = uniqid('', true);

        $diskBar->put('something', $random);
        $this->assertSame($random, $diskLocal->get('something'));
        $this->assertSame($random, $diskFoo->get('something'));
    }
}
