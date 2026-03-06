<?php

namespace App\Enums;

enum StrategyCategory: string
{
    case FINANCIAL = 'financial';
    case MARKETING = 'marketing';
    case OPERATIONS = 'operations';
    case GROWTH = 'growth';
}
