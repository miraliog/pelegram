<?php

namespace Miraliog\Pelegram\Concerns\Update;

trait DetectsUpdateType
{
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
}
