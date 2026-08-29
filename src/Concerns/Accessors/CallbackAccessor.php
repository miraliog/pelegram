<?php

namespace miraliog\pelegram\Concerns\Accessors;

trait CallbackAccessor
{
    public function callbackQueryId(): ?string
    {
        return $this->raw['callback_query']['id'] ?? null;
    }
    public function callbackData(): ?string
    {
        return $this->raw['callback_query']['data'] ?? null;
    }
    public function callbackMessageText(): ?string
    {
        return $this->raw['callback_query']['message']['text'] ?? null;
    }
    public function callbackMessageCaption(): ?string
    {
        return $this->raw['callback_query']['message']['caption'] ?? null;
    }
}
