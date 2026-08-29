<?php

namespace miraliog\pelegram;

use miraliog\pelegram\Concerns\Update\DetectsUpdateType;
use miraliog\pelegram\Concerns\Update\DetectsMessageType;
use miraliog\pelegram\Concerns\Update\DetectsChatType;
use miraliog\pelegram\Concerns\Accessors\UserAccessor;
use miraliog\pelegram\Concerns\Accessors\ChatAccessor;
use miraliog\pelegram\Concerns\Accessors\MessageAccessor;
use miraliog\pelegram\Concerns\Accessors\MediaAccessor;
use miraliog\pelegram\Concerns\Accessors\CommandAccessor;
use miraliog\pelegram\Concerns\Accessors\CallbackAccessor;
use miraliog\pelegram\Concerns\Accessors\InlineQueryAccessor;
use miraliog\pelegram\Concerns\Accessors\PaymentAccessor;
use miraliog\pelegram\Concerns\Accessors\LocationAccessor;
use miraliog\pelegram\Concerns\Accessors\ContactAccessor;
use miraliog\pelegram\Concerns\Accessors\ChatMemberAccessor;
use miraliog\pelegram\Concerns\Actions\Answerable;
use miraliog\pelegram\Concerns\Actions\Replyable;

class Update
{
    use DetectsUpdateType, DetectsMessageType, DetectsChatType;
    use UserAccessor, ChatAccessor, MessageAccessor, MediaAccessor;
    use CommandAccessor, CallbackAccessor, InlineQueryAccessor;
    use PaymentAccessor, LocationAccessor, ContactAccessor, ChatMemberAccessor;
    use Answerable, Replyable;

    private ?Bot $bot = null;

    public function __construct(public readonly array $raw) {}

    public function setBot(Bot $bot): void
    {
        $this->bot = $bot;
    }
}
