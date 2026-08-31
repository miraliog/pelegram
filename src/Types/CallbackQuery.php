<?php

namespace Miraliog\Pelegram\Types;

use Miraliog\Pelegram\Bot;
use Miraliog\Pelegram\Types\Contracts\Keyboardable;

class CallbackQuery
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
        string $text = '',
        bool $showAlert = false,
        ?string $url = null,
        int $cacheTime = 0,
    ): bool {
        return $this->bot->answerCallbackQuery(
            callbackQueryId: $this->id(),
            text: $text !== '' ? $text : null,
            showAlert: $showAlert,
            url: $url,
            cacheTime: $cacheTime,
        );
    }

    public function answerAlert(string $text): bool
    {
        return $this->answer($text, showAlert: true);
    }

    public function edit(
        string $text,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
    ): array {
        return $this->bot->editMessageText(
            chatId: $this->chatId(),
            messageId: $this->messageId(),
            text: $text,
            replyMarkup: $replyMarkup,
            parseMode: $parseMode,
        );
    }

    public function editMarkup(Keyboardable|array|null $replyMarkup = null): array
    {
        return $this->bot->editMessageReplyMarkup(
            chatId: $this->chatId(),
            messageId: $this->messageId(),
            replyMarkup: $replyMarkup,
        );
    }

    public function delete(): bool
    {
        return $this->bot->deleteMessage($this->chatId(), $this->messageId());
    }

    /** answer + edit در یه خط */
    public function answerAndEdit(
        string $editText,
        string $answerText = '',
        bool $showAlert = false,
        Keyboardable|array|null $replyMarkup = null,
    ): void {
        $this->answer($answerText, $showAlert);
        $this->edit($editText, $replyMarkup);
    }

    // ==================== IDs ====================

    public function id(): string
    {
        return $this->raw['id'];
    }
    public function data(): ?string
    {
        return $this->raw['data'] ?? null;
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
    public function languageCode(): ?string
    {
        return $this->raw['from']['language_code'] ?? null;
    }
    public function isPremium(): bool
    {
        return ($this->raw['from']['is_premium'] ?? false) === true;
    }

    public function fullName(): ?string
    {
        $first = $this->firstName();
        if ($first === null) return null;
        $last  = $this->lastName();
        return $last ? trim("{$first} {$last}") : $first;
    }

    public function user(): array
    {
        return $this->raw['from'];
    }

    // ==================== Message ====================

    public function messageId(): int
    {
        return $this->raw['message']['message_id'];
    }
    public function chatId(): int
    {
        return $this->raw['message']['chat']['id'];
    }
    public function chatType(): string
    {
        return $this->raw['message']['chat']['type'];
    }
    public function messageText(): ?string
    {
        return $this->raw['message']['text'] ?? null;
    }
    public function messageCaption(): ?string
    {
        return $this->raw['message']['caption'] ?? null;
    }

    public function message(): ?array
    {
        return $this->raw['message'] ?? null;
    }

    // ==================== Raw ====================

    public function get(string $key): mixed
    {
        return $this->raw[$key] ?? null;
    }
}
