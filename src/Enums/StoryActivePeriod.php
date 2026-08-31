<?php

namespace Miraliog\Pelegram\Enums;

enum StoryActivePeriod: int
{
    case SIX_HOURS = 21600;
    case TWELVE_HOURS = 43200;
    case ONE_DAY = 86400;
    case TWO_DAYS = 172800;
}
