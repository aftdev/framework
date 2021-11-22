<?php

namespace AftDev\Messenger\Message;

/**
 * Interface SelfHandlingInterface.
 *
 * This interface will be used to capture messages that can "handle" themselves.
 *
 * Ideally this interface would define a `handle()` function but because this function parameters
 * can vary from class to class it cannot be defined here.
 */
interface SelfHandlingInterface
{
}
