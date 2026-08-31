<?php

namespace Miraliog\Pelegram;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Miraliog\Pelegram\Exceptions\pelegramException;
use Miraliog\Pelegram\Types\Contracts\Keyboardable;

class Bot
{
    private Client $http;
    private string $baseUrl;

    public function __construct(private readonly string $token)
    {
        $this->baseUrl = "https://api.telegram.org/bot{$token}/";
        $this->http    = new Client(['timeout' => 30]);
    }

    // ==================== Core ====================

    public function call(string $method, array $params = []): array
    {
        try {
            $hasFile   = $this->hasFile($params);
            $options   = $hasFile
                ? ['multipart' => $this->toMultipart($params)]
                : ['json' => array_filter($params, fn($v) => $v !== null)];

            $response  = $this->http->post($this->baseUrl . $method, $options);
            $result    = json_decode($response->getBody()->getContents(), true);

            if (!($result['ok'] ?? false)) {
                throw new pelegramException(
                    $result['description'] ?? 'Unknown error',
                    $result['error_code']  ?? 0,
                    $result['description'] ?? null,
                );
            }

            return $result['result'] ?? [];
        } catch (GuzzleException $e) {
            throw new pelegramException("HTTP error: {$e->getMessage()}", $e->getCode(), null, $e);
        }
    }

