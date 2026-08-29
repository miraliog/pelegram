<?php

namespace miraliog\pelegram\Concerns\Accessors;

trait InlineQueryAccessor
{
    public function inlineQueryId(): ?string
    {
        return $this->raw['inline_query']['id'] ?? null;
    }
    public function inlineQueryText(): ?string
    {
        return $this->raw['inline_query']['query'] ?? null;
    }
    public function inlineQueryOffset(): ?string
    {
        return $this->raw['inline_query']['offset'] ?? null;
    }
}
