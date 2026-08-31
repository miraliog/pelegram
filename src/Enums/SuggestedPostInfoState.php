<?php

namespace Miraliog\Pelegram\Enums;

enum SuggestedPostInfoState: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DECLINED = 'declined';
}
