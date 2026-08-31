# pelegram

A lightweight PHP wrapper for the [Telegram Bot API](https://core.telegram.org/bots/api).

pelegram provides a simple interface for building Telegram bots with PHP, including API methods, update handling, routing, and convenient access to update data.

## Requirements

* PHP 8.1+
* Guzzle HTTP 8.x

## Installation

Install pelegram with Composer:

```bash
composer require miraliog/pelegram
```

## Quick Example

```php
<?php

require 'vendor/autoload.php';

use Miraliog\Pelegram\Bot;
use Miraliog\Pelegram\Router;
use Miraliog\Pelegram\Update;

$bot = new Bot('YOUR_BOT_TOKEN');
$router = new Router();

$router->onCommand('start', function (Bot $bot, Update $update) {
    $bot->sendMessage(
        $update->chatId(),
        'Hello from pelegram!'
    );
});

$update = new Update(
    json_decode(file_get_contents('php://input'), true)
);

$router->dispatch($bot, $update);
```

Just need set webhook on your main file!

That's it.

pelegram is designed to stay close to the Telegram Bot API while keeping bot code clean and simple.

## License

MIT
