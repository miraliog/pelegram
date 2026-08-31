<?php

namespace Miraliog\Pelegram\Types\Keyboard;

/**
 * Represents a disabled inline button (Bot API 10.3)
 * When the user presses it, a tooltip is shown instead of an action
 */
class DisabledButton
{
    private function __construct(
        private readonly string $text,
        private readonly ?string $tooltip = null,
    ) {}

    public static function make(string $text, ?string $tooltip = null): static
    {
        return new static($text, $tooltip);
    }

    public function toArray(): array
    {
        return array_filter([
            'text'    => $this->text,
            'tooltip' => $this->tooltip,
        ]);
    }
}
