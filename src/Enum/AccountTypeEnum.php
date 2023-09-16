<?php

namespace App\Enum;

enum AccountTypeEnum
{
    public const ACCOUNT_DEBIT = 0;
    public const ACCOUNT_CREDIT = 1;

    private const TYPE_NAMES = [
        self::ACCOUNT_DEBIT => 'Deudora',
        self::ACCOUNT_CREDIT => 'Acreedora',
    ];

    /**
     * @param int $type
     * @return string
     * @throws \Exception
     */
    public static function getTypeName(int $type): string
    {
        if (!isset(self::TYPE_NAMES[$type])) {
            throw new \Exception("Unknown type code $type");
        }

        return self::TYPE_NAMES[$type];
    }

    /**
     * @param int $code
     * @return bool
     */
    public static function isTypeCode(int $code): bool
    {
        return array_key_exists($code, self::TYPE_NAMES);
    }

    public static function getTypesArray(): array
    {
        return array_flip(self::TYPE_NAMES);
    }
}
