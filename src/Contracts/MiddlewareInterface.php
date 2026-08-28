<?php

namespace miraliog\pelegram\Contracts;

use miraliog\pelegram\Bot;
use miraliog\pelegram\Update;

interface MiddlewareInterface
{
    public function handle(Bot $bot, Update $update, callable $next): bool;
}
