<?php

namespace Miraliog\Pelegram\Concerns\Accessors;

trait ContactAccessor
{
    public function contact(): ?array
    {
        return $this->raw['message']['contact'] ?? null;
    }
    public function contactPhoneNumber(): ?string
    {
        return $this->raw['message']['contact']['phone_number'] ?? null;
    }
    public function contactUserId(): ?int
    {
        return $this->raw['message']['contact']['user_id'] ?? null;
    }
    public function contactFirstName(): ?string
    {
        return $this->raw['message']['contact']['first_name'] ?? null;
    }
    public function contactLastName(): ?string
    {
        return $this->raw['message']['contact']['last_name'] ?? null;
    }
}
