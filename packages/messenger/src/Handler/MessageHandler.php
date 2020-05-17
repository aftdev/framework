<?php

namespace AftDev\Messenger\Handler;

use AftDev\ServiceManager\Resolver;

class MessageHandler
{
    /**
     * @var Resolver
     */
    protected $resolver;

    public function __construct(Resolver $resolver = null)
    {
        $this->resolver = $resolver;
    }

    public function __invoke($message)
    {
        if (method_exists($message, 'handle')) {
            return $this->resolver->call([$message, 'handle']);
        }
    }
}
