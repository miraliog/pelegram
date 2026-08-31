<?php

namespace Miraliog\Pelegram\Types;

use Miraliog\Pelegram\Bot;
use Miraliog\Pelegram\Types\Keyboard\Contracts\Keyboardable;

class Message
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
        string $text,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
        bool $disableWebPagePreview = false,
    ): array {
        return $this->bot->sendMessage(
            chatId: $this->chatId(),
            text: $text,
            replyMarkup: $replyMarkup,
            parseMode: $parseMode,
            disableWebPagePreview: $disableWebPagePreview,
        );
    }

    public function reply(
        string $text,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
        bool $disableWebPagePreview = false,
    ): array {
        return $this->bot->sendMessage(
            chatId: $this->chatId(),
            text: $text,
            replyMarkup: $replyMarkup,
            parseMode: $parseMode,
            disableWebPagePreview: $disableWebPagePreview,
            replyToMessageId: $this->messageId(),
        );
    }

    public function delete(): bool
    {
        return $this->bot->deleteMessage($this->chatId(), $this->messageId());
    }

    public function forward(int|string $toChatId): array
    {
        return $this->bot->forwardMessage($toChatId, $this->chatId(), $this->messageId());
    }

    public function copy(int|string $toChatId, ?string $caption = null): array
    {
        return $this->bot->copyMessage($toChatId, $this->chatId(), $this->messageId(), $caption);
    }

    public function pin(bool $disableNotification = false): bool
    {
        return $this->bot->pinChatMessage($this->chatId(), $this->messageId(), $disableNotification);
    }

    public function typing(): bool
    {
        return $this->bot->sendTyping($this->chatId());
    }

    // ==================== User ====================

    public function userId(): ?int
    {
        return $this->raw['from']['id'] ?? null;
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

    public function user(): ?array
    {
        return $this->raw['from'] ?? null;
    }

    // ==================== Chat ====================

    public function chatId(): int
    {
        return $this->raw['chat']['id'];
    }
    public function chatType(): string
    {
        return $this->raw['chat']['type'];
    }
    public function chatTitle(): ?string
    {
        return $this->raw['chat']['title'] ?? null;
    }
    public function chatUsername(): ?string
    {
        return $this->raw['chat']['username'] ?? null;
    }

    public function isPrivate(): bool
    {
        return $this->chatType() === 'private';
    }
    public function isGroup(): bool
    {
        return in_array($this->chatType(), ['group', 'supergroup'], true);
    }
    public function isChannel(): bool
    {
        return $this->chatType() === 'channel';
    }

    // ==================== Message ====================

    public function messageId(): int
    {
        return $this->raw['message_id'];
    }
    public function date(): int
    {
        return $this->raw['date'];
    }
    public function text(): ?string
    {
        return $this->raw['text'] ?? null;
    }
    public function caption(): ?string
    {
        return $this->raw['caption'] ?? null;
    }
    public function entities(): array
    {
        return $this->raw['entities'] ?? [];
    }

    public function textOrCaption(): ?string
    {
        return $this->text() ?? $this->caption();
    }

    // ==================== Command ====================

    public function isCommand(): bool
    {
        $text = $this->text();
        return $text !== null && str_starts_with($text, '/');
    }

    /** @return array{0: string, 1: string|null} */
    public function commandParts(): array
    {
        $text    = ltrim(trim($this->text() ?? ''), '/');
        $parts   = explode(' ', $text, 2);
        $command = strtolower(explode('@', $parts[0])[0]);
        $payload = isset($parts[1]) ? trim($parts[1]) : null;
        return [$command, $payload !== '' ? $payload : null];
    }

    public function command(): ?string
    {
        return $this->isCommand() ? $this->commandParts()[0] : null;
    }
    public function commandPayload(): ?string
    {
        return $this->isCommand() ? $this->commandParts()[1] : null;
    }

    // ==================== Message types ====================

    public function isText(): bool
    {
        return isset($this->raw['text']) && !$this->isCommand();
    }
    public function isPhoto(): bool
    {
        return isset($this->raw['photo']);
    }
    public function isVideo(): bool
    {
        return isset($this->raw['video']);
    }
    public function isDocument(): bool
    {
        return isset($this->raw['document']);
    }
    public function isAudio(): bool
    {
        return isset($this->raw['audio']);
    }
    public function isVoice(): bool
    {
        return isset($this->raw['voice']);
    }
    public function isSticker(): bool
    {
        return isset($this->raw['sticker']);
    }
    public function isAnimation(): bool
    {
        return isset($this->raw['animation']);
    }
    public function isVideoNote(): bool
    {
        return isset($this->raw['video_note']);
    }
    public function isContact(): bool
    {
        return isset($this->raw['contact']);
    }
    public function isLocation(): bool
    {
        return isset($this->raw['location']);
    }
    public function isSuccessfulPayment(): bool
    {
        return isset($this->raw['successful_payment']);
    }
    public function isWebAppData(): bool
    {
        return isset($this->raw['web_app_data']);
    }

    // ==================== Media ====================

    public function photoMaxSizeFileId(): ?string
    {
        $photos = $this->raw['photo'] ?? null;
        if (empty($photos)) return null;
        return end($photos)['file_id'] ?? null;
    }

    public function videoFileId(): ?string
    {
        return $this->raw['video']['file_id'] ?? null;
    }
    public function documentFileId(): ?string
    {
        return $this->raw['document']['file_id'] ?? null;
    }
    public function audioFileId(): ?string
    {
        return $this->raw['audio']['file_id'] ?? null;
    }
    public function voiceFileId(): ?string
    {
        return $this->raw['voice']['file_id'] ?? null;
    }
    public function stickerFileId(): ?string
    {
        return $this->raw['sticker']['file_id'] ?? null;
    }
    public function animationFileId(): ?string
    {
        return $this->raw['animation']['file_id'] ?? null;
    }
    public function videoNoteFileId(): ?string
    {
        return $this->raw['video_note']['file_id'] ?? null;
    }

    // ==================== Contact ====================

    public function contact(): ?array
    {
        return $this->raw['contact'] ?? null;
    }
    public function contactPhoneNumber(): ?string
    {
        return $this->raw['contact']['phone_number'] ?? null;
    }
    public function contactUserId(): ?int
    {
        return $this->raw['contact']['user_id'] ?? null;
    }
    public function contactFirstName(): ?string
    {
        return $this->raw['contact']['first_name'] ?? null;
    }
    public function contactLastName(): ?string
    {
        return $this->raw['contact']['last_name'] ?? null;
    }

    // ==================== Location ====================

    public function location(): ?array
    {
        return $this->raw['location'] ?? null;
    }
    public function latitude(): ?float
    {
        return $this->raw['location']['latitude'] ?? null;
    }
    public function longitude(): ?float
    {
        return $this->raw['location']['longitude'] ?? null;
    }

    // ==================== Payment ====================

    public function successfulPayment(): ?array
    {
        return $this->raw['successful_payment'] ?? null;
    }
    public function successfulPaymentPayload(): ?string
    {
        return $this->raw['successful_payment']['invoice_payload'] ?? null;
    }

    // ==================== Web App ====================

    public function webAppData(): ?string
    {
        return $this->raw['web_app_data']['data'] ?? null;
    }

    // ==================== Chat member ====================

    public function newChatMembers(): ?array
    {
        return $this->raw['new_chat_members'] ?? null;
    }
    public function leftChatMember(): ?array
    {
        return $this->raw['left_chat_member'] ?? null;
    }

    // ==================== Reply ====================

    public function replyToMessage(): ?array
    {
        return $this->raw['reply_to_message'] ?? null;
    }

    // ==================== Raw ====================

    public function get(string $key): mixed
    {
        return $this->raw[$key] ?? null;
    }
}
