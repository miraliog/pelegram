<?php

namespace Miraliog\Pelegram;

use Miraliog\Pelegram\Concerns\Update\DetectsUpdateType;
use Miraliog\Pelegram\Concerns\Update\DetectsMessageType;
use Miraliog\Pelegram\Concerns\Update\DetectsChatType;
use Miraliog\Pelegram\Concerns\Accessors\UserAccessor;
use Miraliog\Pelegram\Concerns\Accessors\ChatAccessor;
use Miraliog\Pelegram\Concerns\Accessors\MessageAccessor;
use Miraliog\Pelegram\Concerns\Accessors\MediaAccessor;
use Miraliog\Pelegram\Concerns\Accessors\CommandAccessor;
use Miraliog\Pelegram\Concerns\Accessors\CallbackAccessor;
use Miraliog\Pelegram\Concerns\Accessors\InlineQueryAccessor;
use Miraliog\Pelegram\Concerns\Accessors\PaymentAccessor;
use Miraliog\Pelegram\Concerns\Accessors\LocationAccessor;
use Miraliog\Pelegram\Concerns\Accessors\ContactAccessor;
use Miraliog\Pelegram\Concerns\Accessors\ChatMemberAccessor;
use Miraliog\Pelegram\Concerns\Actions\Answerable;
use Miraliog\Pelegram\Concerns\Actions\Replyable;

class Update
{
    use DetectsUpdateType, DetectsMessageType, DetectsChatType;
    use UserAccessor, ChatAccessor, MessageAccessor, MediaAccessor;
    use CommandAccessor, CallbackAccessor, InlineQueryAccessor;
    use PaymentAccessor, LocationAccessor, ContactAccessor, ChatMemberAccessor;
    use Answerable, Replyable;

    private ?Bot $bot = null;

    public function __construct(public readonly array $raw) {}

    public static function fromWebhook(): ?static
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) return null;
        return new static($data);
    }

    public function setBot(Bot $bot): void
    {
        $this->bot = $bot;
    }
}
