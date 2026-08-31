<?php

namespace Miraliog\Pelegram\Types\Keyboard;

use Miraliog\Pelegram\Types\Keyboard\Contracts\Keyboardable;

/**
 * ReplyKeyboardMarkup
 * Source: https://core.telegram.org/bots/api#replykeyboardmarkup
 */
class ReplyKeyboardMarkup implements Keyboardable
{
    private array   $rows                  = [];
    private bool    $resizeKeyboard        = false;
    private bool    $oneTimeKeyboard       = false;
    private bool    $selective             = false;
    private bool    $isPersistent          = false;
    private ?string $inputFieldPlaceholder = null;

    private function __construct() {}

    public static function make(): static
    {
        return new static();
    }

    public function row(KeyboardButton|string ...$buttons): static
    {
        $row = [];
        foreach ($buttons as $button) {
            $row[] = $button instanceof KeyboardButton
                ? $button->toArray()
                : ['text' => $button];
        }
        $this->rows[] = $row;
        return $this;
    }

    public function button(KeyboardButton|string $button): static
    {
        return $this->row($button);
    }

    public function resize(bool $value = true): static
    {
        $this->resizeKeyboard = $value;
        return $this;
    }

    public function oneTime(bool $value = true): static
    {
        $this->oneTimeKeyboard = $value;
        return $this;
    }

    public function selective(bool $value = true): static
    {
        $this->selective = $value;
        return $this;
    }

    public function persistent(bool $value = true): static
    {
        $this->isPersistent = $value;
        return $this;
    }

    public function placeholder(string $text): static
    {
        $this->inputFieldPlaceholder = $text;
        return $this;
    }

    public function toArray(): array
    {
        $markup = ['keyboard' => $this->rows];

        if ($this->resizeKeyboard)                $markup['resize_keyboard']          = true;
        if ($this->oneTimeKeyboard)               $markup['one_time_keyboard']        = true;
        if ($this->selective)                     $markup['selective']                = true;
        if ($this->isPersistent)                  $markup['is_persistent']            = true;
        if ($this->inputFieldPlaceholder !== null) $markup['input_field_placeholder'] = $this->inputFieldPlaceholder;

        return $markup;
    }
}