    // ==================== Getting updates ====================

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
            'allowed_updates'      => $allowedUpdates,
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
        int|string $chatId,
        string $text,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
        bool $disableWebPagePreview = false,
        ?int $replyToMessageId = null,
        bool $protectContent = false,
        ?int $messageThreadId = null,
    ): array {
        return $this->call('sendMessage', [
            'chat_id'                  => $chatId,
            'text'                     => $text,
            'parse_mode'               => $parseMode,
            'reply_markup'             => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
            'disable_web_page_preview' => $disableWebPagePreview ?: null,
            'reply_to_message_id'      => $replyToMessageId,
            'protect_content'          => $protectContent ?: null,
            'message_thread_id'        => $messageThreadId,
        ]);
    }

    public function sendPhoto(
        int|string $chatId,
        string|\CURLFile $photo,
        ?string $caption = null,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
        ?int $replyToMessageId = null,
        ?int $messageThreadId = null,
    ): array {
        return $this->call('sendPhoto', [
            'chat_id'             => $chatId,
            'photo'               => $photo,
            'caption'             => $caption,
            'parse_mode'          => $parseMode,
            'reply_markup'        => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
            'reply_to_message_id' => $replyToMessageId,
            'message_thread_id'   => $messageThreadId,
        ]);
    }

    public function sendVideo(
        int|string $chatId,
        string|\CURLFile $video,
        ?string $caption = null,
        ?int $duration = null,
        ?int $width = null,
        ?int $height = null,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
        ?int $replyToMessageId = null,
    ): array {
        return $this->call('sendVideo', [
            'chat_id'             => $chatId,
            'video'               => $video,
            'caption'             => $caption,
            'duration'            => $duration,
            'width'               => $width,
            'height'              => $height,
            'parse_mode'          => $parseMode,
            'reply_markup'        => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
            'reply_to_message_id' => $replyToMessageId,
        ]);
    }

    public function sendAudio(
        int|string $chatId,
        string|\CURLFile $audio,
        ?string $caption = null,
        ?string $title = null,
        ?string $performer = null,
        ?int $duration = null,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
    ): array {
        return $this->call('sendAudio', [
            'chat_id'      => $chatId,
            'audio'        => $audio,
            'caption'      => $caption,
            'title'        => $title,
            'performer'    => $performer,
            'duration'     => $duration,
            'parse_mode'   => $parseMode,
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function sendDocument(
        int|string $chatId,
        string|\CURLFile $document,
        ?string $caption = null,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
        ?int $replyToMessageId = null,
    ): array {
        return $this->call('sendDocument', [
            'chat_id'             => $chatId,
            'document'            => $document,
            'caption'             => $caption,
            'parse_mode'          => $parseMode,
            'reply_markup'        => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
            'reply_to_message_id' => $replyToMessageId,
        ]);
    }

    public function sendVoice(
        int|string $chatId,
        string|\CURLFile $voice,
        ?string $caption = null,
        ?int $duration = null,
        Keyboardable|array|null $replyMarkup = null,
    ): array {
        return $this->call('sendVoice', [
            'chat_id'      => $chatId,
            'voice'        => $voice,
            'caption'      => $caption,
            'duration'     => $duration,
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function sendVideoNote(
        int|string $chatId,
        string|\CURLFile $videoNote,
        ?int $duration = null,
        ?int $length = null,
        Keyboardable|array|null $replyMarkup = null,
    ): array {
        return $this->call('sendVideoNote', [
            'chat_id'    => $chatId,
            'video_note' => $videoNote,
            'duration'   => $duration,
            'length'     => $length,
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function sendSticker(
        int|string $chatId,
        string|\CURLFile $sticker,
        Keyboardable|array|null $replyMarkup = null,
        ?int $replyToMessageId = null,
    ): array {
        return $this->call('sendSticker', [
            'chat_id'             => $chatId,
            'sticker'             => $sticker,
            'reply_markup'        => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
            'reply_to_message_id' => $replyToMessageId,
        ]);
    }

    public function sendAnimation(
        int|string $chatId,
        string|\CURLFile $animation,
        ?string $caption = null,
        ?int $duration = null,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
    ): array {
        return $this->call('sendAnimation', [
            'chat_id'      => $chatId,
            'animation'    => $animation,
            'caption'      => $caption,
            'duration'     => $duration,
            'parse_mode'   => $parseMode,
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function sendLocation(
        int|string $chatId,
        float $latitude,
        float $longitude,
        ?int $livePeriod = null,
        Keyboardable|array|null $replyMarkup = null,
    ): array {
        return $this->call('sendLocation', [
            'chat_id'      => $chatId,
            'latitude'     => $latitude,
            'longitude'    => $longitude,
            'live_period'  => $livePeriod,
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function sendContact(
        int|string $chatId,
        string $phoneNumber,
        string $firstName,
        ?string $lastName = null,
        Keyboardable|array|null $replyMarkup = null,
    ): array {
        return $this->call('sendContact', [
            'chat_id'      => $chatId,
            'phone_number' => $phoneNumber,
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function sendDice(
        int|string $chatId,
        string $emoji = '🎲',
        ?int $replyToMessageId = null,
    ): array {
        return $this->call('sendDice', [
            'chat_id'             => $chatId,
            'emoji'               => $emoji,
            'reply_to_message_id' => $replyToMessageId,
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
        Keyboardable|array|null $replyMarkup = null,
    ): array {
        return $this->call('sendPoll', [
            'chat_id'                 => $chatId,
            'question'                => $question,
            'options'                 => json_encode($options),
            'is_anonymous'            => $isAnonymous,
            'type'                    => $type,
            'allows_multiple_answers' => $allowsMultipleAnswers ?: null,
            'correct_option_id'       => $correctOptionId,
            'explanation'             => $explanation,
            'open_period'             => $openPeriod,
            'reply_markup'            => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function sendMediaGroup(int|string $chatId, array $media, ?int $replyToMessageId = null): array
    {
        return $this->call('sendMediaGroup', [
            'chat_id'             => $chatId,
            'media'               => json_encode($media),
            'reply_to_message_id' => $replyToMessageId,
        ]);
    }

    public function sendChatAction(int|string $chatId, string $action): bool
    {
        return (bool) $this->call('sendChatAction', [
            'chat_id' => $chatId,
            'action'  => $action,
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
        Keyboardable|array|null $replyMarkup = null,
        ?int $replyToMessageId = null,
    ): array {
        return $this->call('sendInvoice', [
            'chat_id'             => $chatId,
            'title'               => $title,
            'description'         => $description,
            'payload'             => $payload,
            'provider_token'      => $providerToken,
            'currency'            => $currency,
            'prices'              => json_encode($prices),
            'reply_markup'        => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
            'reply_to_message_id' => $replyToMessageId,
        ]);
    }

    // ==================== Editing messages ====================

    public function editMessageText(
        int|string $chatId,
        int $messageId,
        string $text,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
        bool $disableWebPagePreview = false,
    ): array {
        return $this->call('editMessageText', [
            'chat_id'                  => $chatId,
            'message_id'               => $messageId,
            'text'                     => $text,
            'parse_mode'               => $parseMode,
            'reply_markup'             => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
            'disable_web_page_preview' => $disableWebPagePreview ?: null,
        ]);
    }

    public function editMessageCaption(
        int|string $chatId,
        int $messageId,
        string $caption,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
    ): array {
        return $this->call('editMessageCaption', [
            'chat_id'      => $chatId,
            'message_id'   => $messageId,
            'caption'      => $caption,
            'parse_mode'   => $parseMode,
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function editMessageReplyMarkup(
        int|string $chatId,
        int $messageId,
        Keyboardable|array|null $replyMarkup = null,
    ): array {
        return $this->call('editMessageReplyMarkup', [
            'chat_id'      => $chatId,
            'message_id'   => $messageId,
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function editMessageMedia(
        int|string $chatId,
        int $messageId,
        array $media,
        Keyboardable|array|null $replyMarkup = null,
    ): array {
        return $this->call('editMessageMedia', [
            'chat_id'      => $chatId,
            'message_id'   => $messageId,
            'media'        => json_encode($media),
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
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
        bool $protectContent = false,
    ): array {
        return $this->call('forwardMessage', [
            'chat_id'         => $chatId,
            'from_chat_id'    => $fromChatId,
            'message_id'      => $messageId,
            'protect_content' => $protectContent ?: null,
        ]);
    }

    public function copyMessage(
        int|string $chatId,
        int|string $fromChatId,
        int $messageId,
        ?string $caption = null,
        Keyboardable|array|null $replyMarkup = null,
        string $parseMode = 'HTML',
    ): array {
        return $this->call('copyMessage', [
            'chat_id'      => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id'   => $messageId,
            'caption'      => $caption,
            'parse_mode'   => $parseMode,
            'reply_markup' => $replyMarkup instanceof Keyboardable
                ? json_encode($replyMarkup->toArray())
                : ($replyMarkup ? json_encode($replyMarkup) : null),
        ]);
    }

    public function pinChatMessage(int|string $chatId, int $messageId, bool $disableNotification = false): bool
    {
        return (bool) $this->call('pinChatMessage', [
            'chat_id'              => $chatId,
            'message_id'           => $messageId,
            'disable_notification' => $disableNotification ?: null,
        ]);
    }

    public function unpinChatMessage(int|string $chatId, ?int $messageId = null): bool
    {
        return (bool) $this->call('unpinChatMessage', [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
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
    ): bool {
        return (bool) $this->call('answerInlineQuery', [
            'inline_query_id' => $inlineQueryId,
            'results'         => json_encode($results),
            'cache_time'      => $cacheTime,
            'is_personal'     => $isPersonal ?: null,
            'next_offset'     => $nextOffset,
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
        return $this->call('getChatMember', [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    public function getChatMemberCount(int|string $chatId): int
    {
        return (int) $this->call('getChatMemberCount', ['chat_id' => $chatId]);
    }

    public function getChatAdministrators(int|string $chatId): array
    {
        return $this->call('getChatAdministrators', ['chat_id' => $chatId]);
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

    public function restrictChatMember(int|string $chatId, int $userId, array $permissions, ?int $untilDate = null): bool
    {
        return (bool) $this->call('restrictChatMember', [
            'chat_id'     => $chatId,
            'user_id'     => $userId,
            'permissions' => json_encode($permissions),
            'until_date'  => $untilDate,
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
        return (bool) $this->call('approveChatJoinRequest', [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    public function declineChatJoinRequest(int|string $chatId, int $userId): bool
    {
        return (bool) $this->call('declineChatJoinRequest', [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    public function setChatTitle(int|string $chatId, string $title): bool
    {
        return (bool) $this->call('setChatTitle', ['chat_id' => $chatId, 'title' => $title]);
    }

    public function setChatDescription(int|string $chatId, ?string $description): bool
    {
        return (bool) $this->call('setChatDescription', [
            'chat_id'     => $chatId,
            'description' => $description,
        ]);
    }

    public function leaveChat(int|string $chatId): bool
    {
        return (bool) $this->call('leaveChat', ['chat_id' => $chatId]);
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

    // ==================== User profile ====================

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

    public function createForumTopic(int|string $chatId, string $name, ?int $iconColor = null): array
    {
        return $this->call('createForumTopic', [
            'chat_id'    => $chatId,
            'name'       => $name,
            'icon_color' => $iconColor,
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

    // ==================== Typing action helpers ====================

    public function sendTyping(int|string $chatId): bool
    {
        return $this->sendChatAction($chatId, 'typing');
    }

    public function sendUploadPhoto(int|string $chatId): bool
    {
        return $this->sendChatAction($chatId, 'upload_photo');
    }

    public function sendUploadDocument(int|string $chatId): bool
    {
        return $this->sendChatAction($chatId, 'upload_document');
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
