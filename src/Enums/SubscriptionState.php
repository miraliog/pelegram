<?php

namespace Miraliog\Pelegram\Enums;

enum SubscriptionState: string
{
    case CANCELED = 'canceled';
    case ACTIVE = 'active';
    case FAILED = 'failed';
}
