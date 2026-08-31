<?php

namespace Miraliog\Pelegram\Enums;

enum MessageOriginType: string
{
    case USER = 'user';
    case HIDDEN_USER = 'hidden_user';
    case CHAT = 'chat';
    case CHANNEL = 'channel';
}
