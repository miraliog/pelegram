<?php

namespace Miraliog\Pelegram\Types\Keyboard;

use Miraliog\Pelegram\Types\Keyboard\Contracts\Keyboardable;

/**
 * ReplyKeyboardRemove
 * Source: https://core.telegram.org/bots/api#replykeyboardremove
 */
class KeyboardRemove implements Keyboardable
{
    private function __construct(private readonly bool $selective = false) {}

    public static function make(bool $selective = false): static
    {
        return new static($selective);
    }

    public function toArray(): array
    {
        $markup = ['remove_keyboard' => true];
        if ($this->selective) $markup['selective'] = true;
        return $markup;
    }
}
