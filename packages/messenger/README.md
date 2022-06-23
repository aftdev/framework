# Messenger

Messaging system based on the
[Symfony messenger](https://symfony.com/doc/current/components/messenger.html).

Allows your application to dispatch messages to queues.

## Dispatching a message

To dispatch a message, use the Messenger service

```php
<?php

use AftDev\Messenger\Messenger;
use App\Message\YourMessage;

class Test {

	public function __construct(Messenger $messenger) {

		$message = new YourMessage();

		$messenger->dispatch($message);
	}
}
```

## Handlers

[Handlers](https://symfony.com/doc/current/components/messenger.html#handlers)
are the services that handle the messages that were dispatched.

### Default handler

By default, the default handler will try to use the `handle` function of the
message class.

```php
<?php
return [
   '*' => [
       'default' => MessageHandler::class,
   ],
];
```

```php
<?php
namespace App\Message;

class YourMessage
{
	public function handle(DependencyA $a, OtherDependency $b)
	{
		echo "Message has been handled";
	}
}
```

All dependencies of the handle function will be automatically injected for you
from the container.

**Note**: If your message class does not have a `handle` function. Nothing will
happen.

### Custom Handlers

If you prefer to create a custom handlers for your application messages you can
do so by registering them in your application configuration.

e.g:

```php
<?php
// file: config/autoload/messenger.global.php
return [
	'messenger' => [
		'handlers' => [
			CustomMessage::class => CustomHandler::class, // <- Target a specific message type
			'*' => [
				'CustomDefaultHandlerForAllMessages::class', // <- Target all messages
			]
		],
	],
];
```

## Senders.

Messages, instead of being handled directly, could be sent somewhere else like a
queue.

### Custom Senders

To create custom senders for your application message you can add them by
assigning a class name or interface to a Sender.

```php
<?php

return [
	'messenger' => [
		'senders' => [
			YourMessageOrInterface::class => [YourCustomSender::class],
		],
	],
];
```

### Queues

Dispatching a message straightaway is not always useful. For that reason
messages can easily be sent to a queue instead.

In order for your message to be queued they need to implements the
AftDev\Messenger\Message\QueueableInterface

```php
<?php

class Message implements \AftDev\Messenger\Message\QueueableInterface
{
    use \AftDev\Messenger\Message\QueueableTrait;

	  protected $queue = 'redis';
}
```

```php
<?php

$message = new Message();
$message->onQueue('other');

$messenger->dispatch($message);

```

#### Queue transports available

All symfony transports are available to use Please see the list here
https://symfony.com/doc/current/messenger.html#transport-configuration

By default the redis and memory transports are configured like so:

```php
return [
	'messenger' => [
		'transports' => [
            'default' => 'memory',
            'memory' => [
                'dsn' => 'in-memory://',
            ],
            'redis' => [
                'dsn' => 'redis://redis:6379',
                'delete_after_ack' => true,
                'lazy' => true,
            ],
		],
	],
];
```

##### Memory

Used for testing. Will just add the message to a memory queue. You would need to
consume them from the same php request.

##### Redis

[Symfony Docs for options and requirements](https://symfony.com/doc/current/messenger.html#redis-transport)

```php
return [
	'messenger' => [
		'transports' => [
            'plugins' => [
                'redis' => [
                		'dsn' => 'redis://redis:6379',
                ],
            ],
		],
	],
];
```

### Queue Message Serialization

https://symfony.com/doc/current/messenger.html#serializing-messages

Default serializer will properly serialize and deserialize your messages for
you. Just type-hint your parameters properly.

```php
<?php
class ToSerialize {
    /** @var string  */
    public $param = 'public';

    /** @var \DateTime */
    public $dateField;

    /** @var CarbonInterface */
    public $carbonField;

    /** @var string */
    protected $protectedValue = 'protected';

    /** @var string */
    private $privateValue = 'private';

    public function __construct()
    {
        $this->dateField = new \DateTime();
        $this->carbonField = Carbon::now();
    }
}
```

#### Serializing complex parameters

Out of the box we support \DateTime and CarbonInterface objects. If you want to
support more types you can easily add more normalizers/denormalizers to the
configuration

They need to implement
`\Symfony\Component\Serializer\Normalizer\DenormalizerInterface` and / or
`Symfony\Component\Serializer\Normalizer\NormalizerInterface`

```php
<?php

return [
    'messenger' => [
        'normalizers' => [
            YourCustomNormalizer::class,
        ],
    ],
];
```

### Consuming queues.

We are using the
[symfony message:consume command](https://symfony.com/doc/current/messenger.html#consuming-messages-running-the-worker)
and the `AftDev\Console` package.

```bash
vendor/bin/console message:consume queue --limit=X
```
