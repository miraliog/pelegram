<?php

namespace Miraliog\Pelegram\Types\Keyboard;

use Miraliog\Pelegram\Types\Keyboard\Contracts\Keyboardable;

/**
 * InlineKeyboardMarkup
 * Source: https://core.telegram.org/bots/api#inlinekeyboardmarkup
 */
class InlineKeyboardMarkup implements Keyboardable
{
    private array $rows = [];

    private function __construct() {}

    public static function make(): static
    {
        return new static();
    }

    public function row(InlineKeyboardButton|array ...$buttons): static
    {
        $row = [];
        foreach ($buttons as $button) {
            $row[] = $button instanceof InlineKeyboardButton ? $button->toArray() : $button;
        }
        $this->rows[] = $row;
        return $this;
    }

    public function button(InlineKeyboardButton|array $button): static
    {
        return $this->row($button);
    }

    public function toArray(): array
    {
        return ['inline_keyboard' => $this->rows];
    }
}
