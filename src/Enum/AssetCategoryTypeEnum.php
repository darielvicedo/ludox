<?php

namespace App\Enum;

enum AssetCategoryTypeEnum
{
    public const CATEGORY_GAMES = 0;
    public const CATEGORY_ACCESSORY = 1;
    public const CATEGORY_TOOLS = 2;

    private const TYPE_NAMES = [
        self::CATEGORY_GAMES => "Juegos",
        self::CATEGORY_ACCESSORY => "Accesorios",
        self::CATEGORY_TOOLS => "Útiles y herramientas",
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
