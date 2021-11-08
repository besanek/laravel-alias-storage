# Laravel Alias Storage

Meta filesystem, witch you can acreate aliases for other filesystems.

## Requirement

-   PHP >= 7.4
-   Laravel >= 8.x

## Installing

```shell
$ composer require "besanek/laravel-alias-storage"
```

## Basic Usage
```php
<?php // config/filesystems.php

return [
    'something' => [
        'driver' => 'alias',
        'target' => 'local',
    ],
];
```

In that case, calling  `Storage::disk('something')` will returns local filesystem.

## Real life use case

```php
<?php // config/filesystems.php

return [
    'video' => [
        'driver' => 'alias',
        'target' => env('VIDEO_STORAGE', 'local'),
    ],
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
    's3' => [
        'driver' => 's3',
        // config ...
    ]
];
```

In local development, you can store videos in local filesystem. But in production, you can set environment `VIDEO_STORAGE=s3` and
your video uploads are stored and served from S3. Awesome!
