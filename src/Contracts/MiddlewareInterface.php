<?php

namespace Miraliog\Pelegram\Contracts;

use Miraliog\Pelegram\Bot;
use Miraliog\Pelegram\Update;

interface MiddlewareInterface
{
    public function handle(Bot $bot, Update $update, callable $next): bool;
}
