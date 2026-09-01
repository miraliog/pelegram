<?php

namespace Miraliog\Pelegram;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Miraliog\Pelegram\Exceptions\PelegramException;
use Miraliog\Pelegram\Types\Keyboard\ReplyKeyboardMarkup;
use Miraliog\Pelegram\Types\Keyboard\InlineKeyboardMarkup;
use Miraliog\Pelegram\Types\Keyboard\ReplyKeyboardRemove;
use Miraliog\Pelegram\Enums\ParseMode;

/**
 * Telegram Bot API wrapper
 * Bot API 10.3
 */
class Bot
{
    private Client $http;
    private string $baseUrl;
    private ?ParseMode $defaultParseMode = ParseMode::HTML;

    public function __construct(
        private readonly string $token,
        ?ParseMode $defaultParseMode = null,
    ) {
        $this->baseUrl = "https://api.telegram.org/bot{$token}/";
        $this->http = new Client([
            'timeout' => 30,
        ]);
        $this->defaultParseMode = $defaultParseMode;
    }


    // ==================== Core ====================

    /**
     * @throws PelegramException
     */
    public function call(string $method, array $params = []): array
    {
        try {
            $params  = array_filter($params, fn($v) => $v !== null);
            $hasFile = $this->hasFile($params);
            $options = $hasFile
                ? ['multipart' => $this->toMultipart($params)]
                : ['json' => $params];

            $response = $this->http->post($this->baseUrl . $method, $options);
            $result   = json_decode($response->getBody()->getContents(), true);

            if (!($result['ok'] ?? false)) {
                throw new PelegramException(
                    $result['description'] ?? 'Unknown error',
                    $result['error_code']  ?? 0,
                    $result['description'] ?? null,
                );
            }

            return $result['result'] ?? [];
        } catch (GuzzleException $e) {
            throw new PelegramException("HTTP error: {$e->getMessage()}", $e->getCode(), null, $e);
        }
    }

    // ==================== Webhook ====================

    public function getMe(): array
    {
        return $this->call('getMe');
    }

    public function setWebhook(
        string $url,
        ?string $secretToken = null,
        ?int $maxConnections = null,
        ?array $allowedUpdates = null,
        bool $dropPendingUpdates = false,
    ): bool {
        return (bool) $this->call('setWebhook', [
            'url'                  => $url,
            'secret_token'         => $secretToken,
            'max_connections'      => $maxConnections,
            'allowed_updates'      => $allowedUpdates ? json_encode($allowedUpdates) : null,
            'drop_pending_updates' => $dropPendingUpdates ?: null,
        ]);
    }

    public function deleteWebhook(bool $dropPendingUpdates = false): bool
    {
        return (bool) $this->call('deleteWebhook', [
            'drop_pending_updates' => $dropPendingUpdates ?: null,
        ]);
    }

    public function getWebhookInfo(): array
    {
        return $this->call('getWebhookInfo');
    }

    // ==================== Sending messages ====================

    public function sendMessage(
        string $text,
        int|string|null $chatId = null,
        ?int $messageThreadId = null,
        ParseMode|null $parseMode = null,
        ?array $entities = null,
        ?bool $disableWebPagePreview = null,
        ?bool $linkPreviewOptions = null,
        ?bool $disableNotification = null,
        ?bool $protectContent = null,
        ?int $replyToMessageId = null,
        ?bool $allowSendingWithoutReply = null,
        ?array $replyParameters = null,
        InlineKeyboardMarkup|ReplyKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ?string $businessConnectionId = null,
        ?string $messageEffectId = null,
        ?bool $allowPaidBroadcast = null,
        ?int $directMessagesTopicId = null,
        ?array $suggestedPostParameters = null,
        ?int $receiverUserId = null,
        ?string $callbackQueryId = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call(
            'sendMessage',
            [
                'business_connection_id'      => $businessConnectionId,
                'chat_id'                     => $chatId,
                'message_thread_id'           => $messageThreadId,
                'text'                        => $text,
                'parse_mode'                  => $parseMode ?? $this->defaultParseMode,
                'entities'                    => $entities ? json_encode($entities) : null,
                'disable_web_page_preview'     => $disableWebPagePreview ?: null,
                'link_preview_options'        => $linkPreviewOptions ? json_encode($linkPreviewOptions) : null,
                'disable_notification'        => $disableNotification ?: null,
                'protect_content'             => $protectContent ?: null,
                'reply_to_message_id'          => $replyToMessageId,
                'allow_sending_without_reply' => $allowSendingWithoutReply ?: null,
                'allow_paid_broadcast'        => $allowPaidBroadcast ?: null,
                'direct_messages_topic_id'    => $directMessagesTopicId,
                'suggested_post_parameters'   => $suggestedPostParameters ? json_encode($suggestedPostParameters) : null,
                'receiver_user_id'             => $receiverUserId,
                'callback_query_id'            => $callbackQueryId,
                'message_effect_id'           => $messageEffectId,
                'reply_parameters'            => $replyParameters ? json_encode($replyParameters) : null,
                'reply_markup'                => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
                'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
            ]
        );
    }

