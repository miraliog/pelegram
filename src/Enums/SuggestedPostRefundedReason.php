<?php

namespace Miraliog\Pelegram\Enums;

enum SuggestedPostRefundedReason: string
{
    case POST_DELETED = 'post_deleted';
    case PAYMENT_REFUNDED = 'payment_refunded';
}
