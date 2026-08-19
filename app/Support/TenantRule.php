<?php

namespace App\Support;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

final class TenantRule
{
    public static function exists(string $table, string $column = 'id'): Exists
    {
        $rule = Rule::exists($table, $column);
        $tenantId = auth()->user()?->tenant_id;

        if ($tenantId !== null) {
            $rule->where('tenant_id', $tenantId);
        }

        return $rule;
    }
}
