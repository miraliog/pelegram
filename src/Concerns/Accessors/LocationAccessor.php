<?php

namespace miraliog\pelegram\Concerns\Accessors;

trait LocationAccessor
{
    public function location(): ?array
    {
        return $this->raw['message']['location'] ?? null;
    }
    public function latitude(): ?float
    {
        return $this->raw['message']['location']['latitude'] ?? null;
    }
    public function longitude(): ?float
    {
        return $this->raw['message']['location']['longitude'] ?? null;
    }
}
