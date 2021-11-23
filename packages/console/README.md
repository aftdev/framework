# Console Manager

Provide a console application and a way to register commands.
Based on the symfony console component. https://symfony.com/doc/current/components/console.html

## Using console application

Just use `vendor/bin/console` to access the console application.

## Registering Commands.

You need to register your commands in the `console/commands` section of your configuration file.

Optionally, you could define factories for each command in the `command_manager` section.

```php
return [
    'console' => [
        'commands' => [
            TestCommand::defaultName => TestCommand::class,
        ],
        'command_manager' => [
            'factories' => [
                TestCommand::class => CommandFactory::class,
            ],
        ]
    ]
```

