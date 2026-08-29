<?php

namespace miraliog\pelegram\Concerns\Accessors;

trait CommandAccessor
{
    /**
     * @return array{0: string, 1: string|null}
     */
    public function commandParts(): array
    {
        $text    = trim($this->text() ?? '');
        $text    = ltrim($text, '/');
        $parts   = explode(' ', $text, 2);
        $command = strtolower(explode('@', $parts[0])[0]);
        $payload = isset($parts[1]) ? trim($parts[1]) : null;
        return [$command, $payload !== '' ? $payload : null];
    }

    public function command(): ?string
    {
        if (!$this->isCommand()) return null;
        return $this->commandParts()[0];
    }

    public function commandPayload(): ?string
    {
        if (!$this->isCommand()) return null;
        return $this->commandParts()[1];
    }
}
