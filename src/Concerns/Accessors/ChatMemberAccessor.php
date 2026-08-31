<?php

namespace Miraliog\Pelegram\Concerns\Accessors;

trait ChatMemberAccessor
{
    public function chatShared(): ?array
    {
        return $this->raw['message']['chat_shared'] ?? null;
    }
    public function chatJoinRequest(): ?array
    {
        return $this->raw['chat_join_request'] ?? null;
    }
    public function myChatMember(): ?array
    {
        return $this->raw['my_chat_member'] ?? null;
    }
}
