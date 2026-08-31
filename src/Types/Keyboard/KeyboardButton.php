<?php

namespace Miraliog\Pelegram\Types\Keyboard;

/**
 * KeyboardButton
 * Source: https://core.telegram.org/bots/api#keyboardbutton
 */
class KeyboardButton
{
    private bool    $requestContact  = false;
    private bool    $requestLocation = false;
    private ?array  $requestPoll     = null;
    private ?string $webAppUrl       = null;
    private ?array  $requestUsers    = null;
    private ?array  $requestChat     = null;

    private function __construct(private readonly string $text) {}

    public static function make(string $text): static
    {
        return new static($text);
    }

    // ==================== Action (pick one) ====================

    public function requestContact(): static
    {
        $this->requestContact = true;
        return $this;
    }

    public function requestLocation(): static
    {
        $this->requestLocation = true;
        return $this;
    }

    /** @param string|null $type 'quiz' یا 'regular' — null برای هر دو */
    public function requestPoll(?string $type = null): static
    {
        $this->requestPoll = $type !== null ? ['type' => $type] : [];
        return $this;
    }

    public function webApp(string $url): static
    {
        $this->webAppUrl = $url;
        return $this;
    }

    public function requestUsers(
        int $requestId,
        ?bool $userIsBot = null,
        ?bool $userIsPremium = null,
        int $maxQuantity = 1,
        bool $requestName = false,
        bool $requestUsername = false,
        bool $requestPhoto = false,
    ): static {
        $this->requestUsers = array_filter([
            'request_id'       => $requestId,
            'user_is_bot'      => $userIsBot,
            'user_is_premium'  => $userIsPremium,
            'max_quantity'     => $maxQuantity > 1 ? $maxQuantity : null,
            'request_name'     => $requestName     ?: null,
            'request_username' => $requestUsername  ?: null,
            'request_photo'    => $requestPhoto     ?: null,
        ], fn($v) => $v !== null);
        $this->requestUsers['request_id'] = $requestId;
        return $this;
    }

    public function requestChat(
        int $requestId,
        bool $chatIsChannel = false,
        ?bool $chatIsForum = null,
        ?bool $chatHasUsername = null,
        ?bool $chatIsCreated = null,
        bool $requestTitle = false,
        bool $requestUsername = false,
        bool $requestPhoto = false,
        ?array $userAdminRights = null,
        ?array $botAdminRights = null,
        ?bool $botIsMember = null,
    ): static {
        $this->requestChat = array_filter([
            'request_id'        => $requestId,
            'chat_is_channel'   => $chatIsChannel  ?: null,
            'chat_is_forum'     => $chatIsForum,
            'chat_has_username' => $chatHasUsername,
            'chat_is_created'   => $chatIsCreated,
            'request_title'     => $requestTitle    ?: null,
            'request_username'  => $requestUsername  ?: null,
            'request_photo'     => $requestPhoto     ?: null,
            'user_admin_rights' => $userAdminRights,
            'bot_admin_rights'  => $botAdminRights,
            'bot_is_member'     => $botIsMember,
        ], fn($v) => $v !== null);
        $this->requestChat['request_id'] = $requestId;
        return $this;
    }

    // ==================== Serialize ====================

    public function toArray(): array
    {
        $button = ['text' => $this->text];

        if ($this->requestContact)            $button['request_contact']  = true;
        elseif ($this->requestLocation)       $button['request_location'] = true;
        elseif ($this->requestPoll !== null)  $button['request_poll']     = $this->requestPoll ?: new \stdClass();
        elseif ($this->webAppUrl !== null)    $button['web_app']          = ['url' => $this->webAppUrl];
        elseif ($this->requestUsers !== null) $button['request_users']    = $this->requestUsers;
        elseif ($this->requestChat !== null)  $button['request_chat']     = $this->requestChat;

        return $button;
    }
}
