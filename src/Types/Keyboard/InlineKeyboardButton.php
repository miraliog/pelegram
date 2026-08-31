<?php

namespace Miraliog\Pelegram\Types\Keyboard;

use Miraliog\Pelegram\Enums\ButtonStyle;

/**
 * InlineKeyboardButton
 * Source: https://core.telegram.org/bots/api#inlinekeyboardbutton
 *
 * Exactly one of the optional fields (other than text, icon_custom_emoji_id, style) must be set.
 */
class InlineKeyboardButton
{
    private ?string      $callbackData                  = null;
    private ?string      $url                           = null;
    private ?string      $webAppUrl                     = null;
    private ?array       $loginUrl                      = null;
    private ?string      $switchInlineQuery             = null;
    private ?string      $switchInlineQueryCurrentChat  = null;
    private ?array       $switchInlineQueryChosenChat   = null;
    private ?CopyTextButton $copyText                   = null;
    private ?bool        $pay                           = null;
    private ?array       $disabled                      = null;
    private ?string      $iconCustomEmojiId             = null;
    private ?ButtonStyle $style                         = null;

    private function __construct(private readonly string $text) {}

    public static function make(string $text): static
    {
        return new static($text);
    }

    // ==================== Action (pick one) ====================

    public function callbackData(string $data): static
    {
        $this->callbackData = $data;
        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function webApp(string $url): static
    {
        $this->webAppUrl = $url;
        return $this;
    }

    public function loginUrl(
        string $url,
        ?string $forwardText = null,
        ?string $botUsername = null,
        bool $requestWriteAccess = false,
    ): static {
        $this->loginUrl = array_filter([
            'url'                  => $url,
            'forward_text'         => $forwardText,
            'bot_username'         => $botUsername,
            'request_write_access' => $requestWriteAccess ?: null,
        ]);
        return $this;
    }

    public function switchInlineQuery(string $query = ''): static
    {
        $this->switchInlineQuery = $query;
        return $this;
    }

    public function switchInlineQueryCurrentChat(string $query = ''): static
    {
        $this->switchInlineQueryCurrentChat = $query;
        return $this;
    }

    public function switchInlineQueryChosenChat(
        string $query = '',
        ?bool $allowUserChats = null,
        ?bool $allowBotChats = null,
        ?bool $allowGroupChats = null,
        ?bool $allowChannelChats = null,
    ): static {
        $this->switchInlineQueryChosenChat = array_filter([
            'query'               => $query,
            'allow_user_chats'    => $allowUserChats,
            'allow_bot_chats'     => $allowBotChats,
            'allow_group_chats'   => $allowGroupChats,
            'allow_channel_chats' => $allowChannelChats,
        ], fn($v) => $v !== null);
        return $this;
    }

    public function copyText(CopyTextButton|string $text): static
    {
        $this->copyText = $text instanceof CopyTextButton ? $text : CopyTextButton::make($text);
        return $this;
    }

    public function pay(): static
    {
        $this->pay = true;
        return $this;
    }

    public function disabled(?string $tooltip = null): static
    {
        $this->disabled = $tooltip !== null ? ['text' => $tooltip] : [];
        return $this;
    }

    // ==================== Optional decorators ====================

    public function iconCustomEmojiId(string $customEmojiId): static
    {
        $this->iconCustomEmojiId = $customEmojiId;
        return $this;
    }

    public function style(ButtonStyle $style): static
    {
        $this->style = $style;
        return $this;
    }

    // ==================== Serialize ====================

    public function toArray(): array
    {
        $button = ['text' => $this->text];

        if ($this->iconCustomEmojiId !== null) $button['icon_custom_emoji_id'] = $this->iconCustomEmojiId;
        if ($this->style !== null)             $button['style']                = $this->style->value;

        if ($this->disabled !== null) {
            $button['disabled'] = $this->disabled ?: new \stdClass();
            return $button;
        }

        if ($this->callbackData !== null)                     $button['callback_data']                     = $this->callbackData;
        elseif ($this->url !== null)                          $button['url']                               = $this->url;
        elseif ($this->webAppUrl !== null)                    $button['web_app']                           = ['url' => $this->webAppUrl];
        elseif ($this->loginUrl !== null)                     $button['login_url']                         = $this->loginUrl;
        elseif ($this->switchInlineQuery !== null)            $button['switch_inline_query']               = $this->switchInlineQuery;
        elseif ($this->switchInlineQueryCurrentChat !== null) $button['switch_inline_query_current_chat']  = $this->switchInlineQueryCurrentChat;
        elseif ($this->switchInlineQueryChosenChat !== null)  $button['switch_inline_query_chosen_chat']   = $this->switchInlineQueryChosenChat;
        elseif ($this->copyText !== null)                     $button['copy_text']                         = $this->copyText->toArray();
        elseif ($this->pay !== null)                          $button['pay']                               = true;

        return $button;
    }
}
