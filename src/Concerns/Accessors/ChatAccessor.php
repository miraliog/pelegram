<?php

namespace Miraliog\Pelegram\Concerns\Accessors;

trait ChatAccessor
{
    public function chatId(): ?int
    {
        return $this->raw['message']['chat']['id']
            ?? $this->raw['callback_query']['message']['chat']['id']
            ?? $this->raw['edited_message']['chat']['id']
            ?? $this->raw['channel_post']['chat']['id']
            ?? $this->raw['business_message']['chat']['id']
            ?? null;
    }

    public function chatType(): ?string
    {
        return $this->raw['message']['chat']['type']
            ?? $this->raw['callback_query']['message']['chat']['type']
            ?? $this->raw['edited_message']['chat']['type']
            ?? null;
    }

    public function chatTitle(): ?string
    {
        return $this->raw['message']['chat']['title']
            ?? $this->raw['callback_query']['message']['chat']['title']
            ?? null;
    }

    public function chatUsername(): ?string
    {
        return $this->raw['message']['chat']['username']
            ?? $this->raw['callback_query']['message']['chat']['username']
            ?? null;
    }
}
