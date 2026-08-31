<?php

namespace Miraliog\Pelegram\Concerns\Accessors;

trait MessageAccessor
{
    public function updateId(): int
    {
        return $this->raw['update_id'];
    }

    public function messageId(): ?int
    {
        return $this->raw['message']['message_id']
            ?? $this->raw['callback_query']['message']['message_id']
            ?? $this->raw['edited_message']['message_id']
            ?? null;
    }

    public function text(): ?string
    {
        return $this->raw['message']['text']
            ?? $this->raw['edited_message']['text']
            ?? $this->raw['business_message']['text']
            ?? null;
    }

    public function caption(): ?string
    {
        return $this->raw['message']['caption']
            ?? $this->raw['edited_message']['caption']
            ?? null;
    }

    public function date(): ?int
    {
        return $this->raw['message']['date']
            ?? $this->raw['edited_message']['date']
            ?? null;
    }

    public function entities(): array
    {
        return $this->raw['message']['entities'] ?? [];
    }

    public function webAppData(): ?string
    {
        return $this->raw['message']['web_app_data']['data'] ?? null;
    }

    public function newChatMembers(): ?array
    {
        return $this->raw['message']['new_chat_members'] ?? null;
    }

    public function leftChatMember(): ?array
    {
        return $this->raw['message']['left_chat_member'] ?? null;
    }

    public function get(string $key): mixed
    {
        return $this->raw[$key] ?? null;
    }
}
