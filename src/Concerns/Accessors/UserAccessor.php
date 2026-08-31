<?php

namespace Miraliog\Pelegram\Concerns\Accessors;

trait UserAccessor
{
    public function userId(): ?int
    {
        return $this->raw['message']['from']['id']
            ?? $this->raw['callback_query']['from']['id']
            ?? $this->raw['pre_checkout_query']['from']['id']
            ?? $this->raw['shipping_query']['from']['id']
            ?? $this->raw['inline_query']['from']['id']
            ?? $this->raw['chosen_inline_result']['from']['id']
            ?? $this->raw['poll_answer']['user']['id']
            ?? $this->raw['my_chat_member']['from']['id']
            ?? $this->raw['chat_member']['from']['id']
            ?? $this->raw['chat_join_request']['from']['id']
            ?? $this->raw['message_reaction']['user']['id']
            ?? $this->raw['business_message']['from']['id']
            ?? $this->raw['edited_business_message']['from']['id']
            ?? $this->raw['edited_message']['from']['id']
            ?? null;
    }

    public function firstName(): ?string
    {
        return $this->raw['message']['from']['first_name']
            ?? $this->raw['callback_query']['from']['first_name']
            ?? $this->raw['inline_query']['from']['first_name']
            ?? null;
    }

    public function lastName(): ?string
    {
        return $this->raw['message']['from']['last_name']
            ?? $this->raw['callback_query']['from']['last_name']
            ?? $this->raw['inline_query']['from']['last_name']
            ?? null;
    }

    public function fullName(): ?string
    {
        $first = $this->firstName();
        if ($first === null) return null;
        $last = $this->lastName();
        return $last ? trim("{$first} {$last}") : $first;
    }

    public function username(): ?string
    {
        return $this->raw['message']['from']['username']
            ?? $this->raw['callback_query']['from']['username']
            ?? $this->raw['inline_query']['from']['username']
            ?? null;
    }

    public function languageCode(): ?string
    {
        return $this->raw['message']['from']['language_code']
            ?? $this->raw['callback_query']['from']['language_code']
            ?? null;
    }

    public function isPremium(): bool
    {
        return ($this->raw['message']['from']['is_premium']
            ?? $this->raw['callback_query']['from']['is_premium']
            ?? false) === true;
    }

    public function user(): ?array
    {
        return $this->raw['message']['from']
            ?? $this->raw['callback_query']['from']
            ?? null;
    }
}