    public function sendPhoto(
        int|string $chatId,
        string|\CURLFile $photo,
        ?string $caption = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ParseMode|null $parseMode = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $showCaptionAboveMedia = false,
        bool $hasSpoiler = false,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendPhoto', [
            'business_connection_id'       => $businessConnectionId,
            'chat_id'                      => $chatId,
            'message_thread_id'            => $messageThreadId,
            'photo'                        => $photo,
            'caption'                      => $caption,
            'parse_mode'                   => $parseMode ?? $this->defaultParseMode,
            'show_caption_above_media'     => $showCaptionAboveMedia ?: null,
            'has_spoiler'                  => $hasSpoiler ?: null,
            'disable_notification'         => $disableNotification ?: null,
            'protect_content'              => $protectContent ?: null,
            'message_effect_id'            => $messageEffectId,
            'reply_parameters'             => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                 => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendVideo(
        int|string $chatId,
        string|\CURLFile $video,
        ?string $caption = null,
        ?int $duration = null,
        ?int $width = null,
        ?int $height = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ParseMode|null $parseMode = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $showCaptionAboveMedia = false,
        bool $hasSpoiler = false,
        bool $supportsStreaming = false,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendVideo', [
            'business_connection_id'       => $businessConnectionId,
            'chat_id'                      => $chatId,
            'message_thread_id'            => $messageThreadId,
            'video'                        => $video,
            'duration'                     => $duration,
            'width'                        => $width,
            'height'                       => $height,
            'caption'                      => $caption,
            'parse_mode'                   => $parseMode ?? $this->defaultParseMode,
            'show_caption_above_media'     => $showCaptionAboveMedia ?: null,
            'has_spoiler'                  => $hasSpoiler ?: null,
            'supports_streaming'           => $supportsStreaming ?: null,
            'disable_notification'         => $disableNotification ?: null,
            'protect_content'              => $protectContent ?: null,
            'message_effect_id'            => $messageEffectId,
            'reply_parameters'             => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                 => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendAudio(
        int|string $chatId,
        string|\CURLFile $audio,
        ?string $caption = null,
        ?string $title = null,
        ?string $performer = null,
        ?int $duration = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ParseMode|null $parseMode = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendAudio', [
            'business_connection_id'       => $businessConnectionId,
            'chat_id'                      => $chatId,
            'message_thread_id'            => $messageThreadId,
            'audio'                        => $audio,
            'caption'                      => $caption,
            'parse_mode'                   => $parseMode ?? $this->defaultParseMode,
            'duration'                     => $duration,
            'performer'                    => $performer,
            'title'                        => $title,
            'disable_notification'         => $disableNotification ?: null,
            'protect_content'              => $protectContent ?: null,
            'message_effect_id'            => $messageEffectId,
            'reply_parameters'             => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                 => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendDocument(
        int|string $chatId,
        string|\CURLFile $document,
        ?string $caption = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ParseMode|null $parseMode = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $disableContentTypeDetection = false,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendDocument', [
            'business_connection_id'          => $businessConnectionId,
            'chat_id'                         => $chatId,
            'message_thread_id'               => $messageThreadId,
            'document'                        => $document,
            'caption'                         => $caption,
            'parse_mode'                      => $parseMode ?? $this->defaultParseMode,
            'disable_content_type_detection'  => $disableContentTypeDetection ?: null,
            'disable_notification'            => $disableNotification ?: null,
            'protect_content'                 => $protectContent ?: null,
            'message_effect_id'               => $messageEffectId,
            'reply_parameters'                => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                    => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters'    => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendVoice(
        int|string $chatId,
        string|\CURLFile $voice,
        ?string $caption = null,
        ?int $duration = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ParseMode|null $parseMode = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendVoice', [
            'business_connection_id'       => $businessConnectionId,
            'chat_id'                      => $chatId,
            'message_thread_id'            => $messageThreadId,
            'voice'                        => $voice,
            'caption'                      => $caption,
            'parse_mode'                   => $parseMode ?? $this->defaultParseMode,
            'duration'                     => $duration,
            'disable_notification'         => $disableNotification ?: null,
            'protect_content'              => $protectContent ?: null,
            'message_effect_id'            => $messageEffectId,
            'reply_parameters'             => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                 => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendVideoNote(
        int|string $chatId,
        string|\CURLFile $videoNote,
        ?int $duration = null,
        ?int $length = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendVideoNote', [
            'business_connection_id'       => $businessConnectionId,
            'chat_id'                      => $chatId,
            'message_thread_id'            => $messageThreadId,
            'video_note'                   => $videoNote,
            'duration'                     => $duration,
            'length'                       => $length,
            'disable_notification'         => $disableNotification ?: null,
            'protect_content'              => $protectContent ?: null,
            'reply_parameters'             => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                 => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendSticker(
        int|string $chatId,
        string|\CURLFile $sticker,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        ?string $emoji = null,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendSticker', [
            'business_connection_id'       => $businessConnectionId,
            'chat_id'                      => $chatId,
            'message_thread_id'            => $messageThreadId,
            'sticker'                      => $sticker,
            'emoji'                        => $emoji,
            'disable_notification'         => $disableNotification ?: null,
            'protect_content'              => $protectContent ?: null,
            'message_effect_id'            => $messageEffectId,
            'reply_parameters'             => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                 => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendAnimation(
        int|string $chatId,
        string|\CURLFile $animation,
        ?string $caption = null,
        ?int $duration = null,
        ?int $width = null,
        ?int $height = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ParseMode|null $parseMode = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $showCaptionAboveMedia = false,
        bool $hasSpoiler = false,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendAnimation', [
            'business_connection_id'       => $businessConnectionId,
            'chat_id'                      => $chatId,
            'message_thread_id'            => $messageThreadId,
            'animation'                    => $animation,
            'duration'                     => $duration,
            'width'                        => $width,
            'height'                       => $height,
            'caption'                      => $caption,
            'parse_mode'                   => $parseMode ?? $this->defaultParseMode,
            'show_caption_above_media'     => $showCaptionAboveMedia ?: null,
            'has_spoiler'                  => $hasSpoiler ?: null,
            'disable_notification'         => $disableNotification ?: null,
            'protect_content'              => $protectContent ?: null,
            'message_effect_id'            => $messageEffectId,
            'reply_parameters'             => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                 => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendLocation(
        int|string $chatId,
        float $latitude,
        float $longitude,
        ?int $livePeriod = null,
        ?int $heading = null,
        ?int $proximityAlertRadius = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        ?int $horizontalAccuracy = null,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendLocation', [
            'business_connection_id'       => $businessConnectionId,
            'chat_id'                      => $chatId,
            'message_thread_id'            => $messageThreadId,
            'latitude'                     => $latitude,
            'longitude'                    => $longitude,
            'horizontal_accuracy'          => $horizontalAccuracy,
            'live_period'                  => $livePeriod,
            'heading'                      => $heading,
            'proximity_alert_radius'       => $proximityAlertRadius,
            'disable_notification'         => $disableNotification ?: null,
            'protect_content'              => $protectContent ?: null,
            'message_effect_id'            => $messageEffectId,
            'reply_parameters'             => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                 => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendContact(
        int|string $chatId,
        string $phoneNumber,
        string $firstName,
        ?string $lastName = null,
        ?string $vcard = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
        ?array $ephemeralMessageParameters = null,
    ): array {
        return $this->call('sendContact', [
            'business_connection_id'       => $businessConnectionId,
            'chat_id'                      => $chatId,
            'message_thread_id'            => $messageThreadId,
            'phone_number'                 => $phoneNumber,
            'first_name'                   => $firstName,
            'last_name'                    => $lastName,
            'vcard'                        => $vcard,
            'disable_notification'         => $disableNotification ?: null,
            'protect_content'              => $protectContent ?: null,
            'message_effect_id'            => $messageEffectId,
            'reply_parameters'             => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'                 => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
            'ephemeral_message_parameters' => $ephemeralMessageParameters ? json_encode($ephemeralMessageParameters) : null,
        ]);
    }

    public function sendDice(
        int|string $chatId,
        string $emoji = '🎲',
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
    ): array {
        return $this->call('sendDice', [
            'business_connection_id' => $businessConnectionId,
            'chat_id'                => $chatId,
            'message_thread_id'      => $messageThreadId,
            'emoji'                  => $emoji,
            'disable_notification'   => $disableNotification ?: null,
            'protect_content'        => $protectContent ?: null,
            'message_effect_id'      => $messageEffectId,
            'reply_parameters'       => $replyParameters ? json_encode($replyParameters) : null,
        ]);
    }

    public function sendPoll(
        int|string $chatId,
        string $question,
        array $options,
        bool $isAnonymous = true,
        string $type = 'regular',
        bool $allowsMultipleAnswers = false,
        ?int $correctOptionId = null,
        ?string $explanation = null,
        ?int $openPeriod = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $isClosed = false,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
    ): array {
        return $this->call('sendPoll', [
            'business_connection_id'  => $businessConnectionId,
            'chat_id'                 => $chatId,
            'message_thread_id'       => $messageThreadId,
            'question'                => $question,
            'options'                 => json_encode($options),
            'is_anonymous'            => $isAnonymous,
            'type'                    => $type,
            'allows_multiple_answers' => $allowsMultipleAnswers ?: null,
            'correct_option_id'       => $correctOptionId,
            'explanation'             => $explanation,
            'open_period'             => $openPeriod,
            'is_closed'               => $isClosed ?: null,
            'disable_notification'    => $disableNotification ?: null,
            'protect_content'         => $protectContent ?: null,
            'message_effect_id'       => $messageEffectId,
            'reply_parameters'        => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'            => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
        ]);
    }

    public function sendMediaGroup(
        int|string $chatId,
        array $media,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
    ): array {
        return $this->call('sendMediaGroup', [
            'business_connection_id' => $businessConnectionId,
            'chat_id'                => $chatId,
            'message_thread_id'      => $messageThreadId,
            'media'                  => json_encode($media),
            'disable_notification'   => $disableNotification ?: null,
            'protect_content'        => $protectContent ?: null,
            'message_effect_id'      => $messageEffectId,
            'reply_parameters'       => $replyParameters ? json_encode($replyParameters) : null,
        ]);
    }

    public function sendChatAction(
        int|string $chatId,
        string $action,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
    ): bool {
        return (bool) $this->call('sendChatAction', [
            'business_connection_id' => $businessConnectionId,
            'chat_id'                => $chatId,
            'message_thread_id'      => $messageThreadId,
            'action'                 => $action,
        ]);
    }

    public function sendInvoice(
        int|string $chatId,
        string $title,
        string $description,
        string $payload,
        string $currency,
        array $prices,
        string $providerToken = '',
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ?int $messageThreadId = null,
        ?int $maxTipAmount = null,
        ?array $suggestedTipAmounts = null,
        bool $isFlexible = false,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?string $messageEffectId = null,
        ?array $replyParameters = null,
    ): array {
        return $this->call('sendInvoice', [
            'chat_id'               => $chatId,
            'message_thread_id'     => $messageThreadId,
            'title'                 => $title,
            'description'           => $description,
            'payload'               => $payload,
            'provider_token'        => $providerToken,
            'currency'              => $currency,
            'prices'                => json_encode($prices),
            'max_tip_amount'        => $maxTipAmount,
            'suggested_tip_amounts' => $suggestedTipAmounts ? json_encode($suggestedTipAmounts) : null,
            'is_flexible'           => $isFlexible ?: null,
            'disable_notification'  => $disableNotification ?: null,
            'protect_content'       => $protectContent ?: null,
            'message_effect_id'     => $messageEffectId,
            'reply_parameters'      => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'          => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
        ]);
    }

    // ==================== Editing messages ====================

    public function editMessageText(
        int|string $chatId,
        int $messageId,
        string $text,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ParseMode|null $parseMode = null,
        ?string $businessConnectionId = null,
        ?array $entities = null,
        ?array $linkPreviewOptions = null,
    ): array {
        return $this->call('editMessageText', [
            'business_connection_id' => $businessConnectionId,
            'chat_id'                => $chatId,
            'message_id'             => $messageId,
            'text'                   => $text,
            'parse_mode'             => $parseMode ?? $this->defaultParseMode,
            'entities'               => $entities ? json_encode($entities) : null,
            'link_preview_options'   => $linkPreviewOptions ? json_encode($linkPreviewOptions) : null,
            'reply_markup'           => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
        ]);
    }

    public function editMessageCaption(
        int|string $chatId,
        int $messageId,
        string $caption,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ParseMode|null $parseMode = null,
        ?string $businessConnectionId = null,
        bool $showCaptionAboveMedia = false,
    ): array {
        return $this->call('editMessageCaption', [
            'business_connection_id'  => $businessConnectionId,
            'chat_id'                 => $chatId,
            'message_id'              => $messageId,
            'caption'                 => $caption,
            'parse_mode'              => $parseMode ?? $this->defaultParseMode,
            'show_caption_above_media' => $showCaptionAboveMedia ?: null,
            'reply_markup'            => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
        ]);
    }

    public function editMessageReplyMarkup(
        int|string $chatId,
        int $messageId,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ?string $businessConnectionId = null,
    ): array {
        return $this->call('editMessageReplyMarkup', [
            'business_connection_id' => $businessConnectionId,
            'chat_id'                => $chatId,
            'message_id'             => $messageId,
            'reply_markup'           => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
        ]);
    }

    public function editMessageMedia(
        int|string $chatId,
        int $messageId,
        array $media,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ?string $businessConnectionId = null,
    ): array {
        return $this->call('editMessageMedia', [
            'business_connection_id' => $businessConnectionId,
            'chat_id'                => $chatId,
            'message_id'             => $messageId,
            'media'                  => json_encode($media),
            'reply_markup'           => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
        ]);
    }

    public function deleteMessage(int|string $chatId, int $messageId): bool
    {
        return (bool) $this->call('deleteMessage', [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
        ]);
    }

    public function deleteMessages(int|string $chatId, array $messageIds): bool
    {
        return (bool) $this->call('deleteMessages', [
            'chat_id'     => $chatId,
            'message_ids' => json_encode($messageIds),
        ]);
    }

    public function forwardMessage(
        int|string $chatId,
        int|string $fromChatId,
        int $messageId,
        ?int $messageThreadId = null,
        bool $disableNotification = false,
        bool $protectContent = false,
    ): array {
        return $this->call('forwardMessage', [
            'chat_id'              => $chatId,
            'message_thread_id'    => $messageThreadId,
            'from_chat_id'         => $fromChatId,
            'message_id'           => $messageId,
            'disable_notification' => $disableNotification ?: null,
            'protect_content'      => $protectContent ?: null,
        ]);
    }

    public function copyMessage(
        int|string $chatId,
        int|string $fromChatId,
        int $messageId,
        ?string $caption = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null,
        ParseMode|null $parseMode = null,
        ?int $messageThreadId = null,
        bool $showCaptionAboveMedia = false,
        bool $disableNotification = false,
        bool $protectContent = false,
        bool $allowPaidBroadcast = false,
        ?array $replyParameters = null,
    ): array {
        return $this->call('copyMessage', [
            'chat_id'                  => $chatId,
            'message_thread_id'        => $messageThreadId,
            'from_chat_id'             => $fromChatId,
            'message_id'               => $messageId,
            'caption'                  => $caption,
            'parse_mode'               => $parseMode ?? $this->defaultParseMode,
            'show_caption_above_media' => $showCaptionAboveMedia ?: null,
            'disable_notification'     => $disableNotification ?: null,
            'protect_content'          => $protectContent ?: null,
            'allow_paid_broadcast'     => $allowPaidBroadcast ?: null,
            'reply_parameters'         => $replyParameters ? json_encode($replyParameters) : null,
            'reply_markup'             => $replyMarkup ? json_encode($replyMarkup->toArray()) : null,
        ]);
    }

    public function pinChatMessage(
        int|string $chatId,
        int $messageId,
        bool $disableNotification = false,
        ?string $businessConnectionId = null,
    ): bool {
        return (bool) $this->call('pinChatMessage', [
            'business_connection_id' => $businessConnectionId,
            'chat_id'                => $chatId,
            'message_id'             => $messageId,
            'disable_notification'   => $disableNotification ?: null,
        ]);
    }

    public function unpinChatMessage(
        int|string $chatId,
        ?int $messageId = null,
        ?string $businessConnectionId = null,
    ): bool {
        return (bool) $this->call('unpinChatMessage', [
            'business_connection_id' => $businessConnectionId,
            'chat_id'                => $chatId,
            'message_id'             => $messageId,
        ]);
    }

    // ==================== Callback Query ====================

    public function answerCallbackQuery(
        string $callbackQueryId,
        ?string $text = null,
        bool $showAlert = false,
        ?string $url = null,
        int $cacheTime = 0,
    ): bool {
        return (bool) $this->call('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text'              => $text,
            'show_alert'        => $showAlert ?: null,
            'url'               => $url,
            'cache_time'        => $cacheTime ?: null,
        ]);
    }

    // ==================== Inline Query ====================

    public function answerInlineQuery(
        string $inlineQueryId,
        array $results,
        int $cacheTime = 300,
        bool $isPersonal = false,
        ?string $nextOffset = null,
        ?array $button = null,
    ): bool {
        return (bool) $this->call('answerInlineQuery', [
            'inline_query_id' => $inlineQueryId,
            'results'         => json_encode($results),
            'cache_time'      => $cacheTime,
            'is_personal'     => $isPersonal ?: null,
            'next_offset'     => $nextOffset,
            'button'          => $button ? json_encode($button) : null,
        ]);
    }

    // ==================== Payments ====================

    public function answerPreCheckoutQuery(
        string $preCheckoutQueryId,
        bool $ok,
        ?string $errorMessage = null,
    ): bool {
        return (bool) $this->call('answerPreCheckoutQuery', [
            'pre_checkout_query_id' => $preCheckoutQueryId,
            'ok'                    => $ok,
            'error_message'         => $errorMessage,
        ]);
    }

    public function answerShippingQuery(
        string $shippingQueryId,
        bool $ok,
        ?array $shippingOptions = null,
        ?string $errorMessage = null,
    ): bool {
        return (bool) $this->call('answerShippingQuery', [
            'shipping_query_id' => $shippingQueryId,
            'ok'                => $ok,
            'shipping_options'  => $shippingOptions ? json_encode($shippingOptions) : null,
            'error_message'     => $errorMessage,
        ]);
    }

    public function refundStarPayment(int $userId, string $telegramPaymentChargeId): bool
    {
        return (bool) $this->call('refundStarPayment', [
            'user_id'                    => $userId,
            'telegram_payment_charge_id' => $telegramPaymentChargeId,
        ]);
    }

    // ==================== Chat management ====================

    public function getChat(int|string $chatId): array
    {
        return $this->call('getChat', ['chat_id' => $chatId]);
    }

    public function getChatMember(int|string $chatId, int $userId): array
    {
        return $this->call('getChatMember', ['chat_id' => $chatId, 'user_id' => $userId]);
    }

    public function getChatMemberCount(int|string $chatId): int
    {
        return (int) $this->call('getChatMemberCount', ['chat_id' => $chatId]);
    }

    public function getChatAdministrators(int|string $chatId, bool $returnBots = false): array
    {
        return $this->call('getChatAdministrators', [
            'chat_id'     => $chatId,
            'return_bots' => $returnBots ?: null,
        ]);
    }

    public function banChatMember(
        int|string $chatId,
        int $userId,
        ?int $untilDate = null,
        bool $revokeMessages = false,
    ): bool {
        return (bool) $this->call('banChatMember', [
            'chat_id'         => $chatId,
            'user_id'         => $userId,
            'until_date'      => $untilDate,
            'revoke_messages' => $revokeMessages ?: null,
        ]);
    }

    public function unbanChatMember(int|string $chatId, int $userId, bool $onlyIfBanned = true): bool
    {
        return (bool) $this->call('unbanChatMember', [
            'chat_id'        => $chatId,
            'user_id'        => $userId,
            'only_if_banned' => $onlyIfBanned,
        ]);
    }

    public function restrictChatMember(
        int|string $chatId,
        int $userId,
        array $permissions,
        ?int $untilDate = null,
        bool $useIndependentChatPermissions = false,
    ): bool {
        return (bool) $this->call('restrictChatMember', [
            'chat_id'                          => $chatId,
            'user_id'                          => $userId,
            'permissions'                      => json_encode($permissions),
            'use_independent_chat_permissions' => $useIndependentChatPermissions ?: null,
            'until_date'                       => $untilDate,
        ]);
    }

    public function promoteChatMember(int|string $chatId, int $userId, array $rights = []): bool
    {
        return (bool) $this->call('promoteChatMember', array_merge(
            ['chat_id' => $chatId, 'user_id' => $userId],
            $rights,
        ));
    }

    public function exportChatInviteLink(int|string $chatId): string
    {
        return (string) $this->call('exportChatInviteLink', ['chat_id' => $chatId]);
    }

    public function createChatInviteLink(
        int|string $chatId,
        ?string $name = null,
        ?int $expireDate = null,
        ?int $memberLimit = null,
        bool $createsJoinRequest = false,
    ): array {
        return $this->call('createChatInviteLink', [
            'chat_id'              => $chatId,
            'name'                 => $name,
            'expire_date'          => $expireDate,
            'member_limit'         => $memberLimit,
            'creates_join_request' => $createsJoinRequest ?: null,
        ]);
    }

    public function approveChatJoinRequest(int|string $chatId, int $userId): bool
    {
        return (bool) $this->call('approveChatJoinRequest', ['chat_id' => $chatId, 'user_id' => $userId]);
    }

    public function declineChatJoinRequest(int|string $chatId, int $userId): bool
    {
        return (bool) $this->call('declineChatJoinRequest', ['chat_id' => $chatId, 'user_id' => $userId]);
    }

    public function setChatTitle(int|string $chatId, string $title): bool
    {
        return (bool) $this->call('setChatTitle', ['chat_id' => $chatId, 'title' => $title]);
    }

    public function setChatDescription(int|string $chatId, ?string $description): bool
    {
        return (bool) $this->call('setChatDescription', ['chat_id' => $chatId, 'description' => $description]);
    }

    public function leaveChat(int|string $chatId): bool
    {
        return (bool) $this->call('leaveChat', ['chat_id' => $chatId]);
    }

    public function setMessageReaction(
        int|string $chatId,
        int $messageId,
        ?array $reaction = null,
        bool $isBig = false,
    ): bool {
        return (bool) $this->call('setMessageReaction', [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
            'reaction'   => $reaction ? json_encode($reaction) : null,
            'is_big'     => $isBig ?: null,
        ]);
    }

    // ==================== Bot commands ====================

    public function setMyCommands(array $commands, ?array $scope = null, ?string $languageCode = null): bool
    {
        return (bool) $this->call('setMyCommands', [
            'commands'      => json_encode($commands),
            'scope'         => $scope ? json_encode($scope) : null,
            'language_code' => $languageCode,
        ]);
    }

    public function getMyCommands(?array $scope = null, ?string $languageCode = null): array
    {
        return $this->call('getMyCommands', [
            'scope'         => $scope ? json_encode($scope) : null,
            'language_code' => $languageCode,
        ]);
    }

    public function deleteMyCommands(?array $scope = null, ?string $languageCode = null): bool
    {
        return (bool) $this->call('deleteMyCommands', [
            'scope'         => $scope ? json_encode($scope) : null,
            'language_code' => $languageCode,
        ]);
    }

    public function setMyName(?string $name = null, ?string $languageCode = null): bool
    {
        return (bool) $this->call('setMyName', [
            'name'          => $name,
            'language_code' => $languageCode,
        ]);
    }

    public function setMyDescription(?string $description = null, ?string $languageCode = null): bool
    {
        return (bool) $this->call('setMyDescription', [
            'description'   => $description,
            'language_code' => $languageCode,
        ]);
    }

    public function setMyShortDescription(?string $shortDescription = null, ?string $languageCode = null): bool
    {
        return (bool) $this->call('setMyShortDescription', [
            'short_description' => $shortDescription,
            'language_code'     => $languageCode,
        ]);
    }

    // ==================== Files ====================

    public function getFile(string $fileId): array
    {
        return $this->call('getFile', ['file_id' => $fileId]);
    }

    public function getFileUrl(string $fileId): string
    {
        $file = $this->getFile($fileId);
        return "https://api.telegram.org/file/bot{$this->token}/{$file['file_path']}";
    }

    public function getUserProfilePhotos(int $userId, int $offset = 0, int $limit = 10): array
    {
        return $this->call('getUserProfilePhotos', [
            'user_id' => $userId,
            'offset'  => $offset,
            'limit'   => $limit,
        ]);
    }

    // ==================== Stickers ====================

    public function getStickerSet(string $name): array
    {
        return $this->call('getStickerSet', ['name' => $name]);
    }

    // ==================== Forum topics ====================

    public function createForumTopic(
        int|string $chatId,
        string $name,
        ?int $iconColor = null,
        ?string $iconCustomEmojiId = null,
    ): array {
        return $this->call('createForumTopic', [
            'chat_id'              => $chatId,
            'name'                 => $name,
            'icon_color'           => $iconColor,
            'icon_custom_emoji_id' => $iconCustomEmojiId,
        ]);
    }

    public function editForumTopic(
        int|string $chatId,
        int $messageThreadId,
        ?string $name = null,
        ?string $iconCustomEmojiId = null,
    ): bool {
        return (bool) $this->call('editForumTopic', [
            'chat_id'              => $chatId,
            'message_thread_id'    => $messageThreadId,
            'name'                 => $name,
            'icon_custom_emoji_id' => $iconCustomEmojiId,
        ]);
    }

    public function closeForumTopic(int|string $chatId, int $messageThreadId): bool
    {
        return (bool) $this->call('closeForumTopic', [
            'chat_id'           => $chatId,
            'message_thread_id' => $messageThreadId,
        ]);
    }

    public function reopenForumTopic(int|string $chatId, int $messageThreadId): bool
    {
        return (bool) $this->call('reopenForumTopic', [
            'chat_id'           => $chatId,
            'message_thread_id' => $messageThreadId,
        ]);
    }

    public function deleteForumTopic(int|string $chatId, int $messageThreadId): bool
    {
        return (bool) $this->call('deleteForumTopic', [
            'chat_id'           => $chatId,
            'message_thread_id' => $messageThreadId,
        ]);
    }

    // ==================== Chat action helpers ====================

    public function sendTyping(int|string $chatId, ?string $businessConnectionId = null): bool
    {
        return $this->sendChatAction($chatId, 'typing', $businessConnectionId);
    }

    public function sendUploadPhoto(int|string $chatId, ?string $businessConnectionId = null): bool
    {
        return $this->sendChatAction($chatId, 'upload_photo', $businessConnectionId);
    }

    public function sendUploadDocument(int|string $chatId, ?string $businessConnectionId = null): bool
    {
        return $this->sendChatAction($chatId, 'upload_document', $businessConnectionId);
    }

    public function sendUploadVideo(int|string $chatId, ?string $businessConnectionId = null): bool
    {
        return $this->sendChatAction($chatId, 'upload_video', $businessConnectionId);
    }

    public function sendRecordVoice(int|string $chatId, ?string $businessConnectionId = null): bool
    {
        return $this->sendChatAction($chatId, 'record_voice', $businessConnectionId);
    }

    public function sendChooseSticker(int|string $chatId, ?string $businessConnectionId = null): bool
    {
        return $this->sendChatAction($chatId, 'choose_sticker', $businessConnectionId);
    }

    // ==================== Private helpers ====================

    private function hasFile(array $params): bool
    {
        foreach ($params as $value) {
            if ($value instanceof \CURLFile) return true;
        }
        return false;
    }

    private function toMultipart(array $params): array
    {
        $multipart = [];
        foreach ($params as $key => $value) {
            if ($value === null) continue;
            if ($value instanceof \CURLFile) {
                $multipart[] = [
                    'name'     => $key,
                    'contents' => fopen($value->getFilename(), 'r'),
                    'filename' => $value->getPostFilename() ?: basename($value->getFilename()),
                ];
            } else {
                $multipart[] = [
                    'name'     => $key,
                    'contents' => is_array($value) ? json_encode($value) : (string) $value,
                ];
            }
        }
        return $multipart;
    }
}
