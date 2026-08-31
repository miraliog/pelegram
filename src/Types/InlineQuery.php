<?php

namespace Miraliog\Pelegram\Types;

use Miraliog\Pelegram\Bot;

class InlineQuery
{
    public readonly Bot $bot;

    public function __construct(
        public readonly array $raw,
        Bot $bot,
    ) {
        $this->bot = $bot;
    }

    // ==================== Actions ====================

    public function answer(
        array $results,
        int $cacheTime = 300,
        bool $isPersonal = false,
        ?string $nextOffset = null,
    ): bool {
        return $this->bot->answerInlineQuery(
            inlineQueryId: $this->id(),
            results: $results,
            cacheTime: $cacheTime,
            isPersonal: $isPersonal,
            nextOffset: $nextOffset,
        );
    }

    // ==================== Accessors ====================

    public function id(): string
    {
        return $this->raw['id'];
    }
    public function query(): string
    {
        return $this->raw['query'];
    }
    public function offset(): string
    {
        return $this->raw['offset'];
    }
    public function chatType(): ?string
    {
        return $this->raw['chat_type'] ?? null;
    }

    // ==================== User ====================

    public function userId(): int
    {
        return $this->raw['from']['id'];
    }
    public function firstName(): ?string
    {
        return $this->raw['from']['first_name'] ?? null;
    }
    public function lastName(): ?string
    {
        return $this->raw['from']['last_name'] ?? null;
    }
    public function username(): ?string
    {
        return $this->raw['from']['username'] ?? null;
    }
    public function user(): array
    {
        return $this->raw['from'];
    }

    // ==================== Raw ====================

    public function get(string $key): mixed
    {
        return $this->raw[$key] ?? null;
    }
}
