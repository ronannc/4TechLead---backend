<?php

namespace App\Enums;

enum ContractType: string
{
    case Clt = 'clt';
    case Pj = 'pj';
    case Hourly = 'horista';
    case Cooperative = 'cooperado';
}
