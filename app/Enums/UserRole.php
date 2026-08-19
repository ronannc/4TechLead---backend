<?php

namespace App\Enums;

enum UserRole: string
{
    case TechLead = 'tech_lead';
    case Member = 'member';
}
