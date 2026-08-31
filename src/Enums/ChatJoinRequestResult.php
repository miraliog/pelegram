<?php

namespace Miraliog\Pelegram\Enums;

enum ChatJoinRequestResult: string
{
    case APPROVE = 'approve';
    case DECLINE = 'decline';
    case QUEUE = 'queue';
}
