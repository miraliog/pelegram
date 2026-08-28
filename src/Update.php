<?php

namespace miraliog\pelegram;


class Update
{
    public function __construct(public readonly array $raw) {}

    // ==================== Update type detection ====================

    public function isMessage(): bool
    {
        return isset($this->raw['message']);
    }
    public function isEditedMessage(): bool
    {
        return isset($this->raw['edited_message']);
    }
    public function isChannelPost(): bool
    {
        return isset($this->raw['channel_post']);
    }
    public function isEditedChannelPost(): bool
    {
        return isset($this->raw['edited_channel_post']);
    }
    public function isCallbackQuery(): bool
    {
        return isset($this->raw['callback_query']);
    }
    public function isInlineQuery(): bool
    {
        return isset($this->raw['inline_query']);
    }
    public function isChosenInlineResult(): bool
    {
        return isset($this->raw['chosen_inline_result']);
    }
    public function isShippingQuery(): bool
    {
        return isset($this->raw['shipping_query']);
    }
    public function isPreCheckoutQuery(): bool
    {
        return isset($this->raw['pre_checkout_query']);
    }
    public function isPoll(): bool
    {
        return isset($this->raw['poll']);
    }
    public function isPollAnswer(): bool
    {
        return isset($this->raw['poll_answer']);
    }
    public function isMyChatMember(): bool
    {
        return isset($this->raw['my_chat_member']);
    }
    public function isChatMember(): bool
    {
        return isset($this->raw['chat_member']);
    }
    public function isChatJoinRequest(): bool
    {
        return isset($this->raw['chat_join_request']);
    }
    public function isChatBoost(): bool
    {
        return isset($this->raw['chat_boost']);
    }
    public function isRemovedChatBoost(): bool
    {
        return isset($this->raw['removed_chat_boost']);
    }
    public function isMessageReaction(): bool
    {
        return isset($this->raw['message_reaction']);
    }
    public function isMessageReactionCount(): bool
    {
        return isset($this->raw['message_reaction_count']);
    }
    public function isBusinessConnection(): bool
    {
        return isset($this->raw['business_connection']);
    }
    public function isBusinessMessage(): bool
    {
        return isset($this->raw['business_message']);
    }
    public function isEditedBusinessMessage(): bool
    {
        return isset($this->raw['edited_business_message']);
    }
    public function isDeletedBusinessMessages(): bool
    {
        return isset($this->raw['deleted_business_messages']);
    }
    public function isPurchasedPaidMedia(): bool
    {
        return isset($this->raw['purchased_paid_media']);
    }
    public function isGuestMessage(): bool
    {
        return isset($this->raw['guest_message']);
    }
    public function isManagedBot(): bool
    {
        return isset($this->raw['managed_bot']);
    }
    public function isSubscription(): bool
    {
        return isset($this->raw['subscription']);
    }

    // ==================== Message sub-type detection ====================

    public function isSuccessfulPayment(): bool
    {
        return isset($this->raw['message']['successful_payment']);
    }

    public function isContact(): bool
    {
        return isset($this->raw['message']['contact']);
    }

    public function isLocation(): bool
    {
        return isset($this->raw['message']['location']);
    }

    public function isPhoto(): bool
    {
        return isset($this->raw['message']['photo']);
    }

    public function isVideo(): bool
    {
        return isset($this->raw['message']['video']);
    }

    public function isDocument(): bool
    {
        return isset($this->raw['message']['document']);
    }

    public function isAudio(): bool
    {
        return isset($this->raw['message']['audio']);
    }

    public function isVoice(): bool
    {
        return isset($this->raw['message']['voice']);
    }

    public function isSticker(): bool
    {
        return isset($this->raw['message']['sticker']);
    }

    public function isAnimation(): bool
    {
        return isset($this->raw['message']['animation']);
    }

    public function isVideoNote(): bool
    {
        return isset($this->raw['message']['video_note']);
    }

    public function isCommand(): bool
    {
        $text = $this->text();
        return $text !== null && str_starts_with($text, '/');
    }

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

    // ==================== Update ID ====================

    public function updateId(): int
    {
        return $this->raw['update_id'];
    }

    // ==================== User ====================

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

    // ==================== Chat ====================

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

    // ==================== Message ====================

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

    // ==================== Command ====================

