<?php

namespace App\Enums;

enum DailyEntryStatus: string
{
    case OnTime = 'no_tempo';
    case Burned = 'queimado';
    case SpokeTooLittle = 'pouco_tempo';
}
