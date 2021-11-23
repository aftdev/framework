# Laravel DbBuilder and Eloquent Models.

Use Eloquent models without laravel !

## Configuration

You need to register your connection details.
To do so we use use `aftdev/db-manager` package
Please follow the documentation of this package.

e.g

```php
return [
   'database' => [
       'connections' => [
           'mysql' => [
               'default' => true,
               'type' => 'mysql',
               'database' => 'db_name',
               'hostname' => '127.0.0.1',
               'user' => 'X',
               'password' => 'X',
           ],
       ],
    ],
];
```

To configure extra settings supported by laravel but not by `aftdev/db-manager` 
we need to use the `extra` configuration

e.g

```php
<?php
return [
    'database' => [
        'connections' => [
            'mysql' => [
                'default' => true,
                'type' => 'mysql',
                'database' => 'db_name',
                'hostname' => '127.0.0.1',
                'user' => 'X',
                'password' => 'X',
                'extra' => [
                    'read' => [
                        'host' => [
                            '192.168.1.1',
                            '196.168.1.2',
                        ],
                    ],
                    'write' => [
                        'host' => [
                            '196.168.1.3',
                        ],
                    ],
                    'sticky' => true,
                ],
            ],
        ],
    ],
];
```

## Capsule Manager / Query Builder

https://laravel.com/docs/6.x/database

Use the capsule manager to access laravel connections / table etc.

```php
<?php 

class Test
{ 
    public function __construct(CapsuleManager $capsuleManager) {
        $users = $capsuleManager->getConnection('name')->select('select * from users where active = ?', [1]);

        $userTable = $capsuleManager->getConnection('name')->table('users');
    }
}
```

### Default Connection

To directly use the default connection in your service simply use the `Illuminate\Database\ConnectionInterface` for your dependency.

```php
<?php 

use Illuminate\Database\ConnectionInterface;

class Test
{ 
    public function __construct(ConnectionInterface $connection) {
        $users = $connection->select('select * from users where active = ?', [1]);

        $userTable = $connection->table('users');
    }
}
```

## Eloquent ORM

https://laravel.com/docs/7.x/eloquent

To use Eloquent in your application you need to boot it.
This can be done manually like so :

```php
<?php 
use AftDev\Capsule\CapsuleManager;

$capsuleManager = $container->get(CapsuleManager::class);
$capsuleManager->bootEloquent();

$users = App\User::all();
```

Or by using the provided middleware: `AftDev\DbEloquent\Middleware\BootEloquent`
