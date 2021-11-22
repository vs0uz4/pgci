<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Support\Facades\{DB, Schema};

class SqlSupport
{
    /**
     * @var array|string[]
     */
    private static array $sortStringNumberTypes = ['string', 'varchar', 'char'];

    /**
     * @return string
     */
    public static function like(): string
    {
        if (DB::getDriverName() == 'pgsql') {
            return 'ILIKE';
        }

        return 'LIKE';
    }

    /**
     * @param string $sortField
     * @return string
     */
    public static function sortStringAsNumber(string $sortField): string
    {
        $driverName = DB::getDriverName();

        return self::getSortSqlByDriver($driverName, $sortField);
    }

    /**
     * @param string $driverName
     * @param string $sortField
     * @return string
     */
    private static function getSortSqlByDriver(string $driverName, string $sortField): string
    {
        $sqlByDriver = [
            'sqlite' => "$sortField+0",
            'mysql' => "CAST(NULLIF(REGEXP_REPLACE($sortField, '[[:alpha:]]+', ''), '') AS SIGNED INTEGER)",
            'pgsql' => "CAST(NULLIF(REGEXP_REPLACE($sortField, '\D', '', 'g'), '') AS INTEGER)",
            'sqlsrv' => "$sortField+0",
        ];

        return $sqlByDriver[$driverName] ?? $sortField;
    }

    /**
     * @param string $sortFieldType
     * @return bool
     */
    public static function isValidSortFieldType(string $sortFieldType): bool
    {
        return in_array($sortFieldType, self::$sortStringNumberTypes);
    }

    /**
     * @param string $sortField
     * @return string
     */
    public static function getSortFieldType(string $sortField): string
    {
        $data = explode('.', $sortField);

        return Schema::getConnection()
            ->getDoctrineColumn($data[0], $data[1])
            ->getType()
            ->getName();
    }
}
