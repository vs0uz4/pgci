<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Support\Facades\DB;

class CustomSql
{
    public static function like(): string
    {
        if (DB::getDriverName() == 'pgsql') {
            return 'ILIKE';
        }

        return 'LIKE';
    }
}
