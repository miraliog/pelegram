<?php

namespace miraliog\pelegram\Concerns\Update;

trait DetectsChatType
{
    public function isPrivateChat(): bool
    {
        $type = $this->raw['message']['chat']['type']
            ?? $this->raw['callback_query']['message']['chat']['type']
            ?? null;

        if ($type !== null) {
            return $type === 'private';
        }

        return $this->isCallbackQuery() || $this->isPreCheckoutQuery();
    }

    public function isGroupChat(): bool
    {
        $type = $this->chatType();
        return $type === 'group' || $type === 'supergroup';
    }

    public function isChannelChat(): bool
    {
        return $this->chatType() === 'channel';
    }
}
