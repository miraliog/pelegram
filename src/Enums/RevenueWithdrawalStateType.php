<?php

namespace Miraliog\Pelegram\Enums;

enum RevenueWithdrawalStateType: string
{
    case PENDING = 'pending';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
}
