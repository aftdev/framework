# Db Manager.

Database connection manager with migration feature using [phinx](https://phinx.org)

Provide a common repository of database connections that can be used by other packages.

## Database Connections

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
           'other_connection' => [
               'type' => 'mysql',
               'database' => 'other_db',
               'hostname' => '127.0.0.1',
               'user' => 'X',
               'password' => 'X',
           ],
       ],
    ],
];
```

## Phinx Db Migration Manager.

Allow other packages to define phinx migrations scripts. 

```php
<?php 
return [
    'database' => [
        'migrations' => [
            'paths' => [
                'path/to/migrations/directory',
            ],
            'seeds' => [
                'path/to/seeds/directory',
            ],
       ],
    ],
];
```

See: http://docs.phinx.org/en/latest/index.html

Instead of using the phinx bin use the database bin provided by this package.

```bash
vendor/bin/database
```