    /**
     * Returns [command, payload] — e.g. /start ref123 → ['start', 'ref123']
     * @return array{0: string, 1: string|null}
     */
    public function commandParts(): array
    {
        $text    = trim($this->text() ?? '');
        $text    = ltrim($text, '/');
        $parts   = explode(' ', $text, 2);
        $command = strtolower(explode('@', $parts[0])[0]);
        $payload = isset($parts[1]) ? trim($parts[1]) : null;
        return [$command, $payload !== '' ? $payload : null];
    }

    public function command(): ?string
    {
        if (!$this->isCommand()) return null;
        return $this->commandParts()[0];
    }

    public function commandPayload(): ?string
    {
        if (!$this->isCommand()) return null;
        return $this->commandParts()[1];
    }

    // ==================== Media ====================

    public function photoMaxSizeFileId(): ?string
    {
        $photos = $this->raw['message']['photo'] ?? null;
        if (empty($photos)) return null;
        return end($photos)['file_id'] ?? null;
    }

    public function videoFileId(): ?string
    {
        return $this->raw['message']['video']['file_id'] ?? null;
    }

    public function documentFileId(): ?string
    {
        return $this->raw['message']['document']['file_id'] ?? null;
    }

    public function audioFileId(): ?string
    {
        return $this->raw['message']['audio']['file_id'] ?? null;
    }

    public function voiceFileId(): ?string
    {
        return $this->raw['message']['voice']['file_id'] ?? null;
    }

    public function stickerFileId(): ?string
    {
        return $this->raw['message']['sticker']['file_id'] ?? null;
    }

    public function animationFileId(): ?string
    {
        return $this->raw['message']['animation']['file_id'] ?? null;
    }

    public function videoNoteFileId(): ?string
    {
        return $this->raw['message']['video_note']['file_id'] ?? null;
    }

    // ==================== Contact ====================

    public function contact(): ?array
    {
        return $this->raw['message']['contact'] ?? null;
    }

    public function contactPhoneNumber(): ?string
    {
        return $this->raw['message']['contact']['phone_number'] ?? null;
    }

    public function contactUserId(): ?int
    {
        return $this->raw['message']['contact']['user_id'] ?? null;
    }

    // ==================== Location ====================

    public function location(): ?array
    {
        return $this->raw['message']['location'] ?? null;
    }

    public function latitude(): ?float
    {
        return $this->raw['message']['location']['latitude'] ?? null;
    }

    public function longitude(): ?float
    {
        return $this->raw['message']['location']['longitude'] ?? null;
    }

    // ==================== Callback Query ====================

    public function callbackQueryId(): ?string
    {
        return $this->raw['callback_query']['id'] ?? null;
    }

    public function callbackData(): ?string
    {
        return $this->raw['callback_query']['data'] ?? null;
    }

    public function callbackMessageCaption(): ?string
    {
        return $this->raw['callback_query']['message']['caption'] ?? null;
    }

    public function callbackMessageText(): ?string
    {
        return $this->raw['callback_query']['message']['text'] ?? null;
    }

    // ==================== Inline Query ====================

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

    // ==================== Payments ====================

    public function preCheckoutQueryId(): ?string
    {
        return $this->raw['pre_checkout_query']['id'] ?? null;
    }

    public function successfulPayment(): ?array
    {
        return $this->raw['message']['successful_payment'] ?? null;
    }

    public function successfulPaymentPayload(): ?string
    {
        return $this->raw['message']['successful_payment']['invoice_payload'] ?? null;
    }

    // ==================== Chat Member ====================

    public function chatShared(): ?array
    {
        return $this->raw['message']['chat_shared'] ?? null;
    }

    public function newChatMembers(): ?array
    {
        return $this->raw['message']['new_chat_members'] ?? null;
    }

    public function leftChatMember(): ?array
    {
        return $this->raw['message']['left_chat_member'] ?? null;
    }

    public function chatJoinRequest(): ?array
    {
        return $this->raw['chat_join_request'] ?? null;
    }

    public function myChatMember(): ?array
    {
        return $this->raw['my_chat_member'] ?? null;
    }

    // ==================== Web App ====================

    public function webAppData(): ?string
    {
        return $this->raw['message']['web_app_data']['data'] ?? null;
    }

    // ==================== Raw access ====================

    public function get(string $key): mixed
    {
        return $this->raw[$key] ?? null;
    }
}
