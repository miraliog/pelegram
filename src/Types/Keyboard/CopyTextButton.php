<?php

namespace Miraliog\Pelegram\Types\Keyboard;

/**
 * CopyTextButton
 * Source: https://core.telegram.org/bots/api#copytextbutton
 *
 * Represents an inline keyboard button that copies specified text to the clipboard.
 */
class CopyTextButton
{
    private function __construct(
        public readonly string $text,
    ) {}

    public static function make(string $text): static
    {
        return new static($text);
    }

    public function toArray(): array
    {
        return ['text' => $this->text];
    }
}
